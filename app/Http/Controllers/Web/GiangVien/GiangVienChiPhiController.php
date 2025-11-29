<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GiangVienChiPhiController extends Controller
{
    /**
     * Danh sách chi phí
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

        // Query chi phí
        $query = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('giangvien as gv_yeucau', 'cp.nguoiyeucau', '=', 'gv_yeucau.magiangvien')
            ->leftJoin('nguoidung as nd_yeucau', 'gv_yeucau.manguoidung', '=', 'nd_yeucau.manguoidung')
            ->leftJoin('giangvien as gv_duyet', 'cp.nguoiduyet', '=', 'gv_duyet.magiangvien')
            ->leftJoin('nguoidung as nd_duyet', 'gv_duyet.manguoidung', '=', 'nd_duyet.manguoidung')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->select(
                'cp.machiphi',
                'cp.macuocthi',
                'cp.tenkhoanchi',
                'cp.dutruchiphi',
                'cp.thuctechi',
                'cp.ngaychi',
                'cp.nguoiyeucau',
                'cp.ngayyeucau',
                'cp.nguoiduyet',
                'cp.ngayduyet',
                'cp.trangthai',
                'cp.chungtu',
                'cp.ghichu',
                'ct.tencuocthi',
                'nd_yeucau.hoten as tennguoiyeucau',
                'nd_duyet.hoten as tennguoiduyet'
            );

        // Nếu không phải trưởng bộ môn, chỉ xem chi phí của mình
        if (!$isTruongBoMon) {
            $query->where('cp.nguoiyeucau', $giangvien->magiangvien);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cp.tenkhoanchi', 'ILIKE', "%{$search}%")
                  ->orWhere('ct.tencuocthi', 'ILIKE', "%{$search}%")
                  ->orWhere('nd_yeucau.hoten', 'ILIKE', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('cp.trangthai', $request->status);
        }

        // Lọc theo cuộc thi
        if ($request->filled('macuocthi')) {
            $query->where('cp.macuocthi', $request->macuocthi);
        }

        // Lọc theo người yêu cầu (chỉ trưởng bộ môn mới có filter này)
        if ($isTruongBoMon && $request->filled('nguoiyeucau')) {
            $query->where('cp.nguoiyeucau', $request->nguoiyeucau);
        }

        $chiphis = $query->orderBy('cp.ngayyeucau', 'desc')->paginate(10);

        // Lấy danh sách cuộc thi để filter
        $cuocthis = DB::table('cuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->select('macuocthi', 'tencuocthi')
            ->orderBy('tencuocthi')
            ->get();

        // Lấy danh sách giảng viên trong bộ môn (cho filter của trưởng bộ môn)
        $giangviens = [];
        if ($isTruongBoMon) {
            $giangviens = DB::table('giangvien as gv')
                ->join('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
                ->where('gv.mabomon', $giangvien->mabomon)
                ->select('gv.magiangvien', 'nd.hoten')
                ->orderBy('nd.hoten')
                ->get();
        }

        return view('giangvien.chiphi.index', compact('chiphis', 'cuocthis', 'giangvien', 'isTruongBoMon', 'giangviens'));
    }

    /**
     * Hiển thị form tạo chi phí
     */
    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        // Lấy danh sách cuộc thi đã được duyệt của bộ môn
        $cuocthis = DB::table('cuocthi as ct')
            ->join('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->where('kh.trangthaiduyet', 'Approved')
            ->select('ct.macuocthi', 'ct.tencuocthi', 'ct.dutrukinhphi')
            ->get();
        
        return view('giangvien.chiphi.create', compact('giangvien', 'cuocthis'));
    }

    /**
     * Lưu chi phí mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'tenkhoanchi' => 'required|string|max:300',
            'dutruchiphi' => 'required|numeric|min:0',
            // Không cho nhập thuctechi và ngaychi khi tạo mới
            'chungtu' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ghichu' => 'nullable|string',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        DB::beginTransaction();
        try {
            // Tạo mã chi phí tự động
            $lastChiPhi = DB::table('chiphi')
                ->where('machiphi', 'LIKE', 'CP%')
                ->orderByRaw('CAST(SUBSTRING(machiphi FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastChiPhi && preg_match('/CP(\d+)/', $lastChiPhi->machiphi, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $validated['machiphi'] = 'CP' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $validated['trangthai'] = 'Pending';
            $validated['nguoiyeucau'] = $giangvien->magiangvien; // Thêm người yêu cầu
            $validated['ngayyeucau'] = Carbon::now()->toDateString(); // Thêm ngày yêu cầu

            // Upload chứng từ nếu có
            if ($request->hasFile('chungtu')) {
                $file = $request->file('chungtu');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('chungtu', $filename, 'public');
                $validated['chungtu'] = $path;
            }

            DB::table('chiphi')->insert($validated);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo chi phí. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.chiphi.index')
            ->with('success', 'Tạo chi phí thành công! Đang chờ duyệt.');
    }

    /**
     * Chi tiết chi phí
     */
    public function show($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $chiphi = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv_yeucau', 'cp.nguoiyeucau', '=', 'gv_yeucau.magiangvien')
            ->leftJoin('nguoidung as nd_yeucau', 'gv_yeucau.manguoidung', '=', 'nd_yeucau.manguoidung')
            ->leftJoin('giangvien as gv_duyet', 'cp.nguoiduyet', '=', 'gv_duyet.magiangvien')
            ->leftJoin('nguoidung as nd_duyet', 'gv_duyet.manguoidung', '=', 'nd_duyet.manguoidung')
            ->where('cp.machiphi', $id)
            ->select(
                'cp.*',
                'ct.tencuocthi',
                'ct.dutrukinhphi',
                'ct.mabomon',
                'bm.tenbomon',
                'bm.matruongbomon',
                'nd_yeucau.hoten as tennguoiyeucau',
                'nd_yeucau.email as emailnguoiyeucau',
                'nd_duyet.hoten as tennguoiduyet',
                'nd_duyet.email as emailnguoiduyet'
            )
            ->first();

        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Kiểm tra quyền xem: phải cùng bộ môn
        if ($chiphi->mabomon != $giangvien->mabomon) {
            abort(403, 'Bạn không có quyền xem chi phí này');
        }

        // Kiểm tra xem có phải trưởng bộ môn không
        $isTruongBoMon = ($chiphi->matruongbomon == $giangvien->magiangvien);
        
        // Kiểm tra xem có phải người tạo không
        $isOwner = ($chiphi->nguoiyeucau == $giangvien->magiangvien);

        return view('giangvien.chiphi.show', compact('chiphi', 'giangvien', 'isTruongBoMon', 'isOwner'));
    }

    /**
     * Hiển thị form chỉnh sửa chi phí
     */
    public function edit($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $chiphi = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->where('cp.machiphi', $id)
            ->select('cp.*', 'ct.tencuocthi')
            ->first();
        
        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Chỉ người tạo mới được sửa
        if ($chiphi->nguoiyeucau != $giangvien->magiangvien) {
            return redirect()->route('giangvien.chiphi.show', $id)
                ->with('error', 'Bạn không có quyền chỉnh sửa chi phí này!');
        }

        // Cho phép sửa nếu: Pending, Rejected, hoặc Approved (để cập nhật thực tế chi)
        if (!in_array($chiphi->trangthai, ['Pending', 'Rejected', 'Approved'])) {
            return redirect()->route('giangvien.chiphi.show', $id)
                ->with('error', 'Không thể chỉnh sửa chi phí này!');
        }

        return view('giangvien.chiphi.edit', compact('chiphi'));
    }

    /**
     * Cập nhật chi phí
     */
    public function update(Request $request, $id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $chiphi = DB::table('chiphi')->where('machiphi', $id)->first();

        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Chỉ người tạo mới được sửa
        if ($chiphi->nguoiyeucau != $giangvien->magiangvien) {
            return redirect()->route('giangvien.chiphi.show', $id)
                ->with('error', 'Bạn không có quyền chỉnh sửa chi phí này!');
        }

        // Xác định validation rules dựa trên trạng thái
        if ($chiphi->trangthai == 'Approved') {
            // Khi đã duyệt: CHỈ cho cập nhật thực tế chi & ngày chi
            $validated = $request->validate([
                'thuctechi' => 'required|numeric|min:0',
                'ngaychi' => 'required|date',
                'chungtu' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'ghichu' => 'nullable|string',
            ]);
        } elseif (in_array($chiphi->trangthai, ['Pending', 'Rejected'])) {
            // Khi Pending/Rejected: CHỈ cho sửa dự trù
            $validated = $request->validate([
                'tenkhoanchi' => 'required|string|max:300',
                'dutruchiphi' => 'required|numeric|min:0',
                'chungtu' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'ghichu' => 'nullable|string',
            ]);
        } else {
            return redirect()->route('giangvien.chiphi.show', $id)
                ->with('error', 'Không thể chỉnh sửa chi phí này!');
        }

        // Upload chứng từ mới nếu có
        if ($request->hasFile('chungtu')) {
            // Xóa file cũ
            if ($chiphi->chungtu) {
                Storage::disk('public')->delete($chiphi->chungtu);
            }
            
            $file = $request->file('chungtu');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chungtu', $filename, 'public');
            $validated['chungtu'] = $path;
        }

        // Nếu đang ở trạng thái Rejected, chuyển về Pending khi cập nhật
        if ($chiphi->trangthai == 'Rejected') {
            $validated['trangthai'] = 'Pending';
            $validated['nguoiduyet'] = null; // Reset người duyệt
            $validated['ngayduyet'] = null;  // Reset ngày duyệt
        }

        DB::table('chiphi')->where('machiphi', $id)->update($validated);

        $message = $chiphi->trangthai == 'Approved' 
            ? 'Cập nhật thực tế chi thành công!' 
            : 'Cập nhật chi phí thành công!';

        return redirect()->route('giangvien.chiphi.show', $id)
            ->with('success', $message);
    }

    /**
     * Xóa chi phí
     */
    public function destroy($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $chiphi = DB::table('chiphi')->where('machiphi', $id)->first();

        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Chỉ người tạo mới được xóa
        if ($chiphi->nguoiyeucau != $giangvien->magiangvien) {
            return back()->with('error', 'Bạn không có quyền xóa chi phí này!');
        }

        // Chỉ cho phép xóa nếu đang ở trạng thái Pending hoặc Rejected
        if (!in_array($chiphi->trangthai, ['Pending', 'Rejected', 'Approved'])) {
            return back()->with('error', 'Không thể xóa chi phí đã được duyệt!');
        }

        // Xóa file chứng từ
        if ($chiphi->chungtu) {
            Storage::disk('public')->delete($chiphi->chungtu);
        }

        DB::table('chiphi')->where('machiphi', $id)->delete();

        return redirect()->route('giangvien.chiphi.index')
            ->with('success', 'Xóa chi phí thành công!');
    }

    /**
     * Duyệt chi phí (chỉ trưởng bộ môn)
     */
    public function approve(Request $request, $id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Kiểm tra chi phí
        $chiphi = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->join('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('cp.machiphi', $id)
            ->select('cp.*', 'bm.matruongbomon')
            ->first();

        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Kiểm tra quyền trưởng bộ môn
        if ($chiphi->matruongbomon != $giangvien->magiangvien) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền duyệt chi phí!');
        }

        // Không thể tự duyệt chi phí của mình
        if ($chiphi->nguoiyeucau == $giangvien->magiangvien) {
            return back()->with('error', 'Bạn không thể tự duyệt chi phí của chính mình!');
        }

        // Kiểm tra trạng thái
        if ($chiphi->trangthai != 'Pending') {
            return back()->with('error', 'Chi phí này không ở trạng thái chờ duyệt!');
        }

        DB::table('chiphi')->where('machiphi', $id)->update([
            'trangthai' => 'Approved',
            'nguoiduyet' => $giangvien->magiangvien,
            'ngayduyet' => Carbon::now()->toDateString(),
        ]);

        return redirect()->route('giangvien.chiphi.show', $id)
            ->with('success', 'Duyệt chi phí thành công!');
    }

    /**
     * Từ chối chi phí (chỉ trưởng bộ môn)
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

        // Kiểm tra chi phí
        $chiphi = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->join('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('cp.machiphi', $id)
            ->select('cp.*', 'bm.matruongbomon')
            ->first();

        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Kiểm tra quyền trưởng bộ môn
        if ($chiphi->matruongbomon != $giangvien->magiangvien) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền từ chối chi phí!');
        }

        // Kiểm tra trạng thái
        if ($chiphi->trangthai != 'Pending') {
            return back()->with('error', 'Chi phí này không ở trạng thái chờ duyệt!');
        }

        DB::table('chiphi')->where('machiphi', $id)->update([
            'trangthai' => 'Rejected',
            'nguoiduyet' => $giangvien->magiangvien,
            'ngayduyet' => Carbon::now()->toDateString(),
            'ghichu' => ($chiphi->ghichu ? $chiphi->ghichu . "\n\n" : '') . 
                        "LÝ DO TỪ CHỐI: " . $request->lydotuchoi,
        ]);

        return redirect()->route('giangvien.chiphi.show', $id)
            ->with('success', 'Đã từ chối chi phí!');
    }

    /**
     * Lấy thống kê chi phí
     */
    public function statistics()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Kiểm tra xem có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        // Base query
        $baseQuery = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->where('ct.mabomon', $giangvien->mabomon);

        // Nếu không phải trưởng bộ môn, chỉ thống kê chi phí của mình
        if (!$isTruongBoMon) {
            $baseQuery->where('cp.nguoiyeucau', $giangvien->magiangvien);
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('cp.trangthai', 'Pending')->count(),
            'approved' => (clone $baseQuery)->where('cp.trangthai', 'Approved')->count(),
            'rejected' => (clone $baseQuery)->where('cp.trangthai', 'Rejected')->count(),
            'tongdutru' => (clone $baseQuery)->sum('cp.dutruchiphi'),
            'tongthucte' => (clone $baseQuery)->sum('cp.thuctechi'),
        ];

        // Nếu là trưởng bộ môn, thêm thống kê theo người yêu cầu
        if ($isTruongBoMon) {
            $stats['by_requester'] = DB::table('chiphi as cp')
                ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
                ->join('giangvien as gv', 'cp.nguoiyeucau', '=', 'gv.magiangvien')
                ->join('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->select(
                    'gv.magiangvien',
                    'nd.hoten',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN cp.trangthai = \'Pending\' THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN cp.trangthai = \'Approved\' THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(cp.dutruchiphi) as tongdutru'),
                    DB::raw('SUM(cp.thuctechi) as tongthucte')
                )
                ->groupBy('gv.magiangvien', 'nd.hoten')
                ->get();
        }

        return response()->json($stats);
    }

    /**
     * Cập nhật thực tế chi (sau khi đã duyệt)
     */
    public function updateThucTeChi(Request $request, $id)
    {
        $validated = $request->validate([
            'thuctechi' => 'required|numeric|min:0',
            'ngaychi' => 'required|date',
            'chungtu' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ghichu' => 'nullable|string',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $chiphi = DB::table('chiphi')->where('machiphi', $id)->first();

        if (!$chiphi) {
            abort(404, 'Không tìm thấy chi phí');
        }

        // Chỉ người tạo mới được cập nhật thực tế chi
        if ($chiphi->nguoiyeucau != $giangvien->magiangvien) {
            return back()->with('error', 'Bạn không có quyền cập nhật chi phí này!');
        }

        // Chỉ cho phép cập nhật nếu đã được duyệt
        if ($chiphi->trangthai != 'Approved') {
            return back()->with('error', 'Chỉ có thể cập nhật thực tế chi sau khi được duyệt!');
        }

        // Upload chứng từ mới nếu có
        if ($request->hasFile('chungtu')) {
            if ($chiphi->chungtu) {
                Storage::disk('public')->delete($chiphi->chungtu);
            }
            
            $file = $request->file('chungtu');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chungtu', $filename, 'public');
            $validated['chungtu'] = $path;
        }

        DB::table('chiphi')->where('machiphi', $id)->update($validated);

        return redirect()->route('giangvien.chiphi.show', $id)
            ->with('success', 'Cập nhật thực tế chi thành công!');
    }

    /**
     * Download chứng từ
     */
    public function downloadChungTu($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $chiphi = DB::table('chiphi as cp')
            ->join('cuocthi as ct', 'cp.macuocthi', '=', 'ct.macuocthi')
            ->join('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('cp.machiphi', $id)
            ->select('cp.*', 'bm.matruongbomon')
            ->first();

        if (!$chiphi || !$chiphi->chungtu) {
            abort(404, 'Không tìm thấy chứng từ');
        }

        // Kiểm tra quyền: chỉ người tạo hoặc trưởng bộ môn mới được download
        $isTruongBoMon = ($chiphi->matruongbomon == $giangvien->magiangvien);
        $isOwner = ($chiphi->nguoiyeucau == $giangvien->magiangvien);

        if (!$isTruongBoMon && !$isOwner) {
            abort(403, 'Bạn không có quyền tải chứng từ này');
        }

        $filePath = storage_path('app/public/' . $chiphi->chungtu);
        
        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại');
        }

        return response()->download($filePath);
    }
}