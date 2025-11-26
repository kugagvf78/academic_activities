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
use Illuminate\Support\Facades\Log;

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

        // Query phân công với relationships - THÊM cuocthi qua ban
        $query = PhanCongGiangVien::with([
            'ban.cuocthi',  // Thêm quan hệ này
            'congviec', 
            'giangvien.nguoiDung'
        ]);

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
                })
                ->orWhereHas('ban.cuocthi', function($subQ) use ($search) {
                    $subQ->where('tencuocthi', 'like', "%{$search}%");
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

        // Lọc theo cuộc thi - THÊM MỚI
        if ($request->filled('cuocthi')) {
            $query->whereHas('ban', function($q) use ($request) {
                $q->where('macuocthi', $request->cuocthi);
            });
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
        
        // Lấy danh sách cuộc thi - THÊM MỚI
        $cuocThiList = CuocThi::where('mabomon', $giangvien->mabomon)
            ->orderBy('tencuocthi')
            ->get();
        
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
            'cuocThiList',  // Thêm biến này
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
            'tencongviec' => 'required|string|max:255',
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'maban' => 'required|exists:ban,maban',
            'vaitro' => 'required|string|max:100',
            'ngayphancong' => 'nullable|date',
        ], [
            'magiangvien.required' => 'Vui lòng chọn giảng viên',
            'tencongviec.required' => 'Vui lòng chọn hoặc nhập công việc',
            'macuocthi.required' => 'Vui lòng chọn cuộc thi',
            'maban.required' => 'Vui lòng chọn ban',
            'vaitro.required' => 'Vui lòng nhập vai trò',
        ]);

        // Kiểm tra giảng viên có trong bộ môn không
        $giangVienTarget = GiangVien::findOrFail($validated['magiangvien']);
        if ($giangVienTarget->mabomon != $giangvien->mabomon) {
            return back()->withInput()->with('error', 'Chỉ được phân công cho giảng viên trong bộ môn!');
        }

        DB::beginTransaction();
        try {
            // Tìm hoặc tạo công việc
            $congViec = CongViec::firstOrCreate([
                'tencongviec' => $validated['tencongviec'],
                'macuocthi' => $validated['macuocthi']
            ], [
                // Tạo mã công việc tự động nếu chưa tồn tại
                'macongviec' => $this->generateMaCongViec(),
                'maban' => $validated['maban'],  // Thêm maban
                'mota' => null,
                'thoigianbatdau' => null,
                'thoigianketthuc' => null,
                'trangthai' => 'Pending'  // Set trạng thái mặc định
            ]);

            // Nếu công việc đã tồn tại nhưng chưa có maban, cập nhật maban
            if (!$congViec->maban && $congViec->wasRecentlyCreated === false) {
                $congViec->update(['maban' => $validated['maban']]);
            }

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
            
            $maphancong = 'PC' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Tạo phân công
            PhanCongGiangVien::create([
                'maphancong' => $maphancong,
                'magiangvien' => $validated['magiangvien'],
                'macongviec' => $congViec->macongviec,
                'maban' => $validated['maban'],
                'vaitro' => $validated['vaitro'],
                'ngayphancong' => $validated['ngayphancong'] ?? now(),
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating phan cong: ' . $e->getMessage());
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
        
        // THÊM quan hệ cuocthi qua ban
        $phanCong = PhanCongGiangVien::with([
            'ban.cuocthi',  // Thêm quan hệ này
            'congviec', 
            'giangvien.nguoiDung'
        ])->findOrFail($id);

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

        $phanCong = PhanCongGiangVien::with('ban.cuocthi')->findOrFail($id);

        $validated = $request->validate([
            'magiangvien' => 'required|exists:giangvien,magiangvien',
            'tencongviec' => 'required|string|max:255',
            'maban' => 'required|exists:ban,maban',
            'vaitro' => 'required|string|max:100',
            'ngayphancong' => 'nullable|date',
        ], [
            'magiangvien.required' => 'Vui lòng chọn giảng viên',
            'tencongviec.required' => 'Vui lòng chọn hoặc nhập công việc',
            'maban.required' => 'Vui lòng chọn ban',
            'vaitro.required' => 'Vui lòng nhập vai trò',
        ]);

        // Kiểm tra giảng viên có trong bộ môn không
        $giangVienTarget = GiangVien::findOrFail($validated['magiangvien']);
        if ($giangVienTarget->mabomon != $giangvien->mabomon) {
            return back()->withInput()->with('error', 'Chỉ được phân công cho giảng viên trong bộ môn!');
        }

        DB::beginTransaction();
        try {
            // Lấy mã cuộc thi từ ban
            $ban = Ban::findOrFail($validated['maban']);
            $macuocthi = $ban->macuocthi;

            // Tìm hoặc tạo công việc
            $congViec = CongViec::firstOrCreate([
                'tencongviec' => $validated['tencongviec'],
                'macuocthi' => $macuocthi
            ], [
                'macongviec' => $this->generateMaCongViec(),
                'maban' => $validated['maban'],  // Thêm maban
                'mota' => null,
                'thoigianbatdau' => null,
                'thoigianketthuc' => null,
                'trangthai' => 'Pending'  // Set trạng thái mặc định
            ]);

            // Nếu công việc đã tồn tại nhưng chưa có maban, cập nhật maban
            if (!$congViec->maban && $congViec->wasRecentlyCreated === false) {
                $congViec->update(['maban' => $validated['maban']]);
            }

            // Cập nhật phân công
            $phanCong->update([
                'magiangvien' => $validated['magiangvien'],
                'macongviec' => $congViec->macongviec,
                'maban' => $validated['maban'],
                'vaitro' => $validated['vaitro'],
                'ngayphancong' => $validated['ngayphancong'] ?? $phanCong->ngayphancong,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating phan cong: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại!');
        }

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
     * Helper method: Tạo mã công việc tự động
     */
    private function generateMaCongViec()
    {
        $lastCongViec = CongViec::where('macongviec', 'LIKE', 'CV%')
            ->orderByRaw('CAST(SUBSTRING(macongviec FROM 3) AS INTEGER) DESC')
            ->lockForUpdate()
            ->first();
        
        if ($lastCongViec && preg_match('/CV(\d+)/', $lastCongViec->macongviec, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            // Đếm tổng số công việc hiện có
            $count = CongViec::count();
            $newNumber = $count + 1;
        }
        
        return 'CV' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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
    /**
     * API: Lấy danh sách ban theo cuộc thi
     */
    public function getBanByCuocThi($macuocthi)
    {
        try {
            $banList = Ban::where('macuocthi', $macuocthi)
                ->orderBy('tenban')
                ->get(['maban', 'tenban', 'mota']);
            
            return response()->json($banList);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể tải danh sách ban'], 500);
        }
    }

    /**
     * API: Lấy danh sách công việc theo cuộc thi
     */
    public function getCongViecByCuocThi($macuocthi)
    {
        try {
            $congViecList = CongViec::where('macuocthi', $macuocthi)
                ->orderBy('tencongviec')
                ->get(['macongviec', 'tencongviec', 'mota']);
            
            return response()->json($congViecList);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể tải danh sách công việc'], 500);
        }
    }

    /**
     * API: Lấy thông tin chi tiết ban
     */
    public function getBanDetail($maban)
    {
        try {
            $ban = Ban::with('cuocthi')->findOrFail($maban);
            
            // Đếm số giảng viên đã phân công vào ban này
            $soLuongGiangVien = PhanCongGiangVien::where('maban', $maban)->count();
            
            return response()->json([
                'ban' => $ban,
                'so_luong_giang_vien' => $soLuongGiangVien
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể tải thông tin ban'], 500);
        }
    }

    /**
     * Hiển thị trang quản lý ban cuộc thi (cho trưởng bộ môn)
     */
    public function quanLyBan()
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
                ->with('error', 'Chỉ trưởng bộ môn mới có quyền quản lý ban!');
        }

        // Lấy danh sách cuộc thi của bộ môn
        $cuocThiList = CuocThi::where('mabomon', $giangvien->mabomon)
            ->with(['bans' => function($query) {
                $query->withCount('phancongs');
            }])
            ->orderBy('tencuocthi')
            ->get();

        return view('giangvien.phancong.quan-ly-ban', compact(
            'giangvien',
            'cuocThiList',
            'isTruongBoMon'
        ));
    }

    /**
     * Hiển thị chi tiết phân công của một ban
     */
    public function chiTietBan($maban)
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

        $ban = Ban::with('cuocthi')->findOrFail($maban);
        
        // Kiểm tra ban có thuộc bộ môn không
        if ($ban->cuocthi->mabomon != $giangvien->mabomon) {
            return redirect()
                ->route('giangvien.phancong.index')
                ->with('error', 'Ban không thuộc bộ môn của bạn!');
        }

        // Lấy danh sách phân công của ban
        $phanCongList = PhanCongGiangVien::with(['giangvien.nguoiDung', 'congviec'])
            ->where('maban', $maban)
            ->orderBy('ngayphancong', 'desc')
            ->paginate(15);

        // Lấy danh sách giảng viên trong bộ môn (chưa phân công vào ban này)
        $giangVienChuaPhanCong = GiangVien::with('nguoiDung')
            ->where('mabomon', $giangvien->mabomon)
            ->whereNotIn('magiangvien', function($query) use ($maban) {
                $query->select('magiangvien')
                    ->from('phanconggiangvien')
                    ->where('maban', $maban);
            })
            ->get();

        return view('giangvien.phancong.chi-tiet-ban', compact(
            'giangvien',
            'ban',
            'phanCongList',
            'giangVienChuaPhanCong',
            'isTruongBoMon'
        ));
    }

    /**
     * Phân công nhanh nhiều giảng viên vào ban
     */
    public function phanCongNhieuGiangVien(Request $request)
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
            'maban' => 'required|exists:ban,maban',
            'macongviec' => 'required|exists:congviec,macongviec',
            'giangvien_list' => 'required|array|min:1',
            'giangvien_list.*' => 'required|exists:giangvien,magiangvien',
            'vaitro' => 'required|string|max:100',
            'ngayphancong' => 'required|date',
        ], [
            'giangvien_list.required' => 'Vui lòng chọn ít nhất 1 giảng viên',
            'giangvien_list.min' => 'Vui lòng chọn ít nhất 1 giảng viên',
        ]);

        DB::beginTransaction();
        try {
            $soLuongPhanCong = 0;
            
            foreach ($validated['giangvien_list'] as $magiangvien) {
                // Kiểm tra giảng viên có trong bộ môn không
                $giangVienTarget = GiangVien::findOrFail($magiangvien);
                if ($giangVienTarget->mabomon != $giangvien->mabomon) {
                    continue;
                }

                // Kiểm tra đã phân công chưa
                $exists = PhanCongGiangVien::where('magiangvien', $magiangvien)
                    ->where('maban', $validated['maban'])
                    ->where('macongviec', $validated['macongviec'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Tạo mã phân công
                $lastPhanCong = PhanCongGiangVien::where('maphancong', 'LIKE', 'PC%')
                    ->orderByRaw('CAST(SUBSTRING(maphancong FROM 3) AS INTEGER) DESC')
                    ->lockForUpdate()
                    ->first();
                
                if ($lastPhanCong && preg_match('/PC(\d+)/', $lastPhanCong->maphancong, $matches)) {
                    $newNumber = intval($matches[1]) + 1;
                } else {
                    $newNumber = 1;
                }
                
                $maphancong = 'PC' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

                PhanCongGiangVien::create([
                    'maphancong' => $maphancong,
                    'magiangvien' => $magiangvien,
                    'macongviec' => $validated['macongviec'],
                    'maban' => $validated['maban'],
                    'vaitro' => $validated['vaitro'],
                    'ngayphancong' => $validated['ngayphancong'],
                ]);

                $soLuongPhanCong++;
            }
            
            DB::commit();
            
            if ($soLuongPhanCong > 0) {
                return back()->with('success', "Đã phân công thành công {$soLuongPhanCong} giảng viên!");
            } else {
                return back()->with('info', 'Không có giảng viên nào được phân công (có thể đã được phân công trước đó).');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi phân công. Vui lòng thử lại!');
        }
    }

    /**
     * Hiển thị form tạo ban mới
     */
    public function createBan($macuocthi)
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
                ->route('giangvien.phancong.quan-ly-ban')
                ->with('error', 'Chỉ trưởng bộ môn mới có quyền tạo ban!');
        }

        $cuocThi = CuocThi::where('macuocthi', $macuocthi)
            ->where('mabomon', $giangvien->mabomon)
            ->firstOrFail();

        return view('giangvien.ban.create', compact('cuocThi', 'giangvien'));
    }

    /**
     * Lưu ban mới
     */
    public function storeBan(Request $request)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền tạo ban!');
        }

        $validated = $request->validate([
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'tenban' => 'required|string|max:255',
            'mota' => 'nullable|string',
        ], [
            'tenban.required' => 'Vui lòng nhập tên ban',
            'macuocthi.required' => 'Vui lòng chọn cuộc thi',
        ]);

        DB::beginTransaction();
        try {
            // Tạo mã ban tự động
            $lastBan = Ban::where('maban', 'LIKE', 'BAN%')
                ->orderByRaw('CAST(SUBSTRING(maban FROM 4) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastBan && preg_match('/BAN(\d+)/', $lastBan->maban, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $validated['maban'] = 'BAN' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            Ban::create($validated);
            
            DB::commit();
            
            return redirect()->route('giangvien.phancong.quan-ly-ban')
                ->with('success', 'Tạo ban thành công!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo ban. Vui lòng thử lại!');
        }
    }

    /**
     * Hiển thị form chỉnh sửa ban
     */
    public function editBan($maban)
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
                ->route('giangvien.phancong.quan-ly-ban')
                ->with('error', 'Chỉ trưởng bộ môn mới có quyền sửa ban!');
        }

        $ban = Ban::with('cuocthi')->findOrFail($maban);
        
        // Kiểm tra ban có thuộc bộ môn không
        if ($ban->cuocthi->mabomon != $giangvien->mabomon) {
            return redirect()
                ->route('giangvien.phancong.quan-ly-ban')
                ->with('error', 'Ban không thuộc bộ môn của bạn!');
        }

        return view('giangvien.ban.edit', compact('ban', 'giangvien'));
    }

    /**
     * Cập nhật ban
     */
    public function updateBan(Request $request, $maban)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền cập nhật ban!');
        }

        $ban = Ban::findOrFail($maban);

        $validated = $request->validate([
            'tenban' => 'required|string|max:255',
            'mota' => 'nullable|string',
        ], [
            'tenban.required' => 'Vui lòng nhập tên ban',
        ]);

        $ban->update($validated);

        return redirect()->route('giangvien.phancong.quan-ly-ban')
            ->with('success', 'Cập nhật ban thành công!');
    }

    /**
     * Xóa ban
     */
    public function destroyBan($maban)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Kiểm tra quyền trưởng bộ môn
        $isTruongBoMon = DB::table('bomon')
            ->where('mabomon', $giangvien->mabomon)
            ->where('matruongbomon', $giangvien->magiangvien)
            ->exists();

        if (!$isTruongBoMon) {
            return back()->with('error', 'Chỉ trưởng bộ môn mới có quyền xóa ban!');
        }

        $ban = Ban::findOrFail($maban);
        
        // Kiểm tra có phân công nào không
        if ($ban->phancongs()->count() > 0) {
            return back()->with('error', 'Không thể xóa ban đã có phân công giảng viên!');
        }

        $ban->delete();

        return redirect()->route('giangvien.phancong.quan-ly-ban')
            ->with('success', 'Xóa ban thành công!');
    }
}