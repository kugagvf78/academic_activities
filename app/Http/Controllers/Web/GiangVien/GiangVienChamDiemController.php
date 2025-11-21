<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\GiangVien;
use App\Models\KetQuaThi;
use App\Models\BaiThi;
use App\Models\PhanCongGiangVien;

class GiangVienChamDiemController extends Controller
{
    /**
     * Hiển thị danh sách bài cần chấm
     * 
     * LOGIC MỚI: Lấy TOÀN BỘ cuộc thi của Ban, không chỉ công việc cá nhân
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // ✅ CÁCH MỚI: Lấy TOÀN BỘ cuộc thi của BAN, không chỉ công việc cá nhân
        
        // Bước 1: Lấy danh sách BAN mà GV tham gia (bất kể vai trò gì)
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        // Bước 2: Lấy TẤT CẢ cuộc thi của các Ban đó (qua CongViec)
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)  // ✅ KEY: Lấy theo Ban, không theo công việc cá nhân
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        // Debug: Log để kiểm tra
        \Log::info('GV002 - Danh sách Ban:', $danhSachBan);
        \Log::info('GV002 - Danh sách Cuộc thi:', $danhSachCuocThi);
        
        // Bước 3: Query bài thi cần chấm
        $query = KetQuaThi::with([
            'baithi.dethi.cuocthi',
            'baithi.dangkycanhan.sinhvien.nguoiDung',
            'baithi.dangkydoi.doithi'
        ])
        ->where(function($q) use ($giangvien, $danhSachCuocThi) {
            // Điều kiện 1: Bài chưa có người chấm VÀ thuộc cuộc thi của Ban
            $q->where(function($q1) use ($danhSachCuocThi) {
                $q1->whereNull('nguoichamdiem')
                   ->whereHas('baithi.dethi', function($q2) use ($danhSachCuocThi) {
                       $q2->whereIn('macuocthi', $danhSachCuocThi);
                   });
            })
            // Điều kiện 2: Bài đã được phân cho GV này
            ->orWhere('nguoichamdiem', $giangvien->magiangvien);
        })
        ->whereNull('diem'); // Chỉ lấy bài chưa chấm

        // Tìm kiếm theo tên hoặc mã sinh viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Tìm trong đăng ký cá nhân
                $q->whereHas('baithi.dangkycanhan.sinhvien', function($q2) use ($search) {
                    $q2->where('masinhvien', 'like', "%{$search}%")
                       ->orWhereHas('nguoiDung', function($q3) use ($search) {
                           $q3->where('hoten', 'like', "%{$search}%");
                       });
                })
                // Hoặc tìm trong đăng ký đội
                ->orWhereHas('baithi.dangkydoi.doithi', function($q2) use ($search) {
                    $q2->where('tendoi', 'like', "%{$search}%");
                });
            });
        }

        // Lọc theo cuộc thi
        if ($request->filled('cuocthi')) {
            $query->whereHas('baithi.dethi', function($q) use ($request) {
                $q->where('macuocthi', $request->cuocthi);
            });
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
            'baithi.dethi.cuocthi',
            'baithi.dethi',
            'baithi.dangkycanhan.sinhvien.nguoiDung',
            'baithi.dangkydoi.doithi'
        ])->findOrFail($id);

        // ✅ Kiểm tra quyền chấm điểm
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        // Kiểm tra bài thi thuộc cuộc thi GV phụ trách
        $thuocCuocThiCuaGV = false;
        if ($ketqua->baithi && $ketqua->baithi->dethi) {
            $thuocCuocThiCuaGV = in_array($ketqua->baithi->dethi->macuocthi, $danhSachCuocThi);
        }
        
        // GV có quyền chấm nếu:
        $coQuyenCham = ($ketqua->nguoichamdiem == null && $thuocCuocThiCuaGV) 
                    || ($ketqua->nguoichamdiem == $giangvien->magiangvien);
        
        if (!$coQuyenCham) {
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

        $ketqua = KetQuaThi::with('baithi.dethi')->findOrFail($id);

        // ✅ Kiểm tra quyền
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        $thuocCuocThiCuaGV = false;
        if ($ketqua->baithi && $ketqua->baithi->dethi) {
            $thuocCuocThiCuaGV = in_array($ketqua->baithi->dethi->macuocthi, $danhSachCuocThi);
        }
        
        $coQuyenCham = ($ketqua->nguoichamdiem == null && $thuocCuocThiCuaGV) 
                    || ($ketqua->nguoichamdiem == $giangvien->magiangvien);
        
        if (!$coQuyenCham) {
            return redirect()
                ->route('giangvien.chamdiem.index')
                ->with('error', 'Bạn không có quyền chấm bài thi này!');
        }

        // ✅ Cập nhật điểm
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

        // Kiểm tra quyền - Chỉ người chấm mới được xóa
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

        if (!$ketqua->baithi || !$ketqua->baithi->filebaithi) {
            return back()->with('error', 'Không tìm thấy file bài làm!');
        }

        $filePath = storage_path('app/public/' . $ketqua->baithi->filebaithi);

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

        // Lấy cuộc thi từ Ban của GV
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $cuocThiList = DB::table('congviec as cv')
            ->join('cuocthi as ct', 'cv.macuocthi', '=', 'ct.macuocthi')
            ->whereIn('cv.maban', $danhSachBan)
            ->select('ct.*')
            ->distinct()
            ->get();

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

        // Lấy danh sách cuộc thi GV phụ trách
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();

        $count = 0;
        foreach ($validated['ketqua_ids'] as $id) {
            $ketqua = KetQuaThi::with('baithi.dethi')->find($id);
            
            if (!$ketqua) continue;
            
            // Kiểm tra quyền
            $thuocCuocThiCuaGV = false;
            if ($ketqua->baithi && $ketqua->baithi->dethi) {
                $thuocCuocThiCuaGV = in_array($ketqua->baithi->dethi->macuocthi, $danhSachCuocThi);
            }
            
            $coQuyenCham = ($ketqua->nguoichamdiem == null && $thuocCuocThiCuaGV) 
                        || ($ketqua->nguoichamdiem == $giangvien->magiangvien);
            
            if ($coQuyenCham) {
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