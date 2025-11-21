<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\GiangVien;
use App\Models\KetQuaThi;
use App\Models\BaiThi;

class GiangVienChamDiemController extends Controller
{
    /**
     * Hiển thị danh sách bài cần chấm
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Query bài thi cần chấm
        $query = KetQuaThi::with(['baithi.sinhvien.nguoiDung', 'baithi.dethi.cuocthi'])
            ->where(function($q) use ($giangvien) {
                $q->whereNull('nguoichamdiem')->orWhere('nguoichamdiem', $giangvien->magiangvien);
            })
            ->whereNull('diem');

        // Tìm kiếm theo tên hoặc mã sinh viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('baithi.sinhvien', function($q) use ($search) {
                $q->where('masinhvien', 'like', "%{$search}%")
                  ->orWhereHas('nguoiDung', function($q2) use ($search) {
                      $q2->where('hoten', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo cuộc thi
        if ($request->filled('cuocthi')) {
            $query->whereHas('baithi.dethi.cuocthi', function($q) use ($request) {
                $q->where('macuocthi', $request->cuocthi);
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('trangthai')) {
            if ($request->trangthai == 'chua_cham') {
                $query->whereNull('diem');
            } elseif ($request->trangthai == 'da_cham') {
                $query->whereNotNull('diem');
            }
        }
        
        $ketquaList = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('giangvien.chamdiem.index', compact('ketquaList', 'giangvien'));
    }

    /**
     * Hiển thị form chấm điểm chi tiết
     */
    public function show($id)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $ketqua = KetQuaThi::with([
            'baithi.sinhvien.nguoiDung',
            'baithi.dethi.cuocthi',
            'baithi.dethi'
        ])->findOrFail($id);

        // Kiểm tra quyền chấm điểm
        if ($ketqua->nguoichamdiem && $ketqua->nguoichamdiem != $giangvien->magiangvien) {
            return redirect()
                ->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền chấm bài thi này!');
        }
        
        return view('giangvien.chamdiem.show', compact('ketqua', 'giangvien'));
    }

    /**
     * Cập nhật điểm và nhận xét
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

        $ketqua = KetQuaThi::findOrFail($id);

        // Kiểm tra quyền chấm điểm
        if ($ketqua->nguoichamdiem && $ketqua->nguoichamdiem != $giangvien->magiangvien) {
            return redirect()
                ->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền chấm bài thi này!');
        }

        // Cập nhật điểm
        $ketqua->update([
            'diem' => $validated['diem'],
            'nhanxet' => $validated['nhanxet'] ?? null,
            'nguoichamdiem' => $giangvien->magiangvien,
        ]);

        return redirect()
            ->route('giangvien.chamdiem.index')
            ->with('success', 'Chấm điểm thành công! Điểm: ' . $validated['diem'] . '/10');
    }

    /**
     * Xóa điểm đã chấm (nếu cần chấm lại)
     */
    public function destroy($id)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $ketqua = KetQuaThi::findOrFail($id);

        // Kiểm tra quyền
        if ($ketqua->nguoichamdiem != $giangvien->magiangvien) {
            return redirect()
                ->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền xóa điểm này!');
        }

        // Xóa điểm để chấm lại
        $ketqua->update([
            'diem' => null,
            'nhanxet' => null,
            'nguoichamdiem' => null,
        ]);

        return redirect()
            ->route('giangvien.chamdiem.index')
            ->with('success', 'Đã xóa điểm, có thể chấm lại!');
    }

    /**
     * Xem bài làm của sinh viên (download file)
     */
    public function downloadBaiLam($id)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $ketqua = KetQuaThi::with('baithi')->findOrFail($id);

        if (!$ketqua->baithi || !$ketqua->baithi->filepath) {
            return back()->with('error', 'Không tìm thấy file bài làm!');
        }

        $filePath = storage_path('app/public/' . $ketqua->baithi->filepath);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File không tồn tại!');
        }

        return response()->download($filePath);
    }

    /**
     * API: Lấy danh sách cuộc thi (cho filter)
     */
    public function getCuocThi()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $cuocThiList = KetQuaThi::with('baithi.dethi.cuocthi')
            ->where(function($q) use ($giangvien) {
                $q->whereNull('nguoichamdiem')->orWhere('nguoichamdiem', $giangvien->magiangvien);
            })
            ->whereHas('baithi.dethi.cuocthi')
            ->get()
            ->pluck('baithi.dethi.cuocthi')
            ->unique('macuocthi')
            ->values();

        return response()->json($cuocThiList);
    }

    /**
     * Chấm hàng loạt (bulk grading)
     */
    public function bulkUpdate(Request $request)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $validated = $request->validate([
            'ketqua_ids' => 'required|array',
            'ketqua_ids.*' => 'exists:ketquathi,maketqua',
            'diem' => 'required|numeric|min:0|max:10',
            'nhanxet' => 'nullable|string|max:1000',
        ]);

        $count = 0;
        foreach ($validated['ketqua_ids'] as $id) {
            $ketqua = KetQuaThi::find($id);
            
            // Kiểm tra quyền
            if ($ketqua && (!$ketqua->nguoichamdiem || $ketqua->nguoichamdiem == $giangvien->magiangvien)) {
                $ketqua->update([
                    'diem' => $validated['diem'],
                    'nhanxet' => $validated['nhanxet'] ?? null,
                    'nguoichamdiem' => $giangvien->magiangvien,
                ]);
                $count++;
            }
        }

        return redirect()
            ->route('giangvien.chamdiem.index')
            ->with('success', "Đã chấm {$count} bài thi thành công!");
    }
}