<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\PhanCongGiangVien;
use App\Models\CuocThi;
use App\Models\Ban;
use App\Models\CongViec;

class GiangVienPhanCongController extends Controller
{
    /**
     * Hiển thị danh sách phân công
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra xem có phải trưởng bộ môn không
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        // Query phân công với relationships
        $query = PhanCongGiangVien::with(['ban', 'congviec', 'giangvien.nguoiDung']);

        // Nếu là trưởng bộ môn, hiển thị tất cả phân công trong bộ môn
        if ($isTruongBoMon) {
            $query->whereHas('giangvien', function($q) use ($giangvien) {
                $q->where('mabomon', $giangvien->mabomon);
            });
        } else {
            // Nếu là giảng viên thường, chỉ hiển thị phân công của mình
            $query->where('magiangvien', $giangvien->magiangvien);
        }

        // Tìm kiếm theo vai trò hoặc tên giảng viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vaitro', 'like', "%{$search}%")
                  ->orWhereHas('giangvien.nguoiDung', function($subQ) use ($search) {
                      $subQ->where('hoten', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo công việc
        if ($request->filled('congviec')) {
            $query->where('macongviec', $request->congviec);
        }

        // Lọc theo ban
        if ($request->filled('ban')) {
            $query->where('maban', $request->ban);
        }

        // Lọc theo giảng viên (chỉ trưởng bộ môn)
        if ($isTruongBoMon && $request->filled('giangvien_filter')) {
            $query->where('magiangvien', $request->giangvien_filter);
        }

        // Lọc theo thời gian
        if ($request->filled('from_date')) {
            $query->whereDate('ngayphancong', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('ngayphancong', '<=', $request->to_date);
        }
        
        $phanCongList = $query->orderBy('ngayphancong', 'desc')->paginate(10);

        // Lấy danh sách công việc và ban cho filter
        $congViecList = CongViec::orderBy('tencongviec')->get();
        $banList = Ban::orderBy('tenban')->get();
        
        // Lấy danh sách giảng viên trong bộ môn (cho trưởng bộ môn)
        $giangVienList = collect();
        if ($isTruongBoMon) {
            $giangVienList = GiangVien::with('nguoiDung')
                ->where('mabomon', $giangvien->mabomon)
                ->get();
        }
        
        return view('giangvien.phancong.index', compact(
            'phanCongList', 
            'giangvien', 
            'congViecList', 
            'banList', 
            'isTruongBoMon',
            'giangVienList'
        ));
    }

    /**
     * Hiển thị form tạo phân công (chỉ trưởng bộ môn)
     */
    public function create()
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return redirect()
                ->route('giangvien.phancong.index')
                ->with('error', 'Chỉ trưởng bộ môn mới có quyền phân công!');
        }

        // Lấy danh sách giảng viên trong bộ môn
        $giangVienList = GiangVien::with('nguoiDung')
            ->where('mabomon', $giangvien->mabomon)
            ->get();

        // Lấy danh sách cuộc thi của bộ môn
        $cuocThiList = CuocThi::where('mabomon', $giangvien->mabomon)
            ->orderBy('tencuocthi')
            ->get();

        // Lấy danh sách công việc
        $congViecList = CongViec::with('cuocthi')
            ->whereHas('cuocthi', function($q) use ($giangvien) {
                $q->where('mabomon', $giangvien->mabomon);
            })
            ->orderBy('tencongviec')
            ->get();

        // Lấy danh sách ban
        $banList = Ban::whereHas('cuocthi', function($q) use ($giangvien) {
            $q->where('mabomon', $giangvien->mabomon);
        })
        ->orderBy('tenban')
        ->get();

        return view('giangvien.phancong.create', compact(
            'giangvien',
            'giangVienList',
            'cuocThiList',
            'congViecList',
            'banList'
        ));
    }

    /**
     * Lưu phân công mới (chỉ trưởng bộ môn)
     */
    public function store(Request $request)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền phân công!');
        }

        $validated = $request->validate([
            'magiangvien' => 'required|exists:giangvien,magiangvien',
            'macongviec' => 'required|exists:congviec,macongviec',
            'maban' => 'required|exists:ban,maban',
            'vaitro' => 'required|string|max:100',
            'ngayphancong' => 'required|date',
        ], [
            'magiangvien.required' => 'Vui lòng chọn giảng viên',
            'macongviec.required' => 'Vui lòng chọn công việc',
            'maban.required' => 'Vui lòng chọn ban',
            'vaitro.required' => 'Vui lòng nhập vai trò',
            'ngayphancong.required' => 'Vui lòng chọn ngày phân công',
        ]);

        // Kiểm tra giảng viên có trong bộ môn không
        $giangVienTarget = GiangVien::findOrFail($validated['magiangvien']);
        if ($giangVienTarget->mabomon != $giangvien->mabomon) {
            return back()->withInput()->with('error', 'Chỉ được phân công cho giảng viên trong bộ môn!');
        }

        DB::beginTransaction();
        try {
            // Tạo mã phân công tự động
            $lastPhanCong = PhanCongGiangVien::where('maphancong', 'LIKE', 'PC%')
                ->orderByRaw('CAST(SUBSTRING(maphancong FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastPhanCong && preg_match('/PC(\d+)/', $lastPhanCong->maphancong, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $validated['maphancong'] = 'PC' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            PhanCongGiangVien::create($validated);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi phân công. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.phancong.index')
            ->with('success', 'Phân công thành công!');
    }

    /**
     * Hiển thị chi tiết phân công
     */
    public function show($id)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();
        
        $phanCong = PhanCongGiangVien::with(['ban', 'congviec', 'giangvien.nguoiDung'])
            ->findOrFail($id);

        // Kiểm tra quyền xem
        if (!$isTruongBoMon && $phanCong->magiangvien != $giangvien->magiangvien) {
            return redirect()
                ->route('giangvien.phancong.index')
                ->with('error', 'Bạn không có quyền xem phân công này!');
        }
        
        return view('giangvien.phancong.show', compact('phanCong', 'giangvien', 'isTruongBoMon'));
    }

    /**
     * Hiển thị form chỉnh sửa phân công (chỉ trưởng bộ môn)
     */
    public function edit($id)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return redirect()
                ->route('giangvien.phancong.index')
                ->with('error', 'Chỉ trưởng bộ môn mới có quyền chỉnh sửa phân công!');
        }

        $phanCong = PhanCongGiangVien::with(['ban', 'congviec', 'giangvien.nguoiDung'])
            ->findOrFail($id);

        // Lấy danh sách giảng viên trong bộ môn
        $giangVienList = GiangVien::with('nguoiDung')
            ->where('mabomon', $giangvien->mabomon)
            ->get();

        // Lấy danh sách công việc
        $congViecList = CongViec::with('cuocthi')
            ->whereHas('cuocthi', function($q) use ($giangvien) {
                $q->where('mabomon', $giangvien->mabomon);
            })
            ->orderBy('tencongviec')
            ->get();

        // Lấy danh sách ban
        $banList = Ban::whereHas('cuocthi', function($q) use ($giangvien) {
            $q->where('mabomon', $giangvien->mabomon);
        })
        ->orderBy('tenban')
        ->get();

        return view('giangvien.phancong.edit', compact(
            'phanCong',
            'giangvien',
            'giangVienList',
            'congViecList',
            'banList'
        ));
    }

    /**
     * Cập nhật phân công (chỉ trưởng bộ môn)
     */
    public function update(Request $request, $id)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền cập nhật phân công!');
        }

        $phanCong = PhanCongGiangVien::findOrFail($id);

        $validated = $request->validate([
            'magiangvien' => 'required|exists:giangvien,magiangvien',
            'macongviec' => 'required|exists:congviec,macongviec',
            'maban' => 'required|exists:ban,maban',
            'vaitro' => 'required|string|max:100',
            'ngayphancong' => 'required|date',
        ], [
            'magiangvien.required' => 'Vui lòng chọn giảng viên',
            'macongviec.required' => 'Vui lòng chọn công việc',
            'maban.required' => 'Vui lòng chọn ban',
            'vaitro.required' => 'Vui lòng nhập vai trò',
            'ngayphancong.required' => 'Vui lòng chọn ngày phân công',
        ]);

        // Kiểm tra giảng viên có trong bộ môn không
        $giangVienTarget = GiangVien::findOrFail($validated['magiangvien']);
        if ($giangVienTarget->mabomon != $giangvien->mabomon) {
            return back()->withInput()->with('error', 'Chỉ được phân công cho giảng viên trong bộ môn!');
        }

        $phanCong->update($validated);

        return redirect()->route('giangvien.phancong.show', $id)
            ->with('success', 'Cập nhật phân công thành công!');
    }

    /**
     * Xóa phân công (chỉ trưởng bộ môn)
     */
    public function destroy($id)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền xóa phân công!');
        }

        $phanCong = PhanCongGiangVien::findOrFail($id);
        $phanCong->delete();

        return redirect()->route('giangvien.phancong.index')
            ->with('success', 'Xóa phân công thành công!');
    }

    /**
     * API: Thống kê phân công
     */
    public function statistics()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if ($isTruongBoMon) {
            // Thống kê toàn bộ môn
            $stats = [
                'tong_cong_viec' => PhanCongGiangVien::whereHas('giangvien', function($q) use ($giangvien) {
                    $q->where('mabomon', $giangvien->mabomon);
                })->count(),
                'theo_ban' => PhanCongGiangVien::whereHas('giangvien', function($q) use ($giangvien) {
                    $q->where('mabomon', $giangvien->mabomon);
                })
                    ->select('maban', DB::raw('count(*) as total'))
                    ->groupBy('maban')
                    ->with('ban')
                    ->get(),
                'theo_giangvien' => PhanCongGiangVien::whereHas('giangvien', function($q) use ($giangvien) {
                    $q->where('mabomon', $giangvien->mabomon);
                })
                    ->select('magiangvien', DB::raw('count(*) as total'))
                    ->groupBy('magiangvien')
                    ->with('giangvien.nguoiDung')
                    ->get(),
            ];
        } else {
            // Thống kê cá nhân
            $stats = [
                'tong_cong_viec' => PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)->count(),
                'theo_ban' => PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
                    ->select('maban', DB::raw('count(*) as total'))
                    ->groupBy('maban')
                    ->with('ban')
                    ->get(),
            ];
        }

        return response()->json($stats);
    }

    /**
     * Export danh sách phân công ra Excel
     */
    public function export(Request $request)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        $query = PhanCongGiangVien::with(['ban', 'congviec', 'giangvien.nguoiDung']);

        if ($isTruongBoMon) {
            // Export toàn bộ môn
            $query->whereHas('giangvien', function($q) use ($giangvien) {
                $q->where('mabomon', $giangvien->mabomon);
            });
        } else {
            // Export cá nhân
            $query->where('magiangvien', $giangvien->magiangvien);
        }

        $phanCongList = $query->orderBy('ngayphancong', 'desc')->get();

        // Tạo file CSV
        $filename = 'phan-cong-' . ($isTruongBoMon ? 'bo-mon' : $giangvien->magiangvien) . '-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM cho UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        if ($isTruongBoMon) {
            fputcsv($handle, ['STT', 'Giảng viên', 'Công việc', 'Ban', 'Vai trò', 'Ngày phân công']);
        } else {
            fputcsv($handle, ['STT', 'Công việc', 'Ban', 'Vai trò', 'Ngày phân công']);
        }
        
        // Data
        foreach ($phanCongList as $index => $pc) {
            if ($isTruongBoMon) {
                fputcsv($handle, [
                    $index + 1,
                    $pc->giangvien->nguoiDung->hoten ?? 'N/A',
                    $pc->congviec->tencongviec ?? 'N/A',
                    $pc->ban->tenban ?? 'N/A',
                    $pc->vaitro ?? 'N/A',
                    $pc->ngayphancong ? \Carbon\Carbon::parse($pc->ngayphancong)->format('d/m/Y') : 'N/A',
                ]);
            } else {
                fputcsv($handle, [
                    $index + 1,
                    $pc->congviec->tencongviec ?? 'N/A',
                    $pc->ban->tenban ?? 'N/A',
                    $pc->vaitro ?? 'N/A',
                    $pc->ngayphancong ? \Carbon\Carbon::parse($pc->ngayphancong)->format('d/m/Y') : 'N/A',
                ]);
            }
        }
        
        fclose($handle);
        exit;
    }
}