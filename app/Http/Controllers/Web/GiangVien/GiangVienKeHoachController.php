<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
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

        // Query kế hoạch của bộ môn
        $query = DB::table('kehoachcuocthi as kh')
            ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv', 'kh.nguoiduyet', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->select(
                'kh.*',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'bm.tenbomon',
                'nd.hoten as tennguoiduyet'
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ct.tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo trạng thái duyệt
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

        $kehoachs = $query->orderBy('kh.ngaynopkehoach', 'desc')->paginate(10);

        // Lấy danh sách năm học để filter
        $namhocs = DB::table('kehoachcuocthi')
            ->distinct()
            ->pluck('namhoc')
            ->sort()
            ->values();

        return view('giangvien.kehoach.index', compact('kehoachs', 'namhocs', 'giangvien'));
    }

    /**
     * Hiển thị form tạo kế hoạch
     */
    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        // Lấy danh sách cuộc thi chưa có kế hoạch của bộ môn
        $cuocthis = DB::table('cuocthi as ct')
            ->leftJoin('kehoachcuocthi as kh', 'ct.macuocthi', '=', 'kh.macuocthi')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->whereNull('kh.makehoach')
            ->select('ct.macuocthi', 'ct.tencuocthi', 'ct.loaicuocthi')
            ->get();
        
        return view('giangvien.kehoach.create', compact('giangvien', 'cuocthis'));
    }

    /**
     * Lưu kế hoạch mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'namhoc' => 'required|string',
            'hocky' => 'required|in:1,2,3',
            'ghichu' => 'nullable|string',
        ]);

        // Kiểm tra xem cuộc thi đã có kế hoạch chưa
        $existing = DB::table('kehoachcuocthi')
            ->where('macuocthi', $validated['macuocthi'])
            ->exists();

        if ($existing) {
            return back()->withInput()->with('error', 'Cuộc thi này đã có kế hoạch!');
        }

        DB::beginTransaction();
        try {
            // Tạo mã kế hoạch tự động
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
            
            $validated['makehoach'] = 'KH' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $validated['trangthaiduyet'] = 'Pending';
            $validated['ngaynopkehoach'] = now();

            DB::table('kehoachcuocthi')->insert($validated);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo kế hoạch. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.kehoach.index')
            ->with('success', 'Tạo kế hoạch thành công! Đang chờ duyệt.');
    }

    /**
     * Chi tiết kế hoạch
     */
    public function show($id)
    {
        $kehoach = DB::table('kehoachcuocthi as kh')
            ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv', 'kh.nguoiduyet', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('kh.makehoach', $id)
            ->select(
                'kh.*',
                'ct.*',
                'bm.tenbomon',
                'nd.hoten as tennguoiduyet'
            )
            ->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Lấy danh sách ban của cuộc thi
        $bans = DB::table('ban')
            ->where('macuocthi', $kehoach->macuocthi)
            ->get();

        // Lấy danh sách công việc
        $congviecs = DB::table('congviec')
            ->where('macuocthi', $kehoach->macuocthi)
            ->orderBy('thoigianbatdau')
            ->get();

        return view('giangvien.kehoach.show', compact('kehoach', 'bans', 'congviecs'));
    }

    /**
     * Hiển thị form chỉnh sửa kế hoạch
     */
    public function edit($id)
    {
        $kehoach = DB::table('kehoachcuocthi as kh')
            ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
            ->where('kh.makehoach', $id)
            ->select('kh.*', 'ct.tencuocthi', 'ct.loaicuocthi')
            ->first();
        
        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Chỉ cho phép sửa nếu đang ở trạng thái Pending hoặc Rejected
        if (!in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected'])) {
            return redirect()->route('giangvien.kehoach.show', $id)
                ->with('error', 'Không thể chỉnh sửa kế hoạch đã được duyệt!');
        }

        return view('giangvien.kehoach.edit', compact('kehoach'));
    }

    /**
     * Cập nhật kế hoạch
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'namhoc' => 'required|string',
            'hocky' => 'required|in:1,2,3',
            'ghichu' => 'nullable|string',
        ]);

        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Chỉ cho phép sửa nếu đang ở trạng thái Pending hoặc Rejected
        if (!in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected'])) {
            return redirect()->route('giangvien.kehoach.show', $id)
                ->with('error', 'Không thể chỉnh sửa kế hoạch đã được duyệt!');
        }

        // Nếu đang ở trạng thái Rejected, chuyển về Pending khi cập nhật
        if ($kehoach->trangthaiduyet == 'Rejected') {
            $validated['trangthaiduyet'] = 'Pending';
            $validated['ngayduyet'] = null;
            $validated['nguoiduyet'] = null;
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

        // Chỉ cho phép xóa nếu đang ở trạng thái Pending hoặc Rejected
        if (!in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected'])) {
            return back()->with('error', 'Không thể xóa kế hoạch đã được duyệt!');
        }

        DB::table('kehoachcuocthi')->where('makehoach', $id)->delete();

        return redirect()->route('giangvien.kehoach.index')
            ->with('success', 'Xóa kế hoạch thành công!');
    }

    /**
     * Gửi lại kế hoạch để duyệt (sau khi bị từ chối)
     */
    public function resubmit($id)
    {
        $kehoach = DB::table('kehoachcuocthi')->where('makehoach', $id)->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        if ($kehoach->trangthaiduyet != 'Rejected') {
            return back()->with('error', 'Chỉ có thể gửi lại kế hoạch đã bị từ chối!');
        }

        DB::table('kehoachcuocthi')->where('makehoach', $id)->update([
            'trangthaiduyet' => 'Pending',
            'ngayduyet' => null,
            'nguoiduyet' => null,
        ]);

        return redirect()->route('giangvien.kehoach.show', $id)
            ->with('success', 'Đã gửi lại kế hoạch để duyệt!');
    }

    /**
     * Export kế hoạch ra PDF
     */
    public function export($id)
    {
        $kehoach = DB::table('kehoachcuocthi as kh')
            ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('kh.makehoach', $id)
            ->select('kh.*', 'ct.*', 'bm.tenbomon')
            ->first();

        if (!$kehoach) {
            abort(404, 'Không tìm thấy kế hoạch');
        }

        // Lấy danh sách ban và công việc
        $bans = DB::table('ban')
            ->where('macuocthi', $kehoach->macuocthi)
            ->get();

        $congviecs = DB::table('congviec')
            ->where('macuocthi', $kehoach->macuocthi)
            ->orderBy('thoigianbatdau')
            ->get();

        // Tạo PDF (sử dụng dompdf hoặc thư viện khác)
        $pdf = PDF::loadView('giangvien.kehoach.pdf', compact('kehoach', 'bans', 'congviecs'));
        
        return $pdf->download('ke-hoach-' . $kehoach->makehoach . '.pdf');
    }

    /**
     * Lấy thống kê kế hoạch
     */
    public function statistics()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $stats = [
            'total' => DB::table('kehoachcuocthi as kh')
                ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->count(),
            'pending' => DB::table('kehoachcuocthi as kh')
                ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('kh.trangthaiduyet', 'Pending')
                ->count(),
            'approved' => DB::table('kehoachcuocthi as kh')
                ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('kh.trangthaiduyet', 'Approved')
                ->count(),
            'rejected' => DB::table('kehoachcuocthi as kh')
                ->join('cuocthi as ct', 'kh.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('kh.trangthaiduyet', 'Rejected')
                ->count(),
        ];

        return response()->json($stats);
    }
}