<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GiangVienGiaiThuongController extends Controller
{
    /**
     * Danh sách giải thưởng
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $query = DB::table('datgiai as dg')
            ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->select(
                'dg.*',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'kh.namhoc',
                'kh.hocky',
                DB::raw("CASE 
                    WHEN dg.loaidangky = 'CaNhan' THEN (
                        SELECT nd.hoten 
                        FROM dangkycanhan dk
                        JOIN sinhvien sv ON dk.masinhvien = sv.masinhvien
                        JOIN nguoidung nd ON sv.manguoidung = nd.manguoidung
                        WHERE dk.madangkycanhan = dg.madangkycanhan
                        LIMIT 1
                    )
                    WHEN dg.loaidangky = 'DoiNhom' THEN (
                        SELECT dt.tendoithi
                        FROM dangkydoithi dkd
                        JOIN doithi dt ON dkd.madoithi = dt.madoithi
                        WHERE dkd.madangkydoi = dg.madangkydoi
                        LIMIT 1
                    )
                END as ten_nguoi_dat_giai")
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ct.tencuocthi', 'ILIKE', "%{$search}%")
                  ->orWhere('dg.tengiai', 'ILIKE', "%{$search}%")
                  ->orWhere('dg.giaithuong', 'ILIKE', "%{$search}%");
            });
        }

        // Lọc theo cuộc thi
        if ($request->filled('macuocthi')) {
            $query->where('ct.macuocthi', $request->macuocthi);
        }

        // Lọc theo loại đăng ký
        if ($request->filled('loaidangky')) {
            $query->where('dg.loaidangky', $request->loaidangky);
        }

        // Lọc theo năm học
        if ($request->filled('namhoc')) {
            $query->where('kh.namhoc', $request->namhoc);
        }

        $giaithuongList = $query->orderBy('dg.ngaytrao', 'desc')->paginate(15);

        // Lấy danh sách cuộc thi để filter
        $cuocthiList = DB::table('cuocthi as ct')
            ->join('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->where('ct.trangthai', 'Completed')
            ->select('ct.macuocthi', 'ct.tencuocthi', 'kh.namhoc')
            ->orderBy('kh.namhoc', 'desc')
            ->get();

        // Thống kê
        $statistics = [
            'total' => DB::table('datgiai as dg')
                ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->count(),
            'canhan' => DB::table('datgiai as dg')
                ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('dg.loaidangky', 'CaNhan')
                ->count(),
            'doinh' => DB::table('datgiai as dg')
                ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
                ->where('ct.mabomon', $giangvien->mabomon)
                ->where('dg.loaidangky', 'DoiNhom')
                ->count(),
        ];

        return view('giangvien.giaithuong.index', compact(
            'giaithuongList',
            'cuocthiList',
            'statistics',
            'giangvien'
        ));
    }

    /**
     * Hiển thị form tạo giải thưởng
     */
    public function create(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy danh sách cuộc thi đã kết thúc
        $cuocthiList = DB::table('cuocthi as ct')
            ->join('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->where('ct.trangthai', 'Completed')
            ->select('ct.*', 'kh.namhoc', 'kh.hocky')
            ->orderBy('ct.thoigianketthuc', 'desc')
            ->get();

        $macuocthi = $request->get('macuocthi');
        $dangkycanhans = collect();
        $dangkydois = collect();

        if ($macuocthi) {
            // Lấy đăng ký cá nhân có kết quả thi
            $dangkycanhans = DB::table('dangkycanhan as dk')
                ->join('sinhvien as sv', 'dk.masinhvien', '=', 'sv.masinhvien')
                ->join('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
                ->leftJoin('baithi as bt', function($join) {
                    $join->on('dk.madangkycanhan', '=', 'bt.madangkycanhan');
                })
                ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
                ->where('dk.macuocthi', $macuocthi)
                ->where('dk.trangthai', 'Registered')
                ->whereNotExists(function($query) use ($macuocthi) {
                    $query->select(DB::raw(1))
                        ->from('datgiai')
                        ->whereRaw('datgiai.madangkycanhan = dk.madangkycanhan')
                        ->where('datgiai.macuocthi', $macuocthi);
                })
                ->select(
                    'dk.madangkycanhan',
                    'nd.hoten',
                    'sv.masinhvien',
                    'sv.malop',
                    DB::raw('COALESCE(AVG(kq.diem), 0) as diem_trung_binh')
                )
                ->groupBy('dk.madangkycanhan', 'nd.hoten', 'sv.masinhvien', 'sv.malop')
                ->orderBy('diem_trung_binh', 'desc')
                ->get();

            // Lấy đăng ký đội thi có kết quả
            $dangkydois = DB::table('dangkydoithi as dk')
                ->join('doithi as dt', 'dk.madoithi', '=', 'dt.madoithi')
                ->leftJoin('baithi as bt', function($join) {
                    $join->on('dk.madangkydoi', '=', 'bt.madangkydoi');
                })
                ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
                ->where('dk.macuocthi', $macuocthi)
                ->where('dk.trangthai', 'Registered')
                ->whereNotExists(function($query) use ($macuocthi) {
                    $query->select(DB::raw(1))
                        ->from('datgiai')
                        ->whereRaw('datgiai.madangkydoi = dk.madangkydoi')
                        ->where('datgiai.macuocthi', $macuocthi);
                })
                ->select(
                    'dk.madangkydoi',
                    'dt.tendoithi',
                    'dt.madoithi',
                    'dt.sothanhvien',
                    DB::raw('COALESCE(AVG(kq.diem), 0) as diem_trung_binh')
                )
                ->groupBy('dk.madangkydoi', 'dt.tendoithi', 'dt.madoithi', 'dt.sothanhvien')
                ->orderBy('diem_trung_binh', 'desc')
                ->get();
        }

        return view('giangvien.giaithuong.create', compact(
            'cuocthiList',
            'dangkycanhans',
            'dangkydois',
            'macuocthi',
            'giangvien'
        ));
    }

    /**
     * Lưu giải thưởng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'macuocthi' => 'required|string|exists:cuocthi,macuocthi',
            'loaidangky' => 'required|in:CaNhan,DoiNhom',
            'madangkycanhan' => 'required_if:loaidangky,CaNhan|nullable|string',
            'madangkydoi' => 'required_if:loaidangky,DoiNhom|nullable|string',
            'tengiai' => 'required|string|max:255',
            'giaithuong' => 'nullable|string',
            'diemrenluyen' => 'nullable|numeric|min:0|max:100',
            'ngaytrao' => 'required|date',
        ], [
            'macuocthi.required' => 'Vui lòng chọn cuộc thi',
            'loaidangky.required' => 'Vui lòng chọn loại đăng ký',
            'madangkycanhan.required_if' => 'Vui lòng chọn sinh viên',
            'madangkydoi.required_if' => 'Vui lòng chọn đội thi',
            'tengiai.required' => 'Vui lòng nhập tên giải',
            'ngaytrao.required' => 'Vui lòng chọn ngày trao giải',
        ]);

        DB::beginTransaction();
        try {
            $madatgiai = 'DG' . Str::upper(Str::random(8));

            DB::table('datgiai')->insert([
                'madatgiai' => $madatgiai,
                'macuocthi' => $validated['macuocthi'],
                'madangkycanhan' => $validated['loaidangky'] === 'CaNhan' ? $validated['madangkycanhan'] : null,
                'madangkydoi' => $validated['loaidangky'] === 'DoiNhom' ? $validated['madangkydoi'] : null,
                'loaidangky' => $validated['loaidangky'],
                'tengiai' => $validated['tengiai'],
                'giaithuong' => $validated['giaithuong'] ?? null,
                'diemrenluyen' => $validated['diemrenluyen'] ?? null,
                'ngaytrao' => $validated['ngaytrao'],
            ]);

            DB::commit();
            return redirect()->route('giangvien.giaithuong.index')
                ->with('success', 'Thêm giải thưởng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Chi tiết giải thưởng
     */
    public function show($id)
    {
        $giaithuong = DB::table('datgiai as dg')
            ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('dg.madatgiai', $id)
            ->select('dg.*', 'ct.tencuocthi', 'ct.loaicuocthi', 'kh.namhoc', 'kh.hocky')
            ->first();

        if (!$giaithuong) {
            abort(404, 'Không tìm thấy giải thưởng');
        }

        // Lấy thông tin người đạt giải
        if ($giaithuong->loaidangky === 'CaNhan') {
            $nguoidatgiai = DB::table('dangkycanhan as dk')
                ->join('sinhvien as sv', 'dk.masinhvien', '=', 'sv.masinhvien')
                ->join('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
                ->where('dk.madangkycanhan', $giaithuong->madangkycanhan)
                ->select('nd.hoten', 'sv.masinhvien', 'sv.malop', 'nd.email', 'nd.sodienthoai')
                ->first();
        } else {
            $nguoidatgiai = DB::table('dangkydoithi as dk')
                ->join('doithi as dt', 'dk.madoithi', '=', 'dt.madoithi')
                ->where('dk.madangkydoi', $giaithuong->madangkydoi)
                ->select('dt.tendoithi', 'dt.madoithi', 'dt.sothanhvien')
                ->first();

            // Lấy danh sách thành viên
            if ($nguoidatgiai) {
                $thanhviens = DB::table('thanhviendoithi as tv')
                    ->join('sinhvien as sv', 'tv.masinhvien', '=', 'sv.masinhvien')
                    ->join('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
                    ->where('tv.madoithi', $nguoidatgiai->madoithi)
                    ->select('nd.hoten', 'sv.masinhvien', 'tv.vaitro')
                    ->get();
                $nguoidatgiai->thanhviens = $thanhviens;
            }
        }

        return view('giangvien.giaithuong.show', compact('giaithuong', 'nguoidatgiai'));
    }

    /**
     * Form chỉnh sửa giải thưởng
     */
    public function edit($id)
    {
        $giaithuong = DB::table('datgiai')->where('madatgiai', $id)->first();
        
        if (!$giaithuong) {
            abort(404, 'Không tìm thấy giải thưởng');
        }

        return view('giangvien.giaithuong.edit', compact('giaithuong'));
    }

    /**
     * Cập nhật giải thưởng
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tengiai' => 'required|string|max:255',
            'giaithuong' => 'nullable|string',
            'diemrenluyen' => 'nullable|numeric|min:0|max:100',
            'ngaytrao' => 'required|date',
        ]);

        $giaithuong = DB::table('datgiai')->where('madatgiai', $id)->first();

        if (!$giaithuong) {
            abort(404, 'Không tìm thấy giải thưởng');
        }

        DB::table('datgiai')->where('madatgiai', $id)->update($validated);

        return redirect()->route('giangvien.giaithuong.show', $id)
            ->with('success', 'Cập nhật giải thưởng thành công!');
    }

    /**
     * Xóa giải thưởng
     */
    public function destroy($id)
    {
        $giaithuong = DB::table('datgiai')->where('madatgiai', $id)->first();

        if (!$giaithuong) {
            abort(404, 'Không tìm thấy giải thưởng');
        }

        DB::table('datgiai')->where('madatgiai', $id)->delete();

        return redirect()->route('giangvien.giaithuong.index')
            ->with('success', 'Xóa giải thưởng thành công!');
    }

    /**
     * API: Lấy danh sách đăng ký theo cuộc thi
     */
    public function getDangKyByCuocThi($macuocthi, Request $request)
    {
        $loaidangky = $request->get('loaidangky', 'CaNhan');

        if ($loaidangky === 'CaNhan') {
            $data = DB::table('dangkycanhan as dk')
                ->join('sinhvien as sv', 'dk.masinhvien', '=', 'sv.masinhvien')
                ->join('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
                ->leftJoin('baithi as bt', 'dk.madangkycanhan', '=', 'bt.madangkycanhan')
                ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
                ->where('dk.macuocthi', $macuocthi)
                ->where('dk.trangthai', 'Registered')
                ->whereNotExists(function($query) use ($macuocthi) {
                    $query->select(DB::raw(1))
                        ->from('datgiai')
                        ->whereRaw('datgiai.madangkycanhan = dk.madangkycanhan')
                        ->where('datgiai.macuocthi', $macuocthi);
                })
                ->select(
                    'dk.madangkycanhan',
                    'nd.hoten',
                    'sv.masinhvien',
                    'sv.malop',
                    DB::raw('COALESCE(AVG(kq.diem), 0) as diem_trung_binh')
                )
                ->groupBy('dk.madangkycanhan', 'nd.hoten', 'sv.masinhvien', 'sv.malop')
                ->orderBy('diem_trung_binh', 'desc')
                ->get();
        } else {
            $data = DB::table('dangkydoithi as dk')
                ->join('doithi as dt', 'dk.madoithi', '=', 'dt.madoithi')
                ->leftJoin('baithi as bt', 'dk.madangkydoi', '=', 'bt.madangkydoi')
                ->leftJoin('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
                ->where('dk.macuocthi', $macuocthi)
                ->where('dk.trangthai', 'Registered')
                ->whereNotExists(function($query) use ($macuocthi) {
                    $query->select(DB::raw(1))
                        ->from('datgiai')
                        ->whereRaw('datgiai.madangkydoi = dk.madangkydoi')
                        ->where('datgiai.macuocthi', $macuocthi);
                })
                ->select(
                    'dk.madangkydoi',
                    'dt.tendoithi',
                    'dt.sothanhvien',
                    DB::raw('COALESCE(AVG(kq.diem), 0) as diem_trung_binh')
                )
                ->groupBy('dk.madangkydoi', 'dt.tendoithi', 'dt.sothanhvien')
                ->orderBy('diem_trung_binh', 'desc')
                ->get();
        }

        return response()->json($data);
    }
}