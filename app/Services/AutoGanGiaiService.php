<?php

namespace App\Services;

use App\Models\KetQuaThi;
use App\Models\CoCauGiaiThuong;
use App\Models\GanGiaiThuong;
use App\Models\BaiThi;
use App\Models\CuocThi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoGanGiaiService
{
    /**
     * Tự động gán giải thưởng sau khi chấm điểm
     * Được gọi sau mỗi lần lưu/cập nhật điểm
     */
    public function capNhatXepHangVaGanGiai($macuocthi)
    {
        try {
            DB::beginTransaction();
            
            // Bước 1: Cập nhật xếp hạng cho tất cả bài thi
            $this->capNhatXepHang($macuocthi);
            
            // Bước 2: Xóa các giải thưởng tự động cũ (trạng thái Pending)
            $this->xoaGanGiaiTuDong($macuocthi);
            
            // Bước 3: Tự động gán giải mới dựa trên xếp hạng
            $result = $this->autoGanGiai($macuocthi);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Đã cập nhật xếp hạng và gán giải thưởng thành công',
                'data' => $result
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật xếp hạng và gán giải: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật xếp hạng cho tất cả bài thi trong cuộc thi
     * Xếp hạng dựa trên điểm cao → thấp
     */
    public function capNhatXepHang($macuocthi)
    {
        // Lấy tất cả kết quả có điểm, sắp xếp theo điểm giảm dần
        $ketQuaList = KetQuaThi::whereHas('baithi.dethi', function($q) use ($macuocthi) {
                $q->where('macuocthi', $macuocthi);
            })
            ->whereNotNull('diem')
            ->orderBy('diem', 'desc')
            ->get();
        
        // Cập nhật xếp hạng
        $xephang = 1;
        $diemTruoc = null;
        $viTriThucTe = 1;
        
        foreach ($ketQuaList as $ketqua) {
            // Nếu điểm khác điểm trước, cập nhật xếp hạng
            if ($diemTruoc !== null && $ketqua->diem < $diemTruoc) {
                $xephang = $viTriThucTe;
            }
            
            $ketqua->xephang = $xephang;
            $ketqua->save();
            
            $diemTruoc = $ketqua->diem;
            $viTriThucTe++;
        }
        
        Log::info("Đã cập nhật xếp hạng cho {$ketQuaList->count()} bài thi trong cuộc thi {$macuocthi}");
        
        return $ketQuaList->count();
    }
    
    /**
     * Kiểm tra điều kiện có thể tự động gán giải không
     */
    public function kiemTraDieuKien($macuocthi)
    {
        // Kiểm tra có cơ cấu giải thưởng không
        $coCoCau = CoCauGiaiThuong::where('macuocthi', $macuocthi)
            ->where('trangthai', 'Active')
            ->exists();
        
        if (!$coCoCau) {
            return [
                'can_auto' => false,
                'reason' => 'Cuộc thi chưa có cơ cấu giải thưởng'
            ];
        }
        
        // Đếm số bài thi
        $tongBaiThi = BaiThi::whereHas('dethi', function($q) use ($macuocthi) {
            $q->where('macuocthi', $macuocthi);
        })->count();
        
        if ($tongBaiThi == 0) {
            return [
                'can_auto' => false,
                'reason' => 'Cuộc thi chưa có bài thi nào'
            ];
        }
        
        // Đếm số bài đã chấm
        $daCham = KetQuaThi::whereHas('baithi.dethi', function($q) use ($macuocthi) {
            $q->where('macuocthi', $macuocthi);
        })->whereNotNull('diem')->count();
        
        if ($daCham == 0) {
            return [
                'can_auto' => false,
                'reason' => 'Chưa có bài thi nào được chấm điểm'
            ];
        }
        
        return [
            'can_auto' => true,
            'tong_baithi' => $tongBaiThi,
            'da_cham' => $daCham,
            'chua_cham' => $tongBaiThi - $daCham,
            'ty_le_hoan_thanh' => round(($daCham / $tongBaiThi) * 100, 2)
        ];
    }
    
    /**
     * Tự động gán giải thưởng dựa trên xếp hạng
     */
    public function autoGanGiai($macuocthi)
    {
        try {
            // Lấy danh sách cơ cấu giải thưởng (sắp xếp theo thứ tự ưu tiên)
            $cocauList = CoCauGiaiThuong::where('macuocthi', $macuocthi)
                ->where('trangthai', 'Active')
                ->orderByRaw("
                    CASE 
                        WHEN tengiai ILIKE '%nhất%' THEN 1
                        WHEN tengiai ILIKE '%nhì%' OR tengiai ILIKE '%hai%' THEN 2
                        WHEN tengiai ILIKE '%ba%' THEN 3
                        WHEN tengiai ILIKE '%khuyến khích%' THEN 4
                        WHEN tengiai ILIKE '%an ủi%' THEN 5
                        ELSE 6
                    END
                ")
                ->orderBy('tienthuong', 'desc')
                ->get();
            
            if ($cocauList->isEmpty()) {
                return [
                    'success' => false,
                    'errors' => ['Không có cơ cấu giải thưởng nào']
                ];
            }
            
            // Lấy danh sách kết quả có điểm, sắp xếp theo xếp hạng
            $ketQuaList = KetQuaThi::whereHas('baithi.dethi', function($q) use ($macuocthi) {
                    $q->where('macuocthi', $macuocthi);
                })
                ->whereNotNull('diem')
                ->whereNotNull('xephang')
                ->with(['baithi.dangkycanhan', 'baithi.dangkydoi'])
                ->orderBy('xephang')
                ->orderBy('diem', 'desc')
                ->get();
            
            if ($ketQuaList->isEmpty()) {
                return [
                    'success' => false,
                    'errors' => ['Không có kết quả thi nào để gán giải']
                ];
            }
            
            $totalAssigned = 0;
            $details = [];
            $errors = [];
            
            // Duyệt qua từng cơ cấu giải thưởng
            foreach ($cocauList as $cocau) {
                $soLuongGan = 0;
                $xepHangBatDau = $totalAssigned + 1; // Xếp hạng bắt đầu gán cho giải này
                
                // Lấy số lượng cần gán (hoặc không giới hạn nếu cho phép đồng hạng)
                $soLuongCanGan = $cocau->chophepdonghang ? PHP_INT_MAX : $cocau->soluong;
                
                // Duyệt qua danh sách kết quả
                foreach ($ketQuaList as $ketqua) {
                    // Nếu đã gán đủ số lượng cho giải này (và không cho phép đồng hạng)
                    if ($soLuongGan >= $soLuongCanGan) {
                        break;
                    }
                    
                    // Chỉ gán cho những người có xếp hạng phù hợp
                    if ($ketqua->xephang < $xepHangBatDau) {
                        continue; // Đã được gán giải cao hơn
                    }
                    
                    // Kiểm tra người này đã được gán giải chưa
                    $baithi = $ketqua->baithi;
                    $daCoGiai = GanGiaiThuong::where(function($q) use ($baithi) {
                        if ($baithi->madangkycanhan) {
                            $q->where('madangkycanhan', $baithi->madangkycanhan);
                        } else {
                            $q->where('madangkydoi', $baithi->madangkydoi);
                        }
                    })
                    ->whereHas('cocaugiaithuong', function($q) use ($macuocthi) {
                        $q->where('macuocthi', $macuocthi);
                    })
                    ->exists();
                    
                    if ($daCoGiai) {
                        continue; // Đã có giải rồi
                    }
                    
                    // Tạo bản ghi gán giải
                    $ganGiai = new GanGiaiThuong();
                    $ganGiai->magangiai = 'GG-' . strtoupper(uniqid());
                    $ganGiai->macocau = $cocau->macocau;
                    $ganGiai->madangkycanhan = $baithi->madangkycanhan;
                    $ganGiai->madangkydoi = $baithi->madangkydoi;
                    $ganGiai->loaidangky = $baithi->madangkycanhan ? 'CaNhan' : 'DoiNhom';
                    $ganGiai->ladongkang = false; // Mặc định không phải đồng hạng
                    $ganGiai->xephangthucte = $ketqua->xephang;
                    $ganGiai->trangthai = 'Pending'; // Chờ duyệt
                    $ganGiai->ghichu = 'Tự động gán dựa trên xếp hạng';
                    
                    $ganGiai->save();
                    
                    $soLuongGan++;
                    $totalAssigned++;
                }
                
                // Lưu thống kê
                if ($soLuongGan > 0) {
                    $details[$cocau->tengiai] = $soLuongGan;
                }
                
                // Nếu không gán được ai cho giải này
                if ($soLuongGan == 0 && !$cocau->chophepdonghang) {
                    $errors[] = "Giải '{$cocau->tengiai}': Không đủ thí sinh đủ điều kiện";
                }
            }
            
            return [
                'success' => true,
                'total_assigned' => $totalAssigned,
                'details' => $details,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            Log::error('Lỗi tự động gán giải: ' . $e->getMessage());
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }
    
    /**
     * Xóa các giải thưởng tự động (trạng thái Pending)
     */
    public function xoaGanGiaiTuDong($macuocthi)
    {
        $deleted = GanGiaiThuong::whereHas('cocaugiaithuong', function($q) use ($macuocthi) {
                $q->where('macuocthi', $macuocthi);
            })
            ->where('trangthai', 'Pending')
            ->whereNull('nguoiduyet') // Chưa có người duyệt
            ->delete();
        
        Log::info("Đã xóa {$deleted} giải thưởng tự động trong cuộc thi {$macuocthi}");
        
        return $deleted;
    }
    
    /**
     * Xử lý đồng hạng - gán cùng giải cho những người có cùng điểm
     */
    public function ganGiaiDongHang($macuocthi, $macocau)
    {
        try {
            DB::beginTransaction();
            
            $cocau = CoCauGiaiThuong::findOrFail($macocau);
            
            if (!$cocau->chophepdonghang) {
                return [
                    'success' => false,
                    'message' => 'Giải này không cho phép đồng hạng'
                ];
            }
            
            // Lấy danh sách đã gán giải này
            $danhSachDaGan = GanGiaiThuong::where('macocau', $macocau)
                ->with(['dangkycanhan', 'dangkydoi'])
                ->get();
            
            // Lấy điểm của người được gán (lấy điểm cao nhất trong số đã gán)
            $diemChuan = null;
            foreach ($danhSachDaGan as $gan) {
                $ketqua = KetQuaThi::whereHas('baithi', function($q) use ($gan) {
                    if ($gan->madangkycanhan) {
                        $q->where('madangkycanhan', $gan->madangkycanhan);
                    } else {
                        $q->where('madangkydoi', $gan->madangkydoi);
                    }
                })->first();
                
                if ($ketqua && ($diemChuan === null || $ketqua->diem > $diemChuan)) {
                    $diemChuan = $ketqua->diem;
                }
            }
            
            if ($diemChuan === null) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy điểm chuẩn'
                ];
            }
            
            // Tìm tất cả người có cùng điểm nhưng chưa được gán giải này
            $ketQuaCungDiem = KetQuaThi::whereHas('baithi.dethi', function($q) use ($macuocthi) {
                    $q->where('macuocthi', $macuocthi);
                })
                ->where('diem', $diemChuan)
                ->with('baithi')
                ->get();
            
            $soLuongGanThem = 0;
            
            foreach ($ketQuaCungDiem as $ketqua) {
                $baithi = $ketqua->baithi;
                
                // Kiểm tra đã gán giải này chưa
                $daGan = GanGiaiThuong::where('macocau', $macocau)
                    ->where(function($q) use ($baithi) {
                        if ($baithi->madangkycanhan) {
                            $q->where('madangkycanhan', $baithi->madangkycanhan);
                        } else {
                            $q->where('madangkydoi', $baithi->madangkydoi);
                        }
                    })
                    ->exists();
                
                if (!$daGan) {
                    // Gán giải đồng hạng
                    $ganGiai = new GanGiaiThuong();
                    $ganGiai->magangiai = 'GG-' . strtoupper(uniqid());
                    $ganGiai->macocau = $macocau;
                    $ganGiai->madangkycanhan = $baithi->madangkycanhan;
                    $ganGiai->madangkydoi = $baithi->madangkydoi;
                    $ganGiai->loaidangky = $baithi->madangkycanhan ? 'CaNhan' : 'DoiNhom';
                    $ganGiai->ladongkang = true; // Đánh dấu là đồng hạng
                    $ganGiai->xephangthucte = $ketqua->xephang;
                    $ganGiai->trangthai = 'Pending';
                    $ganGiai->ghichu = 'Tự động gán đồng hạng (cùng điểm: ' . $diemChuan . ')';
                    
                    $ganGiai->save();
                    $soLuongGanThem++;
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Đã gán thêm {$soLuongGanThem} giải đồng hạng",
                'so_luong' => $soLuongGanThem
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi gán giải đồng hạng: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }
}