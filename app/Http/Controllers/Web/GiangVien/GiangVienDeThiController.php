<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GiangVienDeThiController extends Controller
{
    /**
     * Danh sách đề thi của giảng viên
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Query đề thi với JOIN cuộc thi
        $query = DB::table('dethi as dt')
            ->join('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('giangvien as gv', 'dt.nguoitao', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('dt.nguoitao', $giangvien->magiangvien)
            ->select(
                'dt.*',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'ct.thoigianbatdau',
                'ct.thoigianketthuc',
                'ct.trangthai as trangthai_cuocthi',
                'nd.hoten as nguoitao_ten'
            );

        // Tìm kiếm theo tên đề thi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('dt.tendethi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo cuộc thi
        if ($request->filled('macuocthi')) {
            $query->where('dt.macuocthi', $request->macuocthi);
        }

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            $query->where('dt.trangthai', $request->trangthai);
        }

        // Lọc theo loại đề thi
        if ($request->filled('loaidethi')) {
            $query->where('dt.loaidethi', $request->loaidethi);
        }

        $dethiList = $query->orderBy('dt.ngaytao', 'desc')->paginate(10);

        // Thêm thông tin bổ sung cho mỗi đề thi
        foreach ($dethiList->items() as $dethi) {
            $dethi->sobaithi = DB::table('baithi')
                ->where('madethi', $dethi->madethi)
                ->count();

            $dethi->status_color = $this->getStatusColor($dethi->trangthai ?? 'Draft');
            $dethi->status_label = $this->getStatusLabel($dethi->trangthai ?? 'Draft');
        }

        $cuocthiList = DB::table('cuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->whereIn('trangthai', ['Approved', 'InProgress', 'Completed'])
            ->orderBy('ngaytao', 'desc')
            ->get();

        return view('giangvien.dethi.index', compact('dethiList', 'cuocthiList', 'giangvien'));
    }

    /**
     * Form tạo đề thi
     */
    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $cuocthiList = DB::table('cuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->whereIn('trangthai', ['Approved', 'InProgress'])
            ->orderBy('thoigianbatdau', 'desc')
            ->get();

        return view('giangvien.dethi.create', compact('cuocthiList', 'giangvien'));
    }

    /**
     * Lưu đề thi mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tendethi' => 'required|string|max:255',
            'macuocthi' => 'required|string|exists:cuocthi,macuocthi',
            'loaidethi' => 'required|in:LyThuyet,ThucHanh,VietBao,Khac',
            'thoigianlambai' => 'required|integer|min:1|max:999',
            'diemtoida' => 'required|numeric|min:0|max:100',
            'trangthai' => 'required|in:Draft,Active,Archived',
            'file_dethi' => 'nullable|file|mimes:pdf,docx,doc,zip|max:20480',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Tạo mã đề thi tự động
        $lastDethi = DB::table('dethi')
            ->where('madethi', 'LIKE', 'DT%')
            ->orderByRaw("CAST(SUBSTRING(madethi FROM 3) AS INTEGER) DESC")
            ->first();
        
        if ($lastDethi && preg_match('/DT(\d+)/', $lastDethi->madethi, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            $newNumber = 1;
        }
        
        $validated['madethi'] = 'DT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        $validated['nguoitao'] = $giangvien->magiangvien;
        $validated['ngaytao'] = now();

        // Upload file nếu có
        if ($request->hasFile('file_dethi')) {
            $file = $request->file('file_dethi');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('dethi', $filename, 'public');
            $validated['filedethi'] = $path;
        }

        unset($validated['file_dethi']);

        DB::table('dethi')->insert($validated);

        return redirect()->route('giangvien.dethi.index')
            ->with('success', 'Tạo đề thi thành công!');
    }

    /**
     * Chi tiết đề thi
     */
    public function show($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $dethi = DB::table('dethi as dt')
            ->join('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('giangvien as gv', 'dt.nguoitao', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('dt.madethi', $id)
            ->select(
                'dt.*',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'ct.thoigianbatdau',
                'ct.thoigianketthuc',
                'ct.trangthai as trangthai_cuocthi',
                'nd.hoten as nguoitao_ten'
            )
            ->first();

        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền xem đề thi này');
        }

        $dethi->sobaithi = DB::table('baithi')
            ->where('madethi', $id)
            ->count();

        $baithiList = DB::table('baithi as bt')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('bt.madethi', $id)
            ->select(
                'bt.*',
                'sv.masinhvien',
                'nd.hoten as sinhvien_ten',
                'd.tendoithi'
            )
            ->orderBy('bt.thoigiannop', 'desc')
            ->get();

        $dethi->status_color = $this->getStatusColor($dethi->trangthai);
        $dethi->status_label = $this->getStatusLabel($dethi->trangthai);

        return view('giangvien.dethi.show', compact('dethi', 'baithiList'));
    }

    /**
     * Xem file đề thi (NEW METHOD)
     */
    public function viewFile($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $dethi = DB::table('dethi')->where('madethi', $id)->first();

        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền xem đề thi này');
        }

        if (!$dethi->filedethi || !Storage::disk('public')->exists($dethi->filedethi)) {
            abort(404, 'Không tìm thấy file đề thi');
        }

        $filePath = Storage::disk('public')->path($dethi->filedethi);
        $mimeType = Storage::disk('public')->mimeType($dethi->filedethi);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($dethi->filedethi) . '"'
        ]);
    }

    /**
     * Tải xuống file đề thi (NEW METHOD)
     */
    public function downloadFile($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        $dethi = DB::table('dethi')->where('madethi', $id)->first();

        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền tải đề thi này');
        }

        if (!$dethi->filedethi || !Storage::disk('public')->exists($dethi->filedethi)) {
            abort(404, 'Không tìm thấy file đề thi');
        }

        return Storage::disk('public')->download($dethi->filedethi);
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $dethi = DB::table('dethi')->where('madethi', $id)->first();
        
        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi này');
        }

        $hasBaiThi = DB::table('baithi')
            ->where('madethi', $id)
            ->exists();

        if ($hasBaiThi) {
            return redirect()->route('giangvien.dethi.show', $id)
                ->with('error', 'Không thể chỉnh sửa đề thi đã có bài thi nộp!');
        }

        $cuocthiList = DB::table('cuocthi')
            ->where('mabomon', $giangvien->mabomon)
            ->whereIn('trangthai', ['Approved', 'InProgress'])
            ->get();

        return view('giangvien.dethi.edit', compact('dethi', 'cuocthiList', 'giangvien'));
    }

    /**
     * Cập nhật đề thi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tendethi' => 'required|string|max:255',
            'macuocthi' => 'required|string|exists:cuocthi,macuocthi',
            'loaidethi' => 'required|in:LyThuyet,ThucHanh,VietBao,Khac',
            'thoigianlambai' => 'required|integer|min:1|max:999',
            'diemtoida' => 'required|numeric|min:0|max:100',
            'trangthai' => 'required|in:Draft,Active,Archived',
            'file_dethi' => 'nullable|file|mimes:pdf,docx,doc,zip|max:20480',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $dethi = DB::table('dethi')->where('madethi', $id)->first();
        
        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi này');
        }

        $hasBaiThi = DB::table('baithi')
            ->where('madethi', $id)
            ->exists();

        if ($hasBaiThi) {
            return redirect()->route('giangvien.dethi.show', $id)
                ->with('error', 'Không thể chỉnh sửa đề thi đã có bài thi nộp!');
        }

        if ($request->hasFile('file_dethi')) {
            $file = $request->file('file_dethi');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('dethi', $filename, 'public');
            $validated['filedethi'] = $path;
            
            if ($dethi->filedethi && Storage::disk('public')->exists($dethi->filedethi)) {
                Storage::disk('public')->delete($dethi->filedethi);
            }
        }

        unset($validated['file_dethi']);

        DB::table('dethi')->where('madethi', $id)->update($validated);

        return redirect()->route('giangvien.dethi.show', $id)
            ->with('success', 'Cập nhật đề thi thành công!');
    }

    /**
     * Xóa đề thi
     */
    public function destroy($id)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $dethi = DB::table('dethi')->where('madethi', $id)->first();
        
        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền xóa đề thi này');
        }

        $hasBaiThi = DB::table('baithi')->where('madethi', $id)->exists();

        if ($hasBaiThi) {
            return back()->with('error', 'Không thể xóa đề thi đã có bài thi!');
        }

        if ($dethi->filedethi && Storage::disk('public')->exists($dethi->filedethi)) {
            Storage::disk('public')->delete($dethi->filedethi);
        }

        DB::table('dethi')->where('madethi', $id)->delete();

        return redirect()->route('giangvien.dethi.index')
            ->with('success', 'Xóa đề thi thành công!');
    }

    /**
     * Helper: Lấy màu trạng thái
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'Active' => 'green',
            'Draft' => 'yellow',
            'Archived' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Helper: Lấy nhãn trạng thái
     */
    private function getStatusLabel($status)
    {
        return match($status) {
            'Active' => 'Đang hoạt động',
            'Draft' => 'Nháp',
            'Archived' => 'Đã lưu trữ',
            default => $status,
        };
    }
}