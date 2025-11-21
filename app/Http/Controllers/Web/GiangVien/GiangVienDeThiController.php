<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
            'tendethi'       => 'required|string|max:255',
            'macuocthi'      => 'required|string|exists:cuocthi,macuocthi',
            'loaidethi'      => 'required|in:LyThuyet,ThucHanh,VietBao,Khac',
            'thoigianlambai' => 'required|integer|min:1|max:999',
            'diemtoida'      => 'required|numeric|min:0|max:100',
            'trangthai'      => 'sometimes|in:Draft,Published,Archived', // Không bắt buộc nữa
            'file_dethi'     => 'nullable|file|mimes:pdf,docx,doc,zip|max:20480',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Tạo mã đề thi tự động
        $lastDethi = DB::table('dethi')
            ->where('madethi', 'LIKE', 'DT%')
            ->orderByRaw("CAST(SUBSTRING(madethi FROM 3) AS INTEGER) DESC")
            ->first();

        $newNumber = 1;
        if ($lastDethi && preg_match('/DT(\d+)/', $lastDethi->madethi, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        }

        $madethi = 'DT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Upload file nếu có
        $filePath = null;
        if ($request->hasFile('file_dethi')) {
            $file = $request->file('file_dethi');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('dethi', $filename, 'public');
        }

        // TỰ ĐỘNG: Nếu có file → trạng thái = Published
        // Nếu không có file → dùng trạng thái người dùng chọn, mặc định Draft
        $trangthai = $filePath ? 'Published' : ($request->trangthai ?? 'Draft');

        DB::table('dethi')->insert([
            'madethi'         => $madethi,
            'tendethi'        => $validated['tendethi'],
            'macuocthi'       => $validated['macuocthi'],
            'loaidethi'       => $validated['loaidethi'],
            'thoigianlambai'  => $validated['thoigianlambai'],
            'diemtoida'       => $validated['diemtoida'],
            'trangthai'       => $trangthai,
            'filedethi'       => $filePath,
            'nguoitao'        => $giangvien->magiangvien,
            'ngaytao'         => now(),
        ]);

        $statusLabel = $trangthai === 'Published' ? 'đã công khai' : 'nháp';

        return redirect()->route('giangvien.dethi.index')
            ->with('success', "Tạo đề thi thành công! Đề thi hiện đang ở trạng thái: <strong>$statusLabel</strong>");
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

        // SỬA: JOIN với bảng ketquathi để lấy điểm
        $baithiList = DB::table('baithi as bt')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi') // JOIN thêm bảng ketquathi
            ->where('bt.madethi', $id)
            ->select(
                'bt.*',
                'sv.masinhvien',
                'nd.hoten as sinhvien_ten',
                'd.tendoithi',
                'kq.diem as diemso' // Lấy điểm từ bảng ketquathi
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
        
        $dethi = DB::table('dethi as dt')
        ->leftJoin('giangvien as gv', 'dt.nguoitao', '=', 'gv.magiangvien')
        ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
        ->where('dt.madethi', $id)
        ->select(
            'dt.*',
            'nd.hoten as nguoitao_ten'
        )
        ->first();
        
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
            'tendethi'       => 'required|string|max:255',
            'macuocthi'      => 'required|string|exists:cuocthi,macuocthi',
            'loaidethi'      => 'required|in:LyThuyet,ThucHanh,VietBao,Khac',
            'thoigianlambai' => 'required|integer|min:1|max:999',
            'diemtoida'      => 'required|numeric|min:0|max:100',
            'trangthai'      => 'sometimes|in:Draft,Published,Archived', // Không bắt buộc nếu có file
            'file_dethi'     => 'nullable|file|mimes:pdf,docx,doc,zip|max:20480',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $dethi = DB::table('dethi')->where('madethi', $id)->first();
        if (!$dethi) {
            abort(404, 'Không tìm thấy đề thi');
        }

        if ($dethi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền chỉnh sửa đề thi này');
        }

        $hasBaiThi = DB::table('baithi')->where('madethi', $id)->exists();
        if ($hasBaiThi) {
            return redirect()->route('giangvien.dethi.show', $id)
                ->with('error', 'Không thể chỉnh sửa đề thi đã có bài thi nộp!');
        }

        // Xử lý file mới (nếu có)
        if ($request->hasFile('file_dethi')) {
            $file = $request->file('file_dethi');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('dethi', $filename, 'public');
            $validated['filedethi'] = $path;

            // Xóa file cũ
            if ($dethi->filedethi && Storage::disk('public')->exists($dethi->filedethi)) {
                Storage::disk('public')->delete($dethi->filedethi);
            }

            // TỰ ĐỘNG: Có file mới → chuyển thành Published
            $validated['trangthai'] = 'Published';
        } else {
            // Không upload file mới → dùng trạng thái người dùng chọn (mặc định giữ cũ nếu không gửi)
            $validated['trangthai'] = $request->trangthai ?? $dethi->trangthai;
        }

        // Xóa key file_dethi khỏi validated vì không có cột này
        unset($validated['file_dethi']);

        DB::table('dethi')->where('madethi', $id)->update($validated);

        $statusLabel = $validated['trangthai'] === 'Published' ? 'đã công khai' : 
                    ($validated['trangthai'] === 'Draft' ? 'nháp' : 'đã lưu trữ');

        return redirect()->route('giangvien.dethi.show', $id)
            ->with('success', "Cập nhật đề thi thành công! Trạng thái hiện tại: <strong>$statusLabel</strong>");
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
            'Published' => 'green',
            'Draft'     => 'yellow',
            'Archived'  => 'gray',
            default     => 'gray',
        };
    }

    /**
     * Helper: Lấy nhãn trạng thái
     */
    private function getStatusLabel($status)
    {
        return match($status) {
            'Published' => 'Đã công khai',
            'Draft'     => 'Nháp',
            'Archived'  => 'Đã lưu trữ',
            default     => $status,
        };
    }


    /**
     * Tải xuống file bài thi
     */
    public function downloadBaiThi($id, $baithiId)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        if (!$giangvien) {
            abort(403, 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy thông tin bài thi và kiểm tra quyền
        $baithi = DB::table('baithi as bt')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->where('bt.mabaithi', $baithiId)
            ->where('dt.madethi', $id) // Kiểm tra đúng đề thi
            ->select('bt.*', 'dt.nguoitao')
            ->first();

        if (!$baithi) {
            abort(404, 'Không tìm thấy bài thi');
        }

        // Kiểm tra giảng viên có quyền không
        if ($baithi->nguoitao != $giangvien->magiangvien) {
            abort(403, 'Bạn không có quyền tải bài thi này');
        }

        // Kiểm tra file tồn tại
        if (!$baithi->filebaithi) {
            abort(404, 'Bài thi chưa có file đính kèm');
        }

        // File path đã đúng format: baithis/filename.ext
        if (!Storage::disk('public')->exists($baithi->filebaithi)) {
            Log::error('File not found: ' . $baithi->filebaithi);
            Log::error('Full path: ' . Storage::disk('public')->path($baithi->filebaithi));
            abort(404, 'Không tìm thấy file bài thi trên server');
        }

        return Storage::disk('public')->download($baithi->filebaithi);
    }

    /**
     * Tải xuống nhiều file bài thi (nén thành ZIP)
     */
    public function downloadMultipleBaiThi(Request $request, $id)
    {
        $validated = $request->validate([
            'baithi_ids' => 'required|array|min:1',
            'baithi_ids.*' => 'required|string',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        if (!$giangvien) {
            return back()->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Kiểm tra quyền với đề thi
        $dethi = DB::table('dethi')->where('madethi', $id)->first();
        
        if (!$dethi || $dethi->nguoitao != $giangvien->magiangvien) {
            return back()->with('error', 'Bạn không có quyền tải bài thi này');
        }

        // Lấy danh sách bài thi có file
        $baithiList = DB::table('baithi as bt')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->whereIn('bt.mabaithi', $validated['baithi_ids'])
            ->where('bt.madethi', $id)
            ->whereNotNull('bt.filebaithi')
            ->select(
                'bt.mabaithi',
                'bt.filebaithi',
                'bt.loaidangky',
                'sv.masinhvien',
                'nd.hoten as sinhvien_ten',
                'd.tendoithi'
            )
            ->get();

        if ($baithiList->isEmpty()) {
            return back()->with('error', 'Không tìm thấy bài thi nào có file để tải');
        }

        // Tạo file ZIP
        $zipFileName = 'BaiThi_' . $dethi->tendethi . '_' . date('YmdHis') . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);
        
        // Tạo thư mục temp nếu chưa có
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Không thể tạo file ZIP');
        }

        $fileCount = 0;
        $missingFiles = [];

        foreach ($baithiList as $baithi) {
            if (Storage::disk('public')->exists($baithi->filebaithi)) {
                $fullPath = Storage::disk('public')->path($baithi->filebaithi);
                $extension = pathinfo($baithi->filebaithi, PATHINFO_EXTENSION);
                
                // Tạo tên file có ý nghĩa
                if ($baithi->loaidangky === 'CaNhan') {
                    $fileName = ($baithi->masinhvien ?? 'Unknown') . '_' . 
                            preg_replace('/[^A-Za-z0-9]/', '_', $baithi->sinhvien_ten ?? 'Unknown') . '.' . $extension;
                } else {
                    $fileName = preg_replace('/[^A-Za-z0-9._-]/', '_', $baithi->tendoithi ?? 'Team') . '.' . $extension;
                }
                
                $zip->addFile($fullPath, $fileName);
                $fileCount++;
            } else {
                $missingFiles[] = $baithi->mabaithi;
            }
        }

        $zip->close();

        if ($fileCount === 0) {
            unlink($zipFilePath);
            return back()->with('error', 'Không có file nào tồn tại để tải');
        }

        // Log nếu có file thiếu
        if (!empty($missingFiles)) {
            Log::warning('Missing files for bai thi: ' . implode(', ', $missingFiles));
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}