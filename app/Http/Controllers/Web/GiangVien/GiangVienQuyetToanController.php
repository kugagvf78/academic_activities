<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GiangVienQuyetToanController extends Controller
{
    /**
     * Danh sách quyết toán
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra xem có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        // Query quyết toán
        $query = DB::table('quyettoan as qt')
            ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('giangvien as gv_nguoilap', 'qt.nguoilap', '=', 'gv_nguoilap.magiangvien')
            ->leftJoin('nguoidung as nd_nguoilap', 'gv_nguoilap.manguoidung', '=', 'nd_nguoilap.manguoidung')
            ->leftJoin('giangvien as gv_nguoiduyet', 'qt.nguoiduyet', '=', 'gv_nguoiduyet.magiangvien')
            ->leftJoin('nguoidung as nd_nguoiduyet', 'gv_nguoiduyet.manguoidung', '=', 'nd_nguoiduyet.manguoidung')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->select(
                'qt.*',
                'ct.tencuocthi',
                'nd_nguoilap.hoten as tennguoilap',
                'nd_nguoiduyet.hoten as tennguoiduyet'
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ct.tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('qt.trangthai', $request->status);
        }

        // Lọc theo cuộc thi
        if ($request->filled('macuocthi')) {
            $query->where('qt.macuocthi', $request->macuocthi);
        }

        $quyettoans = $query->orderBy('qt.ngayquyettoan', 'desc')->paginate(10);

        // Lấy danh sách cuộc thi để filter
        $cuocthis = DB::table('cuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->select('macuocthi', 'tencuocthi')
            ->orderBy('tencuocthi')
            ->get();

        return view('giangvien.quyettoan.index', compact('quyettoans', 'cuocthis', 'giangvien', 'isTruongBoMon'));
    }

    /**
     * Hiển thị form tạo quyết toán
     */
    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        // Lấy danh sách cuộc thi đã hoàn thành và chưa có quyết toán
        $cuocthis = DB::table('cuocthi as ct')
            ->leftJoin('quyettoan as qt', 'ct.macuocthi', '=', 'qt.macuocthi')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->where('ct.trangthai', 'Completed')
            ->whereNull('qt.maquyettoan')
            ->select('ct.macuocthi', 'ct.tencuocthi', 'ct.dutrukinhphi', 'ct.chiphithucte')
            ->get();
        
        return view('giangvien.quyettoan.create', compact('giangvien', 'cuocthis'));
    }

    /**
     * Lưu quyết toán mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'tongdutru' => 'required|numeric|min:0',
            'tongthucte' => 'required|numeric|min:0',
            'filequyettoan' => 'nullable|file|mimes:pdf|max:10240',
            'ghichu' => 'nullable|string',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Kiểm tra xem cuộc thi đã có quyết toán chưa
        $existing = DB::table('quyettoan')
            ->where('macuocthi', $validated['macuocthi'])
            ->exists();

        if ($existing) {
            return back()->withInput()->with('error', 'Cuộc thi này đã có quyết toán!');
        }

        DB::beginTransaction();
        try {
            // Tạo mã quyết toán tự động
            $lastQuyetToan = DB::table('quyettoan')
                ->where('maquyettoan', 'LIKE', 'QT%')
                ->orderByRaw('CAST(SUBSTRING(maquyettoan FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastQuyetToan && preg_match('/QT(\d+)/', $lastQuyetToan->maquyettoan, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $validated['maquyettoan'] = 'QT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $validated['chenhlech'] = $validated['tongdutru'] - $validated['tongthucte'];
            $validated['trangthai'] = 'Draft';
            $validated['nguoilap'] = $giangvien->magiangvien;
            $validated['ngayquyettoan'] = now();

            // Upload file quyết toán nếu có
            if ($request->hasFile('filequyettoan')) {
                $file = $request->file('filequyettoan');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('quyettoan', $filename, 'public');
                $validated['filequyettoan'] = $path;
            }

            DB::table('quyettoan')->insert($validated);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo quyết toán. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.quyettoan.index')
            ->with('success', 'Tạo quyết toán thành công!');
    }

    /**
     * Chi tiết quyết toán
     */
    public function show($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $quyettoan = DB::table('quyettoan as qt')
            ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv_nguoilap', 'qt.nguoilap', '=', 'gv_nguoilap.magiangvien')
            ->leftJoin('nguoidung as nd_nguoilap', 'gv_nguoilap.manguoidung', '=', 'nd_nguoilap.manguoidung')
            ->leftJoin('giangvien as gv_nguoiduyet', 'qt.nguoiduyet', '=', 'gv_nguoiduyet.magiangvien')
            ->leftJoin('nguoidung as nd_nguoiduyet', 'gv_nguoiduyet.manguoidung', '=', 'nd_nguoiduyet.manguoidung')
            ->where('qt.maquyettoan', $id)
            ->select(
                'qt.*',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'ct.thoigianbatdau',
                'ct.thoigianketthuc',
                'bm.tenbomon',
                'bm.matruongbomon',
                'nd_nguoilap.hoten as tennguoilap',
                'nd_nguoiduyet.hoten as tennguoiduyet'
            )
            ->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Kiểm tra xem có phải trưởng bộ môn không
        $isTruongBoMon = ($quyettoan->matruongbomon == $giangvien->magiangvien);

        // Lấy danh sách chi phí của cuộc thi
        $chiphis = DB::table('chiphi')
            ->where('macuocthi', $quyettoan->macuocthi)
            ->where('trangthai', 'Approved')
            ->get();

        return view('giangvien.quyettoan.show', compact('quyettoan', 'chiphis', 'isTruongBoMon'));
    }

    /**
     * Hiển thị form chỉnh sửa quyết toán
     */
    public function edit($id)
    {
        $quyettoan = DB::table('quyettoan as qt')
            ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
            ->where('qt.maquyettoan', $id)
            ->select('qt.*', 'ct.tencuocthi')
            ->first();
        
        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Chỉ cho phép sửa nếu đang ở trạng thái Draft
        if ($quyettoan->trangthai != 'Draft') {
            return redirect()->route('giangvien.quyettoan.show', $id)
                ->with('error', 'Không thể chỉnh sửa quyết toán đã được nộp hoặc duyệt!');
        }

        return view('giangvien.quyettoan.edit', compact('quyettoan'));
    }

    /**
     * Cập nhật quyết toán
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tongdutru' => 'required|numeric|min:0',
            'tongthucte' => 'required|numeric|min:0',
            'filequyettoan' => 'nullable|file|mimes:pdf|max:10240',
            'ghichu' => 'nullable|string',
        ]);

        $quyettoan = DB::table('quyettoan')->where('maquyettoan', $id)->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Chỉ cho phép sửa nếu đang ở trạng thái Draft
        if ($quyettoan->trangthai != 'Draft') {
            return redirect()->route('giangvien.quyettoan.show', $id)
                ->with('error', 'Không thể chỉnh sửa quyết toán đã được nộp hoặc duyệt!');
        }

        $validated['chenhlech'] = $validated['tongdutru'] - $validated['tongthucte'];

        // Upload file quyết toán mới nếu có
        if ($request->hasFile('filequyettoan')) {
            // Xóa file cũ
            if ($quyettoan->filequyettoan) {
                Storage::disk('public')->delete($quyettoan->filequyettoan);
            }
            
            $file = $request->file('filequyettoan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('quyettoan', $filename, 'public');
            $validated['filequyettoan'] = $path;
        }

        DB::table('quyettoan')->where('maquyettoan', $id)->update($validated);

        return redirect()->route('giangvien.quyettoan.show', $id)
            ->with('success', 'Cập nhật quyết toán thành công!');
    }

    /**
     * Xóa quyết toán
     */
    public function destroy($id)
    {
        $quyettoan = DB::table('quyettoan')->where('maquyettoan', $id)->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Chỉ cho phép xóa nếu đang ở trạng thái Draft
        if ($quyettoan->trangthai != 'Draft') {
            return back()->with('error', 'Không thể xóa quyết toán đã được nộp hoặc duyệt!');
        }

        // Xóa file quyết toán
        if ($quyettoan->filequyettoan) {
            Storage::disk('public')->delete($quyettoan->filequyettoan);
        }

        DB::table('quyettoan')->where('maquyettoan', $id)->delete();

        return redirect()->route('giangvien.quyettoan.index')
            ->with('success', 'Xóa quyết toán thành công!');
    }

    /**
     * Nộp quyết toán để duyệt
     */
    public function submit($id)
    {
        $quyettoan = DB::table('quyettoan')->where('maquyettoan', $id)->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        if ($quyettoan->trangthai != 'Draft') {
            return back()->with('error', 'Quyết toán này đã được nộp!');
        }

        DB::table('quyettoan')->where('maquyettoan', $id)->update([
            'trangthai' => 'Pending',
        ]);

        return redirect()->route('giangvien.quyettoan.show', $id)
            ->with('success', 'Đã nộp quyết toán để duyệt!');
    }

    /**
     * Duyệt quyết toán (chỉ trưởng bộ môn)
     */
    public function approve(Request $request, $id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Kiểm tra quyết toán
        $quyettoan = DB::table('quyettoan as qt')
            ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
            ->join('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('qt.maquyettoan', $id)
            ->select('qt.*', 'bm.matruongbomon')
            ->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Kiểm tra quyền trưởng bộ môn
        if ($quyettoan->matruongbomon != $giangvien->magiangvien) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền duyệt quyết toán!');
        }

        // Kiểm tra trạng thái
        if ($quyettoan->trangthai != 'Pending') {
            return back()->with('error', 'Quyết toán này không ở trạng thái chờ duyệt!');
        }

        DB::table('quyettoan')->where('maquyettoan', $id)->update([
            'trangthai' => 'Approved',
            'nguoiduyet' => $giangvien->magiangvien,
        ]);

        return redirect()->route('giangvien.quyettoan.show', $id)
            ->with('success', 'Duyệt quyết toán thành công!');
    }

    /**
     * Từ chối quyết toán (chỉ trưởng bộ môn)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'lydotuchoi' => 'required|string|min:10',
        ], [
            'lydotuchoi.required' => 'Vui lòng nhập lý do từ chối',
            'lydotuchoi.min' => 'Lý do từ chối phải có ít nhất 10 ký tự',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Kiểm tra quyết toán
        $quyettoan = DB::table('quyettoan as qt')
            ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
            ->join('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('qt.maquyettoan', $id)
            ->select('qt.*', 'bm.matruongbomon')
            ->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Kiểm tra quyền trưởng bộ môn
        if ($quyettoan->matruongbomon != $giangvien->magiangvien) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền từ chối quyết toán!');
        }

        // Kiểm tra trạng thái
        if ($quyettoan->trangthai != 'Pending') {
            return back()->with('error', 'Quyết toán này không ở trạng thái chờ duyệt!');
        }

        DB::table('quyettoan')->where('maquyettoan', $id)->update([
            'trangthai' => 'Rejected',
            'nguoiduyet' => $giangvien->magiangvien,
            'ghichu' => ($quyettoan->ghichu ? $quyettoan->ghichu . "\n\n" : '') . 
                        "LÝ DO TỪ CHỐI: " . $request->lydotuchoi,
        ]);

        return redirect()->route('giangvien.quyettoan.show', $id)
            ->with('success', 'Đã từ chối quyết toán!');
    }

    /**
     * Lấy thống kê quyết toán
     */
    public function statistics()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $stats = [
            'total' => DB::table('quyettoan as qt')
                ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->count(),
            'draft' => DB::table('quyettoan as qt')
                ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('qt.trangthai', 'Draft')
                ->count(),
            'pending' => DB::table('quyettoan as qt')
                ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('qt.trangthai', 'Pending')
                ->count(),
            'approved' => DB::table('quyettoan as qt')
                ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('qt.trangthai', 'Approved')
                ->count(),
            'rejected' => DB::table('quyettoan as qt')
                ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('qt.trangthai', 'Rejected')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export quyết toán ra PDF
     */
    public function export($id)
    {
        $quyettoan = DB::table('quyettoan as qt')
            ->join('cuocthi as ct', 'qt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv_nguoilap', 'qt.nguoilap', '=', 'gv_nguoilap.magiangvien')
            ->leftJoin('nguoidung as nd_nguoilap', 'gv_nguoilap.manguoidung', '=', 'nd_nguoilap.manguoidung')
            ->where('qt.maquyettoan', $id)
            ->select(
                'qt.*',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'bm.tenbomon',
                'nd_nguoilap.hoten as tennguoilap'
            )
            ->first();

        if (!$quyettoan) {
            abort(404, 'Không tìm thấy quyết toán');
        }

        // Lấy danh sách chi phí
        $chiphis = DB::table('chiphi')
            ->where('macuocthi', $quyettoan->macuocthi)
            ->where('trangthai', 'Approved')
            ->get();

        // Tạo PDF
        $pdf = PDF::loadView('giangvien.quyettoan.pdf', compact('quyettoan', 'chiphis'));
        
        return $pdf->download('quyet-toan-' . $quyettoan->maquyettoan . '.pdf');
    }

    /**
     * Download file quyết toán
     */
    public function downloadFile($id)
    {
        $quyettoan = DB::table('quyettoan')->where('maquyettoan', $id)->first();

        if (!$quyettoan || !$quyettoan->filequyettoan) {
            abort(404, 'Không tìm thấy file quyết toán');
        }

        $filePath = storage_path('app/public/' . $quyettoan->filequyettoan);
        
        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại');
        }

        return response()->download($filePath);
    }

    /**
     * Tính toán tự động từ chi phí
     */
    public function autoCalculate($macuocthi)
    {
        $tongDuTru = DB::table('chiphi')
            ->where('macuocthi', $macuocthi)
            ->where('trangthai', 'Approved')
            ->sum('dutruchiphi');

        $tongThucTe = DB::table('chiphi')
            ->where('macuocthi', $macuocthi)
            ->where('trangthai', 'Approved')
            ->sum('thuctechi');

        return response()->json([
            'tongdutru' => $tongDuTru,
            'tongthucte' => $tongThucTe,
            'chenhlech' => $tongDuTru - $tongThucTe,
        ]);
    }
}