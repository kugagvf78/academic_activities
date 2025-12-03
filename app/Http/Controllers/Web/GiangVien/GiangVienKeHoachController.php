<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GiangVienKeHoachController extends Controller
{
    /**
     * Danh sách kế hoạch cuộc thi
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $query = DB::table('kehoachcuocthi as kh')
            ->leftJoin('bomon as bm', 'kh.mabomon', '=', 'bm.mabomon')
            ->leftJoin('cuocthi as ct', 'kh.makehoach', '=', 'ct.makehoach')
            ->where('kh.mabomon', $giangvien->mabomon)
            ->select(
                'kh.*',
                'bm.tenbomon',
                'ct.macuocthi',
                'ct.trangthai as trangthai_cuocthi'
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kh.tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('kh.trangthaiduyet', $request->status);
        }

        // Lọc theo năm học
        if ($request->filled('namhoc')) {
            $query->where('kh.namhoc', $request->namhoc);
        }

        // Lọc theo học kỳ
        if ($request->filled('hocky')) {
            $query->where('kh.hocky', $request->hocky);
        }

        // THAY ĐỔI: Đổi tên biến từ $kehoachList sang $kehoachs
        $kehoachs = $query->orderBy('kh.ngaynopkehoach', 'desc')->paginate(10);

        // Transform data
        $kehoachs->getCollection()->transform(function ($kh) {
            $kh->status_label = $this->getStatusLabel($kh);
            $kh->status_color = $this->getStatusColor($kh);
            $kh->can_create_cuocthi = $kh->trangthaiduyet == 'Approved' && !$kh->macuocthi;
            return $kh;
        });

        // THÊM: Lấy danh sách năm học
        $namhocs = DB::table('kehoachcuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->distinct()
            ->pluck('namhoc')
            ->sort()
            ->values();

        // THÊM: Kiểm tra xem có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        // THAY ĐỔI: Truyền $kehoachs thay vì $kehoachList
        return view('giangvien.kehoach.index', compact('kehoachs', 'giangvien', 'namhocs', 'isTruongBoMon'));
    }

    /**
     * API thống kê
     */
    public function statistics()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return response()->json(['error' => 'Không tìm thấy thông tin giảng viên'], 404);
        }

        $stats = DB::table('kehoachcuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->select('trangthaiduyet', DB::raw('count(*) as total'))
            ->groupBy('trangthaiduyet')
            ->get()
            ->pluck('total', 'trangthaiduyet');

        return response()->json([
            'pending' => $stats['Pending'] ?? 0,
            'approved' => $stats['Approved'] ?? 0,
            'rejected' => $stats['Rejected'] ?? 0,
            'total' => $stats->sum()
        ]);
    }

    /**
     * Hiển thị form tạo kế hoạch
     */
    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $bomons = DB::table('bomon')->get();
        
        return view('giangvien.kehoach.create', compact('giangvien', 'bomons'));
    }

    /**
     * Lưu kế hoạch mới
     */
    public function store(Request $request)
    {
        // ⭐ SỬA VALIDATION
        $validated = $request->validate([
            'tencuocthi' => 'required|string|max:300',
            'loaicuocthi' => 'required|string|in:CuocThi,Seminar,HoiThao',
            'namhoc' => 'required|string|max:20',
            'hocky' => 'required|integer|in:1,2,3',
            'mota' => 'nullable|string',
            'mucdich' => 'nullable|string',
            'doituongthamgia' => 'nullable|string|max:200',
            'thoigianbatdau' => 'required|date',
            'thoigianketthuc' => 'required|date|after:thoigianbatdau',
            'diadiem' => 'nullable|string|max:300',
            'soluongthanhvien' => 'nullable|integer|min:1',
            'hinhthucthamgia' => 'required|string|in:CaNhan,DoiNhom,CaHai',  // ⭐ THÊM
            'dutrukinhphi' => 'nullable|numeric|min:0',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        DB::beginTransaction();
        try {
            // Tạo mã kế hoạch
            $lastKeHoach = DB::table('kehoachcuocthi')
                ->where('makehoach', 'LIKE', 'KH%')
                ->orderByRaw('CAST(SUBSTRING(makehoach FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastKeHoach && preg_match('/KH(\d+)/', $lastKeHoach->makehoach, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $makehoach = 'KH' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Lưu kế hoạch
            DB::table('kehoachcuocthi')->insert([
                'makehoach' => $makehoach,
                'mabomon' => $giangvien->mabomon,
                'tencuocthi' => $validated['tencuocthi'],
                'loaicuocthi' => $validated['loaicuocthi'],
                'namhoc' => $validated['namhoc'],
                'hocky' => $validated['hocky'],
                'mota' => $validated['mota'] ?? null,
                'mucdich' => $validated['mucdich'] ?? null,
                'doituongthamgia' => $validated['doituongthamgia'] ?? null,
                'thoigianbatdau' => $validated['thoigianbatdau'],
                'thoigianketthuc' => $validated['thoigianketthuc'],
                'diadiem' => $validated['diadiem'] ?? null,
                'soluongthanhvien' => $validated['soluongthanhvien'] ?? null,
                'hinhthucthamgia' => $validated['hinhthucthamgia'] ?? null,
                'dutrukinhphi' => $validated['dutrukinhphi'] ?? null,
                'trangthaiduyet' => 'Pending',
                'ngaynopkehoach' => now(),
                'nguoinop' => $giangvien->magiangvien,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // ⭐ THÊM LOG CHI TIẾT
            Log::error('Lỗi tạo kế hoạch: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            
            // ⭐ HIỂN THỊ LỖI CHI TIẾT KHI DEBUG
            if (config('app.debug')) {
                return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            }
            
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo kế hoạch. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.kehoach.index')
            ->with('success', 'Tạo kế hoạch thành công! Vui lòng chờ phê duyệt.');
    }

    /**
     * Chi tiết kế hoạch
     */
    public function show($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $kehoach = DB::table('kehoachcuocthi as kh')
            ->leftJoin('bomon as bm', 'kh.mabomon', '=', 'bm.mabomon')
            ->leftJoin('cuocthi as ct', 'kh.makehoach', '=', 'ct.makehoach')
            ->leftJoin('giangvien as gv_nop', 'kh.nguoinop', '=', 'gv_nop.magiangvien')
            ->leftJoin('nguoidung as nd_nop', 'gv_nop.manguoidung', '=', 'nd_nop.manguoidung')
            ->leftJoin('giangvien as gv_duyet', 'kh.nguoiduyet', '=', 'gv_duyet.magiangvien')
            ->leftJoin('nguoidung as nd_duyet', 'gv_duyet.manguoidung', '=', 'nd_duyet.manguoidung')
            ->where('kh.makehoach', $id)
            ->select(
                'kh.*',
                'bm.tenbomon',
                'ct.macuocthi',
                'ct.trangthai as trangthai_cuocthi',
                'nd_nop.hoten as tennguoinop',
                'nd_duyet.hoten as tennguoiduyet'
            )
            ->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        $kehoach->status_label = $this->getStatusLabel($kehoach);
        $kehoach->status_color = $this->getStatusColor($kehoach);
        $kehoach->can_create_cuocthi = $kehoach->trangthaiduyet == 'Approved' && !$kehoach->macuocthi;

        // ⭐ THÊM: Kiểm tra có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        // ⭐ SỬA: Thêm $isTruongBoMon vào compact
        return view('giangvien.kehoach.show', compact('kehoach', 'isTruongBoMon'));
    }
    
    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();
        
        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Chỉ cho sửa nếu đang Pending hoặc Rejected
        if (!in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected'])) {
            return redirect()->route('giangvien.kehoach.show', $id)
                ->with('error', 'Không thể chỉnh sửa kế hoạch đã được duyệt!');
        }

        $bomons = DB::table('bomon')->get();

        return view('giangvien.kehoach.edit', compact('kehoach', 'bomons'));
    }

    /**
     * Cập nhật kế hoạch
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tencuocthi' => 'required|string|max:255',
            'loaicuocthi' => 'required|string',
            'namhoc' => 'required|string',
            'hocky' => 'required|integer',
            'mota' => 'nullable|string',
            'mucdich' => 'nullable|string',
            'doituongthamgia' => 'nullable|string',
            'thoigianbatdau' => 'required|date',
            'thoigianketthuc' => 'required|date|after:thoigianbatdau',
            'diadiem' => 'nullable|string',
            'soluongthanhvien' => 'nullable|integer',
            'hinhthucthamgia' => 'nullable|string',
            'dutrukinhphi' => 'nullable|numeric',
        ]);

        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Chỉ cho sửa nếu đang Pending hoặc Rejected
        if (!in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected'])) {
            return redirect()->route('giangvien.kehoach.show', $id)
                ->with('error', 'Không thể chỉnh sửa kế hoạch đã được duyệt!');
        }

        // Nếu đang ở trạng thái Rejected, chuyển về Pending khi sửa
        if ($kehoach->trangthaiduyet == 'Rejected') {
            $validated['trangthaiduyet'] = 'Pending';
            $validated['ghichu'] = null; // Xóa ghi chú từ chối cũ
        }

        DB::table('kehoachcuocthi')->where('makehoach', $id)->update($validated);

        return redirect()->route('giangvien.kehoach.show', $id)
            ->with('success', 'Cập nhật kế hoạch thành công!');
    }

    /**
     * Xóa kế hoạch
     */
    public function destroy($id)
    {
        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Không cho xóa nếu đã được duyệt hoặc đã tạo cuộc thi
        if ($kehoach->trangthaiduyet == 'Approved') {
            return back()->with('error', 'Không thể xóa kế hoạch đã được duyệt!');
        }

        $cuocthi = DB::table('cuocthi')->where('makehoach', $id)->first();
        if ($cuocthi) {
            return back()->with('error', 'Không thể xóa kế hoạch đã có cuộc thi!');
        }

        DB::table('kehoachcuocthi')->where('makehoach', $id)->delete();

        return redirect()->route('giangvien.kehoach.index')
            ->with('success', 'Xóa kế hoạch thành công!');
    }

    /**
     * Gửi lại kế hoạch bị từ chối
     */
    public function resubmit($id)
    {
        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        if ($kehoach->trangthaiduyet != 'Rejected') {
            return back()->with('error', 'Chỉ có thể gửi lại kế hoạch bị từ chối!');
        }

        DB::table('kehoachcuocthi')->where('makehoach', $id)->update([
            'trangthaiduyet' => 'Pending',
            'ghichu' => null,
            'nguoiduyet' => null,
            'ngayduyet' => null
        ]);

        return redirect()->route('giangvien.kehoach.show', $id)
            ->with('success', 'Đã gửi lại kế hoạch để xét duyệt!');
    }

    /**
     * Tạo cuộc thi từ kế hoạch đã được duyệt
     */
    public function createCuocThi($makehoach)
    {
        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $makehoach)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Kiểm tra kế hoạch đã được duyệt chưa
        if ($kehoach->trangthaiduyet != 'Approved') {
            return redirect()->route('giangvien.kehoach.show', $makehoach)
                ->with('error', 'Kế hoạch chưa được duyệt!');
        }

        // Kiểm tra đã tạo cuộc thi chưa
        $existingCuocThi = DB::table('cuocthi')->where('makehoach', $makehoach)->first();
        if ($existingCuocThi) {
            return redirect()->route('giangvien.cuocthi.show', $existingCuocThi->macuocthi)
                ->with('info', 'Cuộc thi từ kế hoạch này đã được tạo!');
        }

        DB::beginTransaction();
        try {
            // Tạo mã cuộc thi
            $lastCuocthi = DB::table('cuocthi')
                ->where('macuocthi', 'LIKE', 'CT%')
                ->orderByRaw('CAST(SUBSTRING(macuocthi FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastCuocthi && preg_match('/CT(\d+)/', $lastCuocthi->macuocthi, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $macuocthi = 'CT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Mapping dữ liệu từ kế hoạch sang cuộc thi
            DB::table('cuocthi')->insert([
                'macuocthi' => $macuocthi,
                'makehoach' => $makehoach,
                'mabomon' => $kehoach->mabomon,
                'tencuocthi' => $kehoach->tencuocthi,
                'loaicuocthi' => $kehoach->loaicuocthi,
                'mota' => $kehoach->mota,
                'mucdich' => $kehoach->mucdich,
                'doituongthamgia' => $kehoach->doituongthamgia,
                'thoigianbatdau' => $kehoach->thoigianbatdau,
                'thoigianketthuc' => $kehoach->thoigianketthuc,
                'diadiem' => $kehoach->diadiem,
                'soluongthanhvien' => $kehoach->soluongthanhvien,
                'hinhthucthamgia' => $kehoach->hinhthucthamgia,
                'dutrukinhphi' => $kehoach->dutrukinhphi,
                'trangthai' => 'Approved',
                'ngaytao' => now(),
                'ngaycapnhat' => now(),
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi tạo cuộc thi. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.cuocthi.show', $macuocthi)
            ->with('success', 'Tạo cuộc thi từ kế hoạch thành công!');
    }

    // Helper methods
    private function getStatusLabel($kehoach)
    {
        switch ($kehoach->trangthaiduyet) {
            case 'Pending':
                return 'Chờ duyệt';
            case 'Approved':
                return $kehoach->macuocthi ? 'Đã tạo cuộc thi' : 'Đã duyệt';
            case 'Rejected':
                return 'Từ chối';
            default:
                return 'Nháp';
        }
    }

    private function getStatusColor($kehoach)
    {
        switch ($kehoach->trangthaiduyet) {
            case 'Pending':
                return 'yellow';
            case 'Approved':
                return 'green';
            case 'Rejected':
                return 'red';
            default:
                return 'gray';
        }
    }

    /**
     * Duyệt kế hoạch (Chỉ trưởng bộ môn)
     */
    public function approve($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return back()->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền duyệt kế hoạch!');
        }

        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Kiểm tra kế hoạch thuộc bộ môn của trưởng bộ môn
        if ($kehoach->mabomon != $giangvien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể duyệt kế hoạch của bộ môn mình!');
        }

        // Chỉ duyệt khi đang Pending
        if ($kehoach->trangthaiduyet != 'Pending') {
            return back()->with('error', 'Chỉ có thể duyệt kế hoạch đang chờ duyệt!');
        }

        // Cập nhật trạng thái
        DB::table('kehoachcuocthi')
            ->where('makehoach', $id)
            ->update([
                'trangthaiduyet' => 'Approved',
                'ngayduyet' => now(),
                'nguoiduyet' => $giangvien->magiangvien,
                'ghichu' => null, // Xóa ghi chú cũ nếu có
            ]);

        return back()->with('success', 'Đã duyệt kế hoạch thành công!');
    }

    /**
     * Từ chối kế hoạch (Chỉ trưởng bộ môn)
     */
    public function reject(Request $request, $id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return back()->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền từ chối kế hoạch!');
        }

        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Kiểm tra kế hoạch thuộc bộ môn của trưởng bộ môn
        if ($kehoach->mabomon != $giangvien->mabomon) {
            return back()->with('error', 'Bạn chỉ có thể từ chối kế hoạch của bộ môn mình!');
        }

        // Chỉ từ chối khi đang Pending
        if ($kehoach->trangthaiduyet != 'Pending') {
            return back()->with('error', 'Chỉ có thể từ chối kế hoạch đang chờ duyệt!');
        }

        // Validate ghi chú (bắt buộc khi từ chối)
        $validated = $request->validate([
            'ghichu' => 'required|string|max:500',
        ], [
            'ghichu.required' => 'Vui lòng nhập lý do từ chối!',
            'ghichu.max' => 'Lý do không được quá 500 ký tự!',
        ]);

        // Cập nhật trạng thái
        DB::table('kehoachcuocthi')
            ->where('makehoach', $id)
            ->update([
                'trangthaiduyet' => 'Rejected',
                'ngayduyet' => now(),
                'nguoiduyet' => $giangvien->magiangvien,
                'ghichu' => $validated['ghichu'],
            ]);

        return back()->with('success', 'Đã từ chối kế hoạch!');
    }
}