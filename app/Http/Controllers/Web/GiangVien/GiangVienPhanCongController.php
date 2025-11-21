<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\PhanCongGiangVien;
use App\Models\CuocThi;
use App\Models\Ban;

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
        
        // Query phân công với relationships
        $query = PhanCongGiangVien::with(['ban', 'congviec', 'giangvien.nguoiDung'])
            ->where('magiangvien', $giangvien->magiangvien);

        // Tìm kiếm theo vai trò
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vaitro', 'like', "%{$search}%");
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

        // Lọc theo thời gian
        if ($request->filled('from_date')) {
            $query->whereDate('ngayphancong', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('ngayphancong', '<=', $request->to_date);
        }
        
        $phanCongList = $query->orderBy('ngayphancong', 'desc')->paginate(10);

        // Lấy danh sách công việc và ban cho filter
        $congViecList = \App\Models\CongViec::orderBy('tencongviec')->get();
        $banList = Ban::orderBy('tenban')->get();
        
        return view('giangvien.phancong.index', compact('phanCongList', 'giangvien', 'congViecList', 'banList'));
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
        
        $phanCong = PhanCongGiangVien::with(['ban', 'congviec', 'giangvien.nguoiDung'])
            ->findOrFail($id);

        // Kiểm tra quyền xem
        if ($phanCong->magiangvien != $giangvien->magiangvien) {
            return redirect()
                ->route('giangvien.phancong.index')
                ->with('error', 'Bạn không có quyền xem phân công này!');
        }
        
        return view('giangvien.phancong.show', compact('phanCong', 'giangvien'));
    }

    /**
     * API: Thống kê phân công
     */
    public function statistics()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $stats = [
            'tong_cong_viec' => PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)->count(),
            'theo_ban' => PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
                ->select('maban', DB::raw('count(*) as total'))
                ->groupBy('maban')
                ->with('ban')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Export danh sách phân công ra Excel
     */
    public function export(Request $request)
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();

        $phanCongList = PhanCongGiangVien::with(['ban', 'congviec'])
            ->where('magiangvien', $giangvien->magiangvien)
            ->orderBy('ngayphancong', 'desc')
            ->get();

        // Tạo file CSV
        $filename = 'phan-cong-' . $giangvien->magiangvien . '-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM cho UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($handle, ['STT', 'Công việc', 'Ban', 'Vai trò', 'Ngày phân công']);
        
        // Data
        foreach ($phanCongList as $index => $pc) {
            fputcsv($handle, [
                $index + 1,
                $pc->congviec->tencongviec ?? 'N/A',
                $pc->ban->tenban ?? 'N/A',
                $pc->vaitro ?? 'N/A',
                $pc->ngayphancong ? \Carbon\Carbon::parse($pc->ngayphancong)->format('d/m/Y') : 'N/A',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}