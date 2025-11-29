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
            ->leftJoin('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.mabomon', $giangvien->mabomon)
            ->select(
                'ct.*',
                'bm.tenbomon',
                'kh.makehoach',
                'kh.namhoc',
                'kh.hocky',
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
            $status = $request->status;
            
            if ($status == 'Upcoming') {
                $query->whereRaw('ct.thoigianbatdau > NOW()');
            } 
            elseif ($status == 'InProgress') {
                $query->whereRaw('ct.thoigianbatdau <= NOW() AND ct.thoigianketthuc >= NOW()');
            }
            elseif ($status == 'Completed') {
                $query->whereRaw('ct.thoigianketthuc < NOW()');
            }
        }

        // Lọc theo loại cuộc thi
        if ($request->filled('loai')) {
            $query->where('ct.loaicuocthi', $request->loai);
        }

        // Lọc theo năm học
        if ($request->filled('namhoc')) {
            $query->where('kh.namhoc', $request->namhoc);
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
     * Chi tiết cuộc thi
     */
    public function show($id)
    {
        $cuocthi = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.macuocthi', $id)
            ->select(
                'ct.*',
                'bm.tenbomon',
                'kh.makehoach',
                'kh.namhoc',
                'kh.hocky',
                'kh.trangthaiduyet as trangthaiduyet_kehoach',
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

        $cuocthi->status_label = $this->getStatusLabel($cuocthi);
        $cuocthi->status_color = $this->getStatusColor($cuocthi);

        return view('giangvien.cuocthi.show', compact('cuocthi', 'vongthi', 'dangkycanhan', 'dangkydoi'));
    }

    /**
     * Hiển thị form chỉnh sửa
     * Lưu ý: Chỉ cho sửa một số thông tin, không sửa được thông tin cơ bản đã được duyệt trong kế hoạch
     */
    public function edit($id)
    {
        $cuocthi = DB::table('cuocthi as ct')
            ->leftJoin('kehoachcuocthi as kh', 'ct.makehoach', '=', 'kh.makehoach')
            ->where('ct.macuocthi', $id)
            ->select('ct.*', 'kh.trangthaiduyet as trangthaiduyet_kehoach')
            ->first();
        
        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Kiểm tra xem cuộc thi đã bắt đầu chưa
        if (Carbon::parse($cuocthi->thoigianbatdau)->isPast()) {
            return redirect()->route('giangvien.cuocthi.show', $id)
                ->with('error', 'Không thể chỉnh sửa cuộc thi đã bắt đầu!');
        }

        $bomons = DB::table('bomon')->get();

        return view('giangvien.cuocthi.edit', compact('cuocthi', 'bomons'));
    }

    /**
     * Cập nhật cuộc thi
     * Chỉ cho phép cập nhật một số thông tin không quan trọng
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mota' => 'nullable|string',
            'diadiem' => 'nullable|string',
            'chiphithucte' => 'nullable|numeric',
        ]);

        $cuocthi = DB::table('cuocthi')->where('macuocthi', $id)->first();

        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Kiểm tra xem cuộc thi đã bắt đầu chưa
        if (Carbon::parse($cuocthi->thoigianbatdau)->isPast()) {
            return redirect()->route('giangvien.cuocthi.show', $id)
                ->with('error', 'Không thể chỉnh sửa cuộc thi đã bắt đầu!');
        }

        $validated['ngaycapnhat'] = now();

        DB::table('cuocthi')->where('macuocthi', $id)->update($validated);

        return redirect()->route('giangvien.cuocthi.show', $id)
            ->with('success', 'Cập nhật cuộc thi thành công!');
    }

    /**
     * Xóa cuộc thi
     */
    public function destroy($id)
    {
        $cuocthi = DB::table('cuocthi')->where('macuocthi', $id)->first();

        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Không cho xóa nếu đã có đăng ký
        $hasDangKy = DB::table('dangkycanhan')->where('macuocthi', $id)->exists() ||
                     DB::table('dangkydoithi')->where('macuocthi', $id)->exists();

        if ($hasDangKy) {
            return back()->with('error', 'Không thể xóa cuộc thi đã có đăng ký!');
        }

        // Không cho xóa nếu đã bắt đầu
        if (Carbon::parse($cuocthi->thoigianbatdau)->isPast()) {
            return back()->with('error', 'Không thể xóa cuộc thi đã bắt đầu!');
        }

        DB::table('cuocthi')->where('macuocthi', $id)->delete();

        return redirect()->route('giangvien.cuocthi.index')
            ->with('success', 'Xóa cuộc thi thành công!');
    }

    /**
     * Cập nhật trạng thái cuộc thi
     */
    public function updateStatus($id, Request $request)
    {
        $validated = $request->validate([
            'trangthai' => 'required|in:Approved,InProgress,Completed,Cancelled'
        ]);

        $cuocthi = DB::table('cuocthi')->where('macuocthi', $id)->first();

        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        DB::table('cuocthi')
            ->where('macuocthi', $id)
            ->update([
                'trangthai' => $validated['trangthai'],
                'ngaycapnhat' => now()
            ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    // Helper methods
    private function getStatusLabel($event)
    {
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