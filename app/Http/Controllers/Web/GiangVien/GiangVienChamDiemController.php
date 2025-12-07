<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\KetQuaThi;
use App\Models\BaiThi;
use App\Models\PhanCongGiangVien;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class GiangVienChamDiemController extends Controller
{
    /**
     * Hiển thị danh sách CUỘC THI cần chấm (thay vì từng bài)
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Lấy danh sách Ban mà GV tham gia
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        // Lấy TOÀN BỘ cuộc thi của các Ban đó
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        // Query danh sách cuộc thi + thống kê số bài chấm/chưa chấm
        $query = DB::table('cuocthi as ct')
            ->whereIn('ct.macuocthi', $danhSachCuocThi)
            ->select(
                'ct.*',
                // Tổng bài thi đã nộp
                DB::raw('(SELECT COUNT(*) 
                         FROM baithi bt 
                         JOIN dethi dt ON bt.madethi = dt.madethi 
                         WHERE dt.macuocthi = ct.macuocthi) as tong_baithi'),
                
                // Số bài đã chấm (có điểm)
                DB::raw('(SELECT COUNT(DISTINCT bt.mabaithi) 
                         FROM baithi bt 
                         JOIN dethi dt ON bt.madethi = dt.madethi 
                         LEFT JOIN ketquathi kq ON bt.mabaithi = kq.mabaithi 
                         WHERE dt.macuocthi = ct.macuocthi 
                         AND kq.diem IS NOT NULL) as da_cham'),
                
                // Số bài chưa chấm (chưa có kết quả hoặc chưa có điểm)
                DB::raw('(SELECT COUNT(DISTINCT bt.mabaithi) 
                         FROM baithi bt 
                         JOIN dethi dt ON bt.madethi = dt.madethi 
                         LEFT JOIN ketquathi kq ON bt.mabaithi = kq.mabaithi 
                         WHERE dt.macuocthi = ct.macuocthi 
                         AND (kq.maketqua IS NULL OR kq.diem IS NULL)) as chua_cham')
            );

        // Tìm kiếm theo tên cuộc thi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ct.tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            $query->where('ct.trangthai', $request->trangthai);
        }
        
        $cuocthiList = $query->orderBy('ct.thoigianbatdau', 'desc')->paginate(10);
        
        // Debug: Log thống kê để kiểm tra
        if (config('app.debug')) {
            foreach ($cuocthiList as $ct) {
                Log::info("Cuộc thi: {$ct->tencuocthi}", [
                    'tong_baithi' => $ct->tong_baithi,
                    'da_cham' => $ct->da_cham,
                    'chua_cham' => $ct->chua_cham,
                    'check_sum' => ($ct->da_cham + $ct->chua_cham) == $ct->tong_baithi ? 'OK' : 'MISMATCH'
                ]);
            }
        }
        
        return view('giangvien.chamdiem.index', compact('cuocthiList', 'giangvien'));
    }

    /**
     * Chi tiết cuộc thi - Hiển thị danh sách bài thi
     */
    public function showCuocThi($macuocthi)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        if (!in_array($macuocthi, $danhSachCuocThi)) {
            return redirect()->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền chấm điểm cuộc thi này!');
        }
        
        // Lấy thông tin cuộc thi
        $cuocthi = DB::table('cuocthi')->where('macuocthi', $macuocthi)->first();
        
        if (!$cuocthi) {
            return redirect()->route('giangvien.chamdiem.index')
                ->with('error', 'Không tìm thấy cuộc thi!');
        }
        
        // Lấy danh sách bài thi của cuộc thi
        $baithiList = DB::table('baithi as bt')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dt.macuocthi', $macuocthi)
            ->select(
                'bt.*',
                'dt.tendethi',
                'kq.maketqua',
                'kq.diem',
                'kq.xephang',
                'kq.giaithuong',
                'kq.nhanxet',
                'sv.masinhvien',
                'nd.hoten as sinhvien_ten',
                'd.tendoithi',
                'd.madoithi'
            )
            ->orderByRaw('kq.xephang ASC NULLS LAST')
            ->orderBy('kq.diem', 'desc')
            ->orderBy('bt.thoigiannop', 'asc')
            ->paginate(15);
        
        return view('giangvien.chamdiem.show-cuocthi', compact('cuocthi', 'baithiList', 'giangvien'));
    }

    /**
     * Export template Excel để giảng viên điền điểm
     */
    public function exportTemplate($macuocthi)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        if (!in_array($macuocthi, $danhSachCuocThi)) {
            return back()->with('error', 'Bạn không có quyền xuất file cuộc thi này!');
        }
        
        $cuocthi = DB::table('cuocthi')->where('macuocthi', $macuocthi)->first();
        
        // Lấy danh sách bài thi
        $baithiList = DB::table('baithi as bt')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dt.macuocthi', $macuocthi)
            ->select(
                'bt.mabaithi',
                'bt.loaidangky',
                'dt.tendethi',
                'sv.masinhvien',
                'nd.hoten as sinhvien_ten',
                'd.madoithi',
                'd.tendoithi',
                'kq.diem',
                'kq.nhanxet'
            )
            ->orderBy('sv.masinhvien')
            ->orderBy('d.madoithi')
            ->get();
        
        // Tạo Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'BẢNG CHẤM ĐIỂM - ' . strtoupper($cuocthi->tencuocthi));
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Column headers
        $sheet->setCellValue('A3', 'STT');
        $sheet->setCellValue('B3', 'Mã bài thi');
        $sheet->setCellValue('C3', 'Mã SV/Đội');
        $sheet->setCellValue('D3', 'Tên thí sinh/Đội');
        $sheet->setCellValue('E3', 'Điểm (0-10)');
        $sheet->setCellValue('F3', 'Nhận xét');
        
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A3:F3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Data
        $row = 4;
        foreach ($baithiList as $index => $baithi) {
            $maSVorDoi = $baithi->loaidangky == 'CaNhan' ? $baithi->masinhvien : $baithi->madoithi;
            $tenThiSinh = $baithi->loaidangky == 'CaNhan' ? $baithi->sinhvien_ten : $baithi->tendoithi;
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $baithi->mabaithi);
            $sheet->setCellValue('C' . $row, $maSVorDoi);
            $sheet->setCellValue('D' . $row, $tenThiSinh);
            $sheet->setCellValue('E' . $row, $baithi->diem ?? '');
            $sheet->setCellValue('F' . $row, $baithi->nhanxet ?? '');
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Ghi chú
        $sheet->setCellValue('A' . ($row + 2), 'LƯU Ý:');
        $sheet->setCellValue('A' . ($row + 3), '- Điểm từ 0 đến 10, có thể dùng số thập phân (VD: 8.5)');
        $sheet->setCellValue('A' . ($row + 4), '- KHÔNG sửa các cột: STT, Mã bài thi, Mã SV/Đội, Tên thí sinh/Đội');
        $sheet->setCellValue('A' . ($row + 5), '- Chỉ điền vào cột: Điểm và Nhận xét');
        
        $sheet->getStyle('A' . ($row + 2))->getFont()->setBold(true);
        
        // Download
        $filename = 'BangChamDiem_' . $cuocthi->macuocthi . '_' . date('YmdHis') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);
        
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import điểm từ Excel - FIX LỖI INVALID INTEGER
     */
    public function importDiem(Request $request, $macuocthi)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel',
            'file.mimes' => 'File phải là định dạng Excel (.xlsx, .xls)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);
        
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        if (!in_array($macuocthi, $danhSachCuocThi)) {
            return back()->with('error', 'Bạn không có quyền import điểm cuộc thi này!');
        }
        
        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $processedData = [];
            
            $highestRow = $sheet->getHighestRow();
            
            Log::info("🔵 BẮT ĐẦU IMPORT ĐIỂM", [
                'macuocthi' => $macuocthi,
                'total_rows' => $highestRow,
                'magiangvien' => $giangvien->magiangvien,
                'file_name' => $file->getClientOriginalName()
            ]);
            
            // ✅ BẮT ĐẦU TRANSACTION
            DB::beginTransaction();
            
            try {
                for ($row = 4; $row <= $highestRow; $row++) {
                    // ✅ ĐỌC DỮ LIỆU TỪ EXCEL
                    $mabaithi = trim((string)$sheet->getCell('B' . $row)->getValue());
                    $diemRaw = $sheet->getCell('E' . $row)->getValue();
                    $nhanxet = trim((string)$sheet->getCell('F' . $row)->getValue());
                    
                    // Skip dòng trống hoàn toàn
                    if (empty($mabaithi) && ($diemRaw === null || $diemRaw === '')) {
                        continue;
                    }
                    
                    // ✅ KIỂM TRA MÃ BÀI THI
                    if (empty($mabaithi)) {
                        $errors[] = "Dòng $row: Thiếu mã bài thi";
                        $errorCount++;
                        continue;
                    }
                    
                    // ✅ KIỂM TRA ĐIỂM (bắt buộc phải có)
                    if ($diemRaw === null || $diemRaw === '') {
                        $errors[] = "Dòng $row: Thiếu điểm cho mã bài thi $mabaithi";
                        $errorCount++;
                        continue;
                    }
                    
                    // Chuyển đổi điểm
                    $diem = is_numeric($diemRaw) ? floatval($diemRaw) : null;
                    
                    if ($diem === null) {
                        $errors[] = "Dòng $row: Điểm không hợp lệ (phải là số) - Mã bài thi: $mabaithi";
                        $errorCount++;
                        continue;
                    }
                    
                    // Validate điểm trong khoảng 0-10
                    if ($diem < 0 || $diem > 10) {
                        $errors[] = "Dòng $row: Điểm phải từ 0 đến 10 (nhận được: $diem) - Mã bài thi: $mabaithi";
                        $errorCount++;
                        continue;
                    }
                    
                    // ✅ TÌM BÀI THI
                    $baithi = BaiThi::where('mabaithi', $mabaithi)->first();
                    
                    if (!$baithi) {
                        $errors[] = "Dòng $row: Không tìm thấy bài thi '$mabaithi'";
                        $errorCount++;
                        continue;
                    }
                    
                    // ✅ KIỂM TRA BÀI THI THUỘC CUỘC THI
                    $dethi = DB::table('dethi')->where('madethi', $baithi->madethi)->first();
                    
                    if (!$dethi || $dethi->macuocthi != $macuocthi) {
                        $errors[] = "Dòng $row: Bài thi '$mabaithi' không thuộc cuộc thi này";
                        $errorCount++;
                        continue;
                    }
                    
                    // ✅ CẬP NHẬT HOẶC TẠO MỚI KẾT QUẢ
                    $ketqua = KetQuaThi::where('mabaithi', $mabaithi)->first();
                    
                    if ($ketqua) {
                        // ✅ UPDATE KẾT QUẢ ĐÃ TỒN TẠI
                        $ketqua->diem = $diem;
                        $ketqua->nhanxet = $nhanxet ?: null;
                        $ketqua->nguoichamdiem = $giangvien->magiangvien;
                        $ketqua->ngaychamdiem = now();
                        $ketqua->xephang = null;
                        $ketqua->giaithuong = null;
                        
                        $saved = $ketqua->save();
                        
                        if ($saved) {
                            Log::info("✅ UPDATE thành công dòng $row", [
                                'maketqua' => $ketqua->maketqua,
                                'mabaithi' => $mabaithi,
                                'diem' => $diem
                            ]);
                            
                            $processedData[] = [
                                'row' => $row,
                                'action' => 'UPDATE',
                                'maketqua' => $ketqua->maketqua,
                                'mabaithi' => $mabaithi,
                                'diem' => $diem
                            ];
                            $successCount++;
                        } else {
                            $errors[] = "Dòng $row: Không thể cập nhật kết quả - Mã bài thi: $mabaithi";
                            $errorCount++;
                        }
                        
                    } else {
                        // ✅ TẠO MỚI KẾT QUẢ
                        // Lấy mã kết quả cuối cùng (CHỈ LẤY ĐÚNG ĐỊNH DẠNG KQ + SỐ)
                        $lastKetQua = DB::table('ketquathi')
                            ->select('maketqua')
                            ->where('maketqua', '~', '^KQ[0-9]+$') // ✅ Chỉ lấy KQ + số thuần
                            ->orderByRaw("CAST(SUBSTRING(maketqua FROM 3) AS INTEGER) DESC")
                            ->lockForUpdate()
                            ->first();
                        
                        $newNumber = 1;
                        if ($lastKetQua && preg_match('/^KQ(\d+)$/', $lastKetQua->maketqua, $matches)) {
                            $newNumber = intval($matches[1]) + 1;
                        }
                        
                        $maketqua = 'KQ' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
                        
                        // Tạo object mới
                        $ketqua = new KetQuaThi();
                        $ketqua->maketqua = $maketqua;
                        $ketqua->mabaithi = $mabaithi;
                        $ketqua->diem = $diem;
                        $ketqua->nhanxet = $nhanxet ?: null;
                        $ketqua->nguoichamdiem = $giangvien->magiangvien;
                        $ketqua->ngaychamdiem = now();
                        $ketqua->xephang = null;
                        $ketqua->giaithuong = null;
                        
                        $saved = $ketqua->save();
                        
                        if ($saved) {
                            Log::info("✅ CREATE thành công dòng $row", [
                                'maketqua' => $maketqua,
                                'mabaithi' => $mabaithi,
                                'diem' => $diem
                            ]);
                            
                            $processedData[] = [
                                'row' => $row,
                                'action' => 'CREATE',
                                'maketqua' => $maketqua,
                                'mabaithi' => $mabaithi,
                                'diem' => $diem
                            ];
                            $successCount++;
                        } else {
                            $errors[] = "Dòng $row: Không thể tạo kết quả mới - Mã bài thi: $mabaithi";
                            $errorCount++;
                        }
                    }
                }
                
                // ✅ Tự động Xếp Hạng SAU KHI IMPORT XONG
                if ($successCount > 0) {
                    Log::info("🏆 Bắt đầu tính xếp hạng cho cuộc thi $macuocthi");
                    $this->calculateRankingsForContest($macuocthi);
                }
                
                // ✅ COMMIT TRANSACTION
                DB::commit();
                
                Log::info("🎉 HOÀN THÀNH IMPORT", [
                    'macuocthi' => $macuocthi,
                    'success' => $successCount,
                    'errors' => $errorCount,
                    'processed_data' => $processedData
                ]);
                
            } catch (\Exception $innerException) {
                // ✅ ROLLBACK NẾU CÓ LỖI TRONG LOOP
                DB::rollBack();
                
                Log::error("❌ LỖI TRONG TRANSACTION", [
                    'message' => $innerException->getMessage(),
                    'file' => $innerException->getFile(),
                    'line' => $innerException->getLine(),
                    'trace' => $innerException->getTraceAsString()
                ]);
                
                throw $innerException;
            }
            
            // ✅ TẠO MESSAGE CHO USER
            if ($successCount > 0 && $errorCount == 0) {
                $message = "✅ Import thành công $successCount bài thi! Đã cập nhật điểm và xếp hạng.";
                $alertType = 'success';
            } elseif ($successCount > 0 && $errorCount > 0) {
                $message = "⚠️ Import thành công $successCount bài thi, có $errorCount lỗi. " . 
                        "Chi tiết: " . implode('; ', array_slice($errors, 0, 3));
                $alertType = 'warning';
            } else {
                $message = "❌ Import thất bại! Lỗi: " . implode('; ', array_slice($errors, 0, 5));
                $alertType = 'error';
            }
            
            return redirect()
                ->route('giangvien.chamdiem.show-cuocthi', $macuocthi)
                ->with($alertType, $message);
            
        } catch (\Exception $e) {
            Log::error('❌ LỖI IMPORT ĐIỂM', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', '❌ Có lỗi xảy ra khi import: ' . $e->getMessage());
        }
    }



    /**
     * Xem/Chấm điểm từng bài (giữ lại cho trường hợp chấm thủ công)
     */
    public function show($id)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $ketqua = KetQuaThi::with([
            'baithi.dethi.cuocthi',
            'baithi.dethi',
            'baithi.dangkycanhan.sinhvien.nguoiDung',
            'baithi.dangkydoi.doithi'
        ])->findOrFail($id);

        // Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        $thuocCuocThiCuaGV = false;
        if ($ketqua->baithi && $ketqua->baithi->dethi) {
            $thuocCuocThiCuaGV = in_array($ketqua->baithi->dethi->macuocthi, $danhSachCuocThi);
        }
        
        $coQuyenCham = ($ketqua->nguoichamdiem == null && $thuocCuocThiCuaGV) 
                    || ($ketqua->nguoichamdiem == $giangvien->magiangvien);
        
        if (!$coQuyenCham) {
            return redirect()
                ->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền chấm bài thi này!');
        }
        
        return view('giangvien.chamdiem.show-cuocthi', compact('ketqua', 'giangvien'));
    }

    /**
     * Cập nhật điểm thủ công (từng bài)
     */
    public function update(Request $request, $id)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $validated = $request->validate([
            'diem' => 'required|numeric|min:0|max:10',
            'nhanxet' => 'nullable|string|max:1000',
        ], [
            'diem.required' => 'Vui lòng nhập điểm',
            'diem.numeric' => 'Điểm phải là số',
            'diem.min' => 'Điểm phải từ 0 đến 10',
            'diem.max' => 'Điểm phải từ 0 đến 10',
            'nhanxet.max' => 'Nhận xét không được quá 1000 ký tự',
        ]);

        $ketqua = KetQuaThi::with('baithi.dethi')->findOrFail($id);

        // Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        $thuocCuocThiCuaGV = false;
        if ($ketqua->baithi && $ketqua->baithi->dethi) {
            $thuocCuocThiCuaGV = in_array($ketqua->baithi->dethi->macuocthi, $danhSachCuocThi);
        }
        
        $coQuyenCham = ($ketqua->nguoichamdiem == null && $thuocCuocThiCuaGV) 
                    || ($ketqua->nguoichamdiem == $giangvien->magiangvien);
        
        if (!$coQuyenCham) {
            return redirect()
                ->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền chấm bài thi này!');
        }

        $ketqua->update([
            'diem' => $validated['diem'],
            'nhanxet' => $validated['nhanxet'] ?? null,
            'nguoichamdiem' => $giangvien->magiangvien,
            'ngaychamdiem' => now(),
        ]);
        
        // ✅ TỰ ĐỘNG XẾP HẠNG VÀ GÁN GIẢI THƯỞNG
        if ($ketqua->baithi && $ketqua->baithi->dethi) {
            $this->calculateRankingsForContest($ketqua->baithi->dethi->macuocthi);
        }

        return redirect()
            ->route('giangvien.chamdiem.index')
            ->with('success', 'Chấm điểm thành công! Điểm: ' . $validated['diem'] . '/10');
    }
    
    /**
     * ✅ TỰ ĐỘNG XẾP HẠNG VÀ GÁN GIẢI THƯỞNG CHO CUỘC THI
     */
    private function calculateRankingsForContest($macuocthi)
    {
        // Lấy tất cả kết quả thi của cuộc thi, sắp xếp theo điểm giảm dần
        $ketquaList = DB::table('ketquathi as kq')
            ->join('baithi as bt', 'kq.mabaithi', '=', 'bt.mabaithi')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->where('dt.macuocthi', $macuocthi)
            ->whereNotNull('kq.diem')
            ->select('kq.maketqua', 'kq.diem', 'kq.ngaychamdiem')
            ->orderBy('kq.diem', 'desc')
            ->orderBy('kq.ngaychamdiem', 'asc') // Nếu điểm bằng nhau, ai nộp trước xếp hạng cao hơn
            ->get();
        
        $currentRank = 1;
        $previousDiem = null;
        
        foreach ($ketquaList as $index => $ketqua) {
            // Xử lý xếp hạng
            if ($previousDiem === null || $ketqua->diem < $previousDiem) {
                $currentRank = $index + 1;
            }
            // Nếu điểm bằng nhau thì giữ nguyên currentRank (đồng hạng)
            
            // ✅ CHỈ CẬP NHẬT XẾP HẠNG, KHÔNG GÁN GIẢI THƯỞNG
            DB::table('ketquathi')
                ->where('maketqua', $ketqua->maketqua)
                ->update([
                    'xephang' => $currentRank,
                    'giaithuong' => null, // Để null, Trưởng BM sẽ gán sau
                ]);
            
            $previousDiem = $ketqua->diem;
        }
        
        Log::info("✅ Đã cập nhật xếp hạng cho cuộc thi: $macuocthi", [
            'total_results' => $ketquaList->count()
        ]);
    }
    
    /**
     * ✅ XÁC ĐỊNH GIẢI THƯỞNG DỰA TRÊN XẾP HẠNG
     */
    private function determineAward($rank, $diem)
    {
        // Điểm tối thiểu để được giải thưởng (có thể tùy chỉnh)
        $minScoreForAward = 5.0;
        
        if ($diem < $minScoreForAward) {
            return null; // Không đủ điểm để nhận giải
        }
        
        return match($rank) {
            1 => 'Giải Nhất',
            2 => 'Giải Nhì',
            3 => 'Giải Ba',
            default => null,
        };
    }
    
    /**
     * ✅ API: Lấy bảng xếp hạng của cuộc thi (cho frontend)
     */
    public function getRankings($macuocthi)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        if (!in_array($macuocthi, $danhSachCuocThi)) {
            return response()->json(['error' => 'Không có quyền xem'], 403);
        }
        
        // Lấy bảng xếp hạng
        $rankings = DB::table('ketquathi as kq')
            ->join('baithi as bt', 'kq.mabaithi', '=', 'bt.mabaithi')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->where('dt.macuocthi', $macuocthi)
            ->whereNotNull('kq.diem')
            ->select(
                'kq.xephang',
                'kq.diem',
                'kq.giaithuong',
                'bt.loaidangky',
                'sv.masinhvien',
                'nd.hoten as ten_sinhvien',
                'd.madoithi',
                'd.tendoithi'
            )
            ->orderBy('kq.xephang', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'rankings' => $rankings
        ]);
    }
}