<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GiangVienCuocThiController extends Controller
{
    /**
     * Danh sách cuộc thi
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $query = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->select(
                'ct.*',
                'bm.tenbomon',
                DB::raw('(
                    (SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                    (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)
                ) as soluongdangky')
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ct.tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('ct.trangthai', $request->status);
        }

        // Lọc theo loại cuộc thi
        if ($request->filled('loai')) {
            $query->where('ct.loaicuocthi', $request->loai);
        }

        $cuocthiList = $query->orderBy('ct.thoigianbatdau', 'desc')->paginate(10);

        // Transform data
        $cuocthiList->getCollection()->transform(function ($ct) {
            $ct->status_label = $this->getStatusLabel($ct);
            $ct->status_color = $this->getStatusColor($ct);
            return $ct;
        });

        return view('giangvien.cuocthi.index', compact('cuocthiList', 'giangvien'));
    }

    /**
     * Hiển thị form tạo cuộc thi
     */
    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $bomons = DB::table('bomon')->get();
        
        return view('giangvien.cuocthi.create', compact('giangvien', 'bomons'));
    }

    /**
     * Lưu cuộc thi mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tencuocthi' => 'required|string|max:255',
            'loaicuocthi' => 'required|string',
            'mota' => 'nullable|string',
            'mucdich' => 'nullable|string',
            'doituongthamgia' => 'nullable|string',
            'thoigianbatdau' => 'required|date',
            'thoigianketthuc' => 'required|date|after:thoigianbatdau',
            'diadiem' => 'nullable|string',
            'soluongthanhvien' => 'nullable|integer',
            'hinhthucthamgia' => 'nullable|string',
            'dutrukinhphi' => 'nullable|numeric',
        ]);

        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();

        // Tạo mã cuộc thi tự động - FIX: Sử dụng DB transaction và lock
        DB::beginTransaction();
        try {
            // Lấy số cuối cùng với FOR UPDATE để tránh duplicate
            $lastCuocthi = DB::table('cuocthi')
                ->where('macuocthi', 'LIKE', 'CT%')
                ->orderByRaw('CAST(SUBSTRING(macuocthi FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastCuocthi && preg_match('/CT(\d+)/', $lastCuocthi->macuocthi, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $validated['macuocthi'] = 'CT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $validated['mabomon'] = $giangvien->mabomon;
            $validated['trangthai'] = 'Draft';
            $validated['ngaytao'] = now();
            $validated['ngaycapnhat'] = now();

            DB::table('cuocthi')->insert($validated);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo cuộc thi. Vui lòng thử lại!');
        }

        return redirect()->route('giangvien.cuocthi.index')
            ->with('success', 'Tạo cuộc thi thành công!');
    }

    /**
     * Chi tiết cuộc thi
     */
    public function show($id)
    {
        $cuocthi = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('ct.macuocthi', $id)
            ->select(
                'ct.*',
                'bm.tenbomon',
                DB::raw('(
                    (SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                    (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)
                ) as soluongdangky')
            )
            ->first();

        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Lấy danh sách vòng thi
        $vongthi = DB::table('vongthi')
            ->where('macuocthi', $id)
            ->orderBy('thutu')
            ->get();

        // Lấy danh sách đăng ký
        $dangkycanhan = DB::table('dangkycanhan as dk')
            ->join('sinhvien as sv', 'dk.masinhvien', '=', 'sv.masinhvien')
            ->join('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dk.macuocthi', $id)
            ->select('dk.*', 'nd.hoten', 'sv.malop as lop')
            ->get();

        $dangkydoi = DB::table('dangkydoithi as dk')
            ->join('doithi as dt', 'dk.madoithi', '=', 'dt.madoithi')
            ->where('dk.macuocthi', $id)
            ->select('dk.*', 'dt.tendoithi')
            ->get();

        return view('giangvien.cuocthi.show', compact('cuocthi', 'vongthi', 'dangkycanhan', 'dangkydoi'));
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $cuocthi = DB::table('cuocthi')->where('macuocthi', $id)->first();
        
        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        $bomons = DB::table('bomon')->get();

        return view('giangvien.cuocthi.edit', compact('cuocthi', 'bomons'));
    }

    /**
     * Cập nhật cuộc thi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tencuocthi' => 'required|string|max:255',
            'loaicuocthi' => 'required|string',
            'mota' => 'nullable|string',
            'mucdich' => 'nullable|string',
            'doituongthamgia' => 'nullable|string',
            'thoigianbatdau' => 'required|date',
            'thoigianketthuc' => 'required|date|after:thoigianbatdau',
            'diadiem' => 'nullable|string',
            'soluongthanhvien' => 'nullable|integer',
            'hinhthucthamgia' => 'nullable|string',
            'dutrukinhphi' => 'nullable|numeric',
        ]);

        $validated['ngaycapnhat'] = now();

        DB::table('cuocthi')->where('macuocthi', $id)->update($validated);

        return redirect()->route('giangvien.cuocthi.index', $id)
            ->with('success', 'Cập nhật cuộc thi thành công!');
    }

    /**
     * Xóa cuộc thi
     */
    public function destroy($id)
    {
        // Kiểm tra xem có đăng ký nào chưa
        $hasDangKy = DB::table('dangkycanhan')->where('macuocthi', $id)->exists() ||
                     DB::table('dangkydoithi')->where('macuocthi', $id)->exists();

        if ($hasDangKy) {
            return back()->with('error', 'Không thể xóa cuộc thi đã có đăng ký!');
        }

        DB::table('cuocthi')->where('macuocthi', $id)->delete();

        return redirect()->route('giangvien.cuocthi.index')
            ->with('success', 'Xóa cuộc thi thành công!');
    }

    // Helper methods
    private function getStatusLabel($event)
    {
        // Nếu trạng thái là Draft -> hiển thị "Nháp"
        if ($event->trangthai == 'Draft') {
            return 'Nháp';
        }
        
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        $end = Carbon::parse($event->thoigianketthuc);

        if ($now->lt($start)) {
            return 'Sắp diễn ra';
        } elseif ($now->between($start, $end)) {
            return 'Đang diễn ra';
        } else {
            return 'Đã kết thúc';
        }
    }

    private function getStatusColor($event)
    {
        // Nếu trạng thái là Draft -> màu xám
        if ($event->trangthai == 'Draft') {
            return 'gray';
        }
        
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        $end = Carbon::parse($event->thoigianketthuc);

        if ($now->lt($start)) {
            return 'yellow';
        } elseif ($now->between($start, $end)) {
            return 'green';
        } else {
            return 'gray';
        }
    }
}