<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\CoCauGiaiThuong;
use App\Models\GanGiaiThuong;
use App\Models\CuocThi;
use App\Models\DangKyCaNhan;
use App\Models\DangKyDoiThi;
use App\Models\GiangVien;
use App\Models\BoMon;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\KetQuaThi;
use App\Models\DatGiai;
use App\Models\DiemRenLuyen;


class GiangVienGiaiThuongController extends Controller
{
    /**
     * Kiểm tra giảng viên có phải trưởng bộ môn không
     */
    private function laTruongBoMon()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user || $user->vaitro !== 'GiangVien') {
            return false;
        }
        
        // Lấy thông tin giảng viên
        $giangVien = GiangVien::where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangVien) {
            return false;
        }
        
        // Kiểm tra xem giảng viên có phải là trưởng bộ môn không
        return BoMon::where('matruongbomon', $giangVien->magiangvien)->exists();
    }

    /**
     * Lấy thông tin giảng viên từ user đang đăng nhập
     */
    private function getGiangVien()
    {
        $user = Auth::guard('web')->user();
        
        if (!$user || $user->vaitro !== 'GiangVien') {
            abort(403, 'Bạn không có quyền truy cập');
        }
        
        $giangVien = GiangVien::where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangVien) {
            abort(403, 'Không tìm thấy thông tin giảng viên');
        }
        
        return $giangVien;
    }

    /**
     * Hiển thị danh sách cuộc thi có cơ cấu giải thưởng
     */
    public function index(Request $request)
    {
        $giangVien = $this->getGiangVien();
        $laTruongBoMon = $this->laTruongBoMon();

        $query = CuocThi::query()
            ->with(['cocaugiaithuong', 'bomon'])
            ->orderBy('thoigianbatdau', 'desc');

        // Phân quyền xem cuộc thi
        if ($laTruongBoMon) {
            // Trưởng bộ môn: Xem TẤT CẢ cuộc thi của bộ môn
            $query->where('mabomon', $giangVien->mabomon);
        } else {
            // Giảng viên thường: CHỈ xem cuộc thi ĐÃ CÓ cơ cấu giải thưởng
            $query->whereHas('cocaugiaithuong');
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc trạng thái
        if ($request->filled('trangthai')) {
            $query->where('trangthai', $request->trangthai);
        }

        // Lọc theo năm
        if ($request->filled('nam')) {
            $query->whereYear('thoigianbatdau', $request->nam);
        }

        // Lọc có/chưa có cơ cấu giải
        if ($laTruongBoMon && $request->filled('co_giai')) {
            if ($request->co_giai === 'co') {
                $query->whereHas('cocaugiaithuong');
            } elseif ($request->co_giai === 'chua') {
                $query->whereDoesntHave('cocaugiaithuong');
            }
        }

        $cuocthiList = $query->paginate(12);

        // Thống kê cho mỗi cuộc thi
        foreach ($cuocthiList as $cuocthi) {
            $cuocthi->tong_cocau = $cuocthi->cocaugiaithuong()->count();
            $cuocthi->tong_slot = $cuocthi->cocaugiaithuong()->sum('soluong');
            $cuocthi->da_gan = GanGiaiThuong::whereHas('cocaugiaithuong', function($q) use ($cuocthi) {
                $q->where('macuocthi', $cuocthi->macuocthi);
            })->whereIn('trangthai', ['Pending', 'Approved'])->count();
        }

        // Danh sách năm để lọc
        $namList = CuocThi::selectRaw('EXTRACT(YEAR FROM thoigianbatdau)::integer as nam')
            ->distinct()
            ->orderBy('nam', 'desc')
            ->pluck('nam');

        return view('giangvien.giaithuong.index', compact('cuocthiList', 'laTruongBoMon', 'namList'));
    }

    /**
     * Hiển thị chi tiết cơ cấu giải thưởng của cuộc thi
     */
    public function show($macuocthi)
    {
        $giangVien = $this->getGiangVien();
        $laTruongBoMon = $this->laTruongBoMon();

        $cuocthi = CuocThi::with(['bomon'])
            ->findOrFail($macuocthi);

        // Kiểm tra quyền truy cập nếu là trưởng bộ môn
        if ($laTruongBoMon && $cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn không có quyền xem cơ cấu giải thưởng của cuộc thi này.');
        }

        $cocauList = CoCauGiaiThuong::where('macuocthi', $macuocthi)
            ->withCount([
                'gangiaithuong as da_gan' => function($query) {
                    $query->whereIn('trangthai', ['Pending', 'Approved']);
                },
                'gangiaithuong as pending' => function($query) {
                    $query->where('trangthai', 'Pending');
                },
                'gangiaithuong as approved' => function($query) {
                    $query->where('trangthai', 'Approved');
                }
            ])
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

        // Thêm thông tin con lại cho mỗi cơ cấu
        foreach ($cocauList as $cocau) {
            $cocau->con_lai = $cocau->chophepdonghang ? null : max(0, $cocau->soluong - $cocau->da_gan);
        }

        // Thống kê tổng quan
        $tongGiai = $cocauList->count();
        $tongSlot = $cocauList->where('chophepdonghang', false)->sum('soluong');
        $tongDaGan = $cocauList->sum('da_gan');
        $tongTienThuong = $cocauList->sum('tienthuong');

        return view('giangvien.giaithuong.show', compact(
            'cuocthi', 
            'cocauList', 
            'laTruongBoMon',
            'tongGiai',
            'tongSlot',
            'tongDaGan',
            'tongTienThuong'
        ));
    }

    /**
     * Hiển thị danh sách kết quả thi để gán giải (chỉ trưởng bộ môn)
     */
    public function danhSachGanGiai($macocau)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền gán giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        if ($cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể gán giải thưởng cho cuộc thi của bộ môn mình.');
        }

        // Lấy danh sách kết quả thi có xếp hạng của cuộc thi
        $ketquaList = DB::table('ketquathi as kq')
            ->join('baithi as bt', 'kq.mabaithi', '=', 'bt.mabaithi')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->leftJoin('gangiaithuong as gg', function($join) use ($macocau) {
                $join->on(function($q) {
                    $q->on('gg.madangkycanhan', '=', 'dkcn.madangkycanhan')
                      ->orOn('gg.madangkydoi', '=', 'dkdt.madangkydoi');
                })->where('gg.macocau', '=', $macocau);
            })
            ->where('dt.macuocthi', $cocau->macuocthi)
            ->whereNotNull('kq.diem')
            ->whereNotNull('kq.xephang')
            ->select(
                'kq.maketqua',
                'kq.xephang',
                'kq.diem',
                'kq.giaithuong',
                'bt.mabaithi',
                'bt.loaidangky',
                'dkcn.madangkycanhan',
                'dkdt.madangkydoi',
                'sv.masinhvien',
                'nd.hoten as ten_sinhvien',
                'd.madoithi',
                'd.tendoithi',
                'gg.magangiai',
                'gg.trangthai as trangthai_gangiai',
                'gg.ladonghang'
            )
            ->orderBy('kq.xephang', 'asc')
            ->get();

        // Thống kê
        $tongSlot = $cocau->chophepdonghang ? null : $cocau->soluong;
        $daGan = GanGiaiThuong::where('macocau', $macocau)
            ->whereIn('trangthai', ['Pending', 'Approved'])
            ->count();
        $conLai = $tongSlot ? max(0, $tongSlot - $daGan) : null;

        return view('giangvien.giaithuong.gan-giai', compact(
            'cocau',
            'ketquaList',
            'tongSlot',
            'daGan',
            'conLai'
        ));
    }

    /**
     * Gán giải thưởng cho sinh viên/đội (chỉ trưởng bộ môn)
     */
    public function storeGanGiai(Request $request, $macocau)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền gán giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        if ($cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể gán giải thưởng cho cuộc thi của bộ môn mình.');
        }

        // Validate input
        $validated = $request->validate([
            'loaidangky' => 'required|in:CaNhan,DoiNhom',
            'madangky' => 'required|string',
            'xephangthucte' => 'required|integer|min:1',
            'ladonghang' => 'nullable',
            'ghichu' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra đã gán chưa
            $existingGan = GanGiaiThuong::where('macocau', $macocau)
                ->where(function($q) use ($validated) {
                    if ($validated['loaidangky'] == 'CaNhan') {
                        $q->where('madangkycanhan', $validated['madangky']);
                    } else {
                        $q->where('madangkydoi', $validated['madangky']);
                    }
                })
                ->whereIn('trangthai', ['Pending', 'Approved'])
                ->first();

            if ($existingGan) {
                DB::rollBack();
                return back()->with('error', 'Sinh viên/Đội này đã được gán giải thưởng này rồi!');
            }

            // Kiểm tra còn slot không (nếu không phải đồng hạng)
            if (!$cocau->chophepdonghang) {
                $daGan = GanGiaiThuong::where('macocau', $macocau)
                    ->whereIn('trangthai', ['Pending', 'Approved'])
                    ->count();

                if ($daGan >= $cocau->soluong) {
                    DB::rollBack();
                    return back()->with('error', 'Đã hết slot cho giải thưởng này!');
                }
            }

            // Tạo mã gán giải - đảm bảo unique
            do {
                $magangiai = 'GG-' . strtoupper(Str::random(8));
            } while (GanGiaiThuong::where('magangiai', $magangiai)->exists());

            // Xử lý checkbox ladonghang
            $laDongHang = $request->has('ladonghang') ? true : false;

            // Chuẩn bị dữ liệu để insert
            $dataToInsert = [
                'magangiai' => $magangiai,
                'macocau' => $macocau,
                'madangkycanhan' => $validated['loaidangky'] == 'CaNhan' ? $validated['madangky'] : null,
                'madangkydoi' => $validated['loaidangky'] == 'DoiNhom' ? $validated['madangky'] : null,
                'loaidangky' => $validated['loaidangky'],
                'xephangthucte' => (int)$validated['xephangthucte'],
                'ladonghang' => $laDongHang,
                'trangthai' => 'Approved',
                'nguoigan' => $giangVien->magiangvien,
                'ngaygan' => now(),
                'ghichu' => $validated['ghichu'] ?? null,
                'nguoiduyet' => $giangVien->magiangvien,
                'ngayduyet' => now(),
            ];

            // Log để debug
            Log::info('Dữ liệu gán giải:', $dataToInsert);

            // Tạo bản ghi gán giải
            $ganGiai = GanGiaiThuong::create($dataToInsert);

            // Verify insert thành công
            if (!$ganGiai) {
                throw new \Exception('Không thể tạo bản ghi gán giải');
            }

            // Verify bản ghi đã được lưu vào DB
            $verified = GanGiaiThuong::where('magangiai', $magangiai)->first();
            if (!$verified) {
                throw new \Exception('Bản ghi gán giải không tồn tại sau khi tạo');
            }

            Log::info('Gán giải thành công:', [
                'magangiai' => $ganGiai->magangiai,
                'macocau' => $ganGiai->macocau,
                'verified' => $verified ? 'Yes' : 'No'
            ]);

            DB::commit();

            // TẠO BẢN GHI ĐẠT GIẢI SAU KHI GÁN GIẢI THÀNH CÔNG
            $this->taoBanGhiDatGiai($ganGiai, $cocau);

            return redirect()
                ->route('giangvien.giaithuong.danh-sach-gan-giai', $macocau)
                ->with('success', 'Đã gán giải thưởng thành công! Đang chờ phê duyệt.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi gán giải thưởng: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Gán giải hàng loạt (tự động theo xếp hạng)
     */
    public function ganGiaiHangLoat(Request $request, $macocau)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền gán giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        if ($cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể gán giải thưởng cho cuộc thi của bộ môn mình.');
        }

        $validated = $request->validate([
            'tu_xephang' => 'required|integer|min:1',
            'den_xephang' => 'required|integer|min:1|gte:tu_xephang',
        ]);

        try {
            DB::beginTransaction();

            // Lấy danh sách kết quả thi trong khoảng xếp hạng
            $ketquaList = DB::table('ketquathi as kq')
                ->join('baithi as bt', 'kq.mabaithi', '=', 'bt.mabaithi')
                ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
                ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
                ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
                ->where('dt.macuocthi', $cocau->macuocthi)
                ->whereNotNull('kq.xephang')
                ->whereBetween('kq.xephang', [$validated['tu_xephang'], $validated['den_xephang']])
                ->select(
                    'kq.xephang',
                    'bt.loaidangky',
                    'dkcn.madangkycanhan',
                    'dkdt.madangkydoi'
                )
                ->orderBy('kq.xephang', 'asc')
                ->get();

            $successCount = 0;
            $skipCount = 0;

            foreach ($ketquaList as $ketqua) {
                // Kiểm tra đã gán chưa
                $existingGan = GanGiaiThuong::where('macocau', $macocau)
                    ->where(function($q) use ($ketqua) {
                        if ($ketqua->loaidangky == 'CaNhan') {
                            $q->where('madangkycanhan', $ketqua->madangkycanhan);
                        } else {
                            $q->where('madangkydoi', $ketqua->madangkydoi);
                        }
                    })
                    ->whereIn('trangthai', ['Pending', 'Approved'])
                    ->first();

                if ($existingGan) {
                    $skipCount++;
                    continue;
                }

                // Kiểm tra slot (nếu không phải đồng hạng)
                if (!$cocau->chophepdonghang) {
                    $daGan = GanGiaiThuong::where('macocau', $macocau)
                        ->whereIn('trangthai', ['Pending', 'Approved'])
                        ->count();

                    if ($daGan >= $cocau->soluong) {
                        break; // Đã hết slot
                    }
                }

                // Tạo mã gán giải
                do {
                    $magangiai = 'GG-' . strtoupper(Str::random(8));
                } while (GanGiaiThuong::where('magangiai', $magangiai)->exists());

                // Gán giải
                $ganGiai = GanGiaiThuong::create([
                    'magangiai' => $magangiai,
                    'macocau' => $macocau,
                    'madangkycanhan' => $ketqua->loaidangky == 'CaNhan' ? $ketqua->madangkycanhan : null,
                    'madangkydoi' => $ketqua->loaidangky == 'DoiNhom' ? $ketqua->madangkydoi : null,
                    'loaidangky' => $ketqua->loaidangky,
                    'xephangthucte' => (int)$ketqua->xephang,
                    'ladonghang' => false,
                    'trangthai' => 'Approved',
                    'nguoigan' => $giangVien->magiangvien,
                    'ngaygan' => now(),
                    'ghichu' => 'Gán tự động theo xếp hạng',
                    'nguoiduyet' => $giangVien->magiangvien,
                    'ngayduyet' => now(),
                ]);

                // TẠO BẢN GHI ĐẠT GIẢI
                $this->taoBanGhiDatGiai($ganGiai, $cocau);

                $successCount++;
            }

            DB::commit();

            $message = "Đã gán {$successCount} giải thưởng.";
            if ($skipCount > 0) {
                $message .= " Bỏ qua {$skipCount} người đã được gán trước đó.";
            }

            return redirect()
                ->route('giangvien.giaithuong.danh-sach-gan-giai', $macocau)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi gán giải hàng loạt: ' . $e->getMessage());
            return back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hủy gán giải thưởng
     */
    public function huyGanGiai($magangiai)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền hủy gán giải.');
        }

        $giangVien = $this->getGiangVien();
        $ganGiai = GanGiaiThuong::with('cocaugiaithuong.cuocthi')->findOrFail($magangiai);

        if ($ganGiai->cocaugiaithuong->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn không có quyền hủy gán giải này.');
        }

        try {
            DB::beginTransaction();

            // ✅ 1. XÓA BẢN GHI ĐIỂM RÈN LUYỆN LIÊN QUAN
            $deletedDiemRL = DiemRenLuyen::where('magangiai', $magangiai)->delete();
            Log::info("Đã xóa {$deletedDiemRL} bản ghi điểm rèn luyện của gán giải {$magangiai}");

            // ✅ 2. XÓA BẢN GHI ĐẠT GIẢI LIÊN QUAN
            if ($ganGiai->loaidangky === 'CaNhan') {
                $deletedDatGiai = DatGiai::where('madangkycanhan', $ganGiai->madangkycanhan)
                    ->where('macuocthi', $ganGiai->cocaugiaithuong->macuocthi)
                    ->delete();
                
                Log::info("Đã xóa {$deletedDatGiai} bản ghi đạt giải (cá nhân) của gán giải {$magangiai}");
            } else {
                $deletedDatGiai = DatGiai::where('madangkydoi', $ganGiai->madangkydoi)
                    ->where('macuocthi', $ganGiai->cocaugiaithuong->macuocthi)
                    ->delete();
                
                Log::info("Đã xóa {$deletedDatGiai} bản ghi đạt giải (đội) của gán giải {$magangiai}");
            }

            // ✅ 3. XÓA BẢN GHI GÁN GIẢI
            $ganGiai->delete();
            Log::info("Đã xóa bản ghi gán giải {$magangiai}");

            DB::commit();

            return back()->with('success', 'Đã hủy gán giải thành công! Điểm rèn luyện và đạt giải đã được xóa.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi hủy gán giải: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xem chi tiết giải đã gán của một cơ cấu giải thưởng
     */
    public function showGanGiai($macocau)
    {
        $giangVien = $this->getGiangVien();
        $laTruongBoMon = $this->laTruongBoMon();

        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        // Kiểm tra quyền truy cập
        if ($laTruongBoMon && $cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn không có quyền xem danh sách gán giải này.');
        }

        $gangiaiList = GanGiaiThuong::with([
            'dangkycanhan.sinhvien',
            'dangkydoi.doithi',
            'nguoigan',
            'nguoiduyet'
        ])
        ->where('macocau', $macocau)
        ->orderBy('xephangthucte', 'asc')
        ->orderBy('ngaygan', 'desc')
        ->get();

        return view('giangvien.giaithuong.gangiai', compact(
            'cocau',
            'gangiaiList',
            'laTruongBoMon'
        ));
    }

    /**
     * Thống kê giải thưởng theo cuộc thi
     */
    public function thongke($macuocthi)
    {
        $giangVien = $this->getGiangVien();
        $laTruongBoMon = $this->laTruongBoMon();

        $cuocthi = CuocThi::findOrFail($macuocthi);

        // Kiểm tra quyền truy cập
        if ($laTruongBoMon && $cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn không có quyền xem thống kê này.');
        }

        // Thống kê tổng quan
        $thongke = [
            'tong_cocau' => CoCauGiaiThuong::where('macuocthi', $macuocthi)->count(),
            'tong_slot' => CoCauGiaiThuong::where('macuocthi', $macuocthi)
                ->where('chophepdonghang', false)
                ->sum('soluong'),
            'tong_da_gan' => GanGiaiThuong::whereHas('cocaugiaithuong', function($q) use ($macuocthi) {
                $q->where('macuocthi', $macuocthi);
            })->whereIn('trangthai', ['Pending', 'Approved'])->count(),
            'tong_pending' => GanGiaiThuong::whereHas('cocaugiaithuong', function($q) use ($macuocthi) {
                $q->where('macuocthi', $macuocthi);
            })->where('trangthai', 'Pending')->count(),
            'tong_approved' => GanGiaiThuong::whereHas('cocaugiaithuong', function($q) use ($macuocthi) {
                $q->where('macuocthi', $macuocthi);
            })->where('trangthai', 'Approved')->count(),
            'tong_tien_thuong' => CoCauGiaiThuong::where('macuocthi', $macuocthi)->sum('tienthuong'),
        ];

        return view('giangvien.giaithuong.thongke', compact(
            'cuocthi',
            'thongke',
            'laTruongBoMon'
        ));
    }

    /**
     * Hiển thị form tạo cơ cấu giải thưởng (chỉ trưởng bộ môn)
     */
    public function create($macuocthi)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền tạo cơ cấu giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cuocthi = CuocThi::findOrFail($macuocthi);

        // Kiểm tra cuộc thi có thuộc bộ môn không
        if ($cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể tạo cơ cấu giải thưởng cho cuộc thi của bộ môn mình.');
        }

        return view('giangvien.giaithuong.create', compact('cuocthi'));
    }

    /**
     * Lưu cơ cấu giải thưởng mới (chỉ trưởng bộ môn)
     */
    public function store(Request $request, $macuocthi)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền tạo cơ cấu giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cuocthi = CuocThi::findOrFail($macuocthi);

        if ($cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể tạo cơ cấu giải thưởng cho cuộc thi của bộ môn mình.');
        }

        $validated = $request->validate([
            'tengiai' => 'required|string|max:200',
            'soluong' => 'required|integer|min:1',
            'tienthuong' => 'nullable|numeric|min:0',
            'giaykhen' => 'required|in:0,1',
            'chophepdonghang' => 'required|in:0,1',  // Sửa từ chophepdongkang
            'ghichudonghang' => 'nullable|string|max:500',  // Sửa từ ghichudongkang
            'ghichu' => 'nullable|string|max:500',
        ], [
            'tengiai.required' => 'Vui lòng nhập tên giải thưởng',
            'soluong.required' => 'Vui lòng nhập số lượng',
            'soluong.min' => 'Số lượng phải lớn hơn 0',
            'tienthuong.min' => 'Tiền thưởng không được âm',
            'giaykhen.required' => 'Vui lòng chọn có giấy khen hay không',
            'chophepdonghang.required' => 'Vui lòng chọn có cho phép đồng hạng hay không',  // Sửa
        ]);

        try {
            DB::beginTransaction();

            $macocau = 'COCAU-' . strtoupper(Str::random(8));

            CoCauGiaiThuong::create([
                'macocau' => $macocau,
                'macuocthi' => $macuocthi,
                'tengiai' => $validated['tengiai'],
                'soluong' => $validated['soluong'],
                'tienthuong' => $validated['tienthuong'] ?? 0,
                'giaykhen' => (bool)$validated['giaykhen'],
                'chophepdonghang' => (bool)$validated['chophepdonghang'],  // Sửa từ chophepdongkang
                'ghichudonghang' => $validated['ghichudonghang'] ?? null,  // Sửa từ ghichudongkang
                'ghichu' => $validated['ghichu'] ?? null,
                'trangthai' => 'Active',
                'ngaytao' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('giangvien.giaithuong.show', $macuocthi)
                ->with('success', 'Đã thêm cơ cấu giải thưởng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log lỗi để debug
            Log::error('Lỗi tạo cơ cấu giải thưởng: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa cơ cấu giải thưởng (chỉ trưởng bộ môn)
     */
    public function edit($macocau)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền chỉnh sửa cơ cấu giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        if ($cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể chỉnh sửa cơ cấu giải thưởng của bộ môn mình.');
        }

        return view('giangvien.giaithuong.edit', compact('cocau'));
    }

    /**
     * Cập nhật cơ cấu giải thưởng (chỉ trưởng bộ môn)
     */
    public function update(Request $request, $macocau)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền chỉnh sửa cơ cấu giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        if ($cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể chỉnh sửa cơ cấu giải thưởng của bộ môn mình.');
        }

        $validated = $request->validate([
            'tengiai' => 'required|string|max:200',
            'soluong' => 'required|integer|min:1',
            'tienthuong' => 'nullable|numeric|min:0',
            'giaykhen' => 'required|in:0,1',
            'chophepdonghang' => 'required|in:0,1',  // Sửa từ chophepdongkang
            'ghichudonghang' => 'nullable|string|max:500',  // Sửa từ ghichudongkang
            'ghichu' => 'nullable|string|max:500',
            'trangthai' => 'required|in:Active,Inactive',
        ], [
            'tengiai.required' => 'Vui lòng nhập tên giải thưởng',
            'soluong.required' => 'Vui lòng nhập số lượng',
            'soluong.min' => 'Số lượng phải lớn hơn 0',
            'tienthuong.min' => 'Tiền thưởng không được âm',
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra nếu giảm số lượng, có đủ slot không
            if (!$cocau->chophepdonghang && $validated['soluong'] < $cocau->soluong) {  // Sửa từ chophepdongkang
                $daGan = $cocau->gangiaithuong()
                    ->whereIn('trangthai', ['Pending', 'Approved'])
                    ->count();
                
                if ($validated['soluong'] < $daGan) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', "Không thể giảm số lượng xuống {$validated['soluong']} vì đã gán {$daGan} giải.");
                }
            }

            $cocau->update($validated);

            DB::commit();

            return redirect()
                ->route('giangvien.giaithuong.show', $cocau->macuocthi)
                ->with('success', 'Đã cập nhật cơ cấu giải thưởng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa cơ cấu giải thưởng (chỉ trưởng bộ môn)
     */
    public function destroy($macocau)
    {
        if (!$this->laTruongBoMon()) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền xóa cơ cấu giải thưởng.');
        }

        $giangVien = $this->getGiangVien();
        $cocau = CoCauGiaiThuong::with('cuocthi')->findOrFail($macocau);

        if ($cocau->cuocthi->mabomon !== $giangVien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể xóa cơ cấu giải thưởng của bộ môn mình.');
        }

        try {
            DB::beginTransaction();

            // Kiểm tra đã có giải thưởng được gán chưa
            $daGan = $cocau->gangiaithuong()->count();
            if ($daGan > 0) {
                DB::rollBack();
                return back()->with('error', "Không thể xóa vì đã có {$daGan} giải thưởng được gán.");
            }

            $macuocthi = $cocau->macuocthi;
            $cocau->delete();

            DB::commit();

            return redirect()
                ->route('giangvien.giaithuong.show', $macuocthi)
                ->with('success', 'Đã xóa cơ cấu giải thưởng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * TẠO BẢN GHI ĐẠT GIẢI VÀ CỘNG ĐIỂM RÈN LUYỆN
     * +15 điểm khi đạt giải
     */
    private function taoBanGhiDatGiai($ganGiai, $cocau)
    {
        try {
            DB::beginTransaction();

            // Tạo mã đạt giải
            do {
                $madatgiai = 'DG-' . strtoupper(Str::random(8));
            } while (DatGiai::where('madatgiai', $madatgiai)->exists());

            // Điểm rèn luyện: Đạt giải = +15
            $diemRenLuyen = 15;

            // Tạo tên giải với thông tin xếp hạng
            $tenGiai = $cocau->tengiai;
            if ($ganGiai->ladonghang) {
                $tenGiai .= ' (Đồng hạng ' . $ganGiai->xephangthucte . ')';
            } else {
                $tenGiai .= ' (Hạng ' . $ganGiai->xephangthucte . ')';
            }

            // Tạo mô tả giải thưởng: Tiền thưởng + Giấy khen
            $giaiThuongParts = [];
            
            if ($cocau->tienthuong > 0) {
                $giaiThuongParts[] = number_format($cocau->tienthuong, 0, ',', '.') . ' VNĐ';
            }
            
            if ($cocau->giaykhen) {
                $giaiThuongParts[] = 'Giấy khen';
            }
            
            $giaiThuong = !empty($giaiThuongParts) 
                ? implode(' + ', $giaiThuongParts) 
                : 'Không có phần thưởng';

            // Tạo bản ghi đạt giải
            DatGiai::create([
                'madatgiai' => $madatgiai,
                'macuocthi' => $cocau->macuocthi,
                'madangkycanhan' => $ganGiai->loaidangky === 'CaNhan' ? $ganGiai->madangkycanhan : null,
                'madangkydoi' => $ganGiai->loaidangky === 'DoiNhom' ? $ganGiai->madangkydoi : null,
                'loaidangky' => $ganGiai->loaidangky,
                'tengiai' => $tenGiai,
                'giaithuong' => $giaiThuong,
                'diemrenluyen' => $diemRenLuyen,
                'ngaytrao' => now(),
            ]);

            Log::info("Đã tạo bản ghi đạt giải {$madatgiai} - Giải thưởng: {$giaiThuong}");

            // ✅ CỘNG ĐIỂM RÈN LUYỆN CHO SINH VIÊN
            if ($ganGiai->loaidangky === 'CaNhan') {
                // Đăng ký cá nhân: Cộng điểm cho 1 sinh viên
                $dangKy = DangKyCaNhan::with('sinhvien')->find($ganGiai->madangkycanhan);
                
                if ($dangKy && $dangKy->sinhvien) {
                    $this->congDiemRenLuyen(
                        $dangKy->sinhvien->masinhvien,
                        $cocau->macuocthi,
                        $ganGiai->magangiai,
                        'DatGiai',
                        $diemRenLuyen,
                        "Đạt giải: {$tenGiai} - {$giaiThuong}"
                    );
                    
                    Log::info("Đã cộng {$diemRenLuyen} điểm rèn luyện cho sinh viên {$dangKy->sinhvien->masinhvien}");
                }
            } else {
                // Đăng ký đội: Cộng điểm cho TẤT CẢ thành viên trong đội
                $dangKy = DangKyDoiThi::with('doithi.thanhvien.sinhvien')->find($ganGiai->madangkydoi);
                
                if ($dangKy && $dangKy->doithi && $dangKy->doithi->thanhvien) {
                    foreach ($dangKy->doithi->thanhvien as $thanhVien) {
                        if ($thanhVien->sinhvien) {
                            $this->congDiemRenLuyen(
                                $thanhVien->sinhvien->masinhvien,
                                $cocau->macuocthi,
                                $ganGiai->magangiai,
                                'DatGiai',
                                $diemRenLuyen,
                                "Đạt giải (Đội: {$dangKy->doithi->tendoithi}): {$tenGiai} - {$giaiThuong}"
                            );
                            
                            Log::info("Đã cộng {$diemRenLuyen} điểm rèn luyện cho sinh viên {$thanhVien->sinhvien->masinhvien} (Đội: {$dangKy->doithi->tendoithi})");
                        }
                    }
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo bản ghi đạt giải: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * HÀM PHỤ: CỘNG ĐIỂM RÈN LUYỆN CHO SINH VIÊN
     * ✅ Sử dụng Model DiemRenLuyen với field magangiai
     */
    private function congDiemRenLuyen($masinhvien, $macuocthi, $magangiai, $loaihoatdong, $diem, $mota)
    {
        try {
            // Tạo mã điểm rèn luyện
            do {
                $madiemrl = 'DRL-' . strtoupper(Str::random(8));
            } while (DiemRenLuyen::where('madiemrl', $madiemrl)->exists());

            // ✅ Sử dụng Model DiemRenLuyen với field magangiai
            DiemRenLuyen::create([
                'madiemrl' => $madiemrl,
                'masinhvien' => $masinhvien,
                'macuocthi' => $macuocthi,
                'mahoatdong' => null, // Không phải hoạt động hỗ trợ
                'magangiai' => $magangiai, // ✅ Liên kết với giải thưởng đã gán
                'loaihoatdong' => $loaihoatdong, // 'DatGiai'
                'diem' => $diem,
                'mota' => $mota,
                'ngaycong' => now(),
            ]);

            Log::info("Đã tạo bản ghi điểm rèn luyện {$madiemrl} cho sinh viên {$masinhvien}");
            return true;

        } catch (\Exception $e) {
            Log::error("Lỗi cộng điểm rèn luyện cho sinh viên {$masinhvien}: " . $e->getMessage());
            return false;
        }
    }
}