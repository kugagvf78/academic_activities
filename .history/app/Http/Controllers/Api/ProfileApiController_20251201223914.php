<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProfileApiController extends Controller
{
    /**
     * API: Lấy toàn bộ thông tin hồ sơ
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        // Lấy thông tin chi tiết theo vai trò
        if ($user->vaitro === 'SinhVien') {

            $profile = $user->sinhVien()
                ->with([
                    'lop.giangvienchunhiem.nguoiDung'
                ])
                ->first();

            $danhSachLop = DB::table('lop')
                ->leftJoin('giangvien', 'lop.magvcn', '=', 'giangvien.magiangvien')
                ->leftJoin('nguoidung', 'giangvien.manguoidung', '=', 'nguoidung.manguoidung')
                ->select('lop.*', 'giangvien.magiangvien', 'nguoidung.hoten')
                ->orderBy('lop.tenlop')
                ->get();

            $activities = $this->getSinhVienActivities($profile);
            $certificates = $this->getSinhVienCertificates($profile);
            $diemRenLuyen = $this->getDiemRenLuyenDetail($profile);
            $competitionRegistrations = $this->getCompetitionRegistrations($profile);

            // 🔥 Lấy đăng ký hoạt động hỗ trợ + cổ vũ
            $registrations = DB::table('dangkyhoatdong as dkhd')
                ->join('hoatdonghotro as hd', 'dkhd.mahoatdong', '=', 'hd.mahoatdong')
                ->join('cuocthi as ct', 'hd.macuocthi', '=', 'ct.macuocthi')
                ->where('dkhd.masinhvien', $profile->masinhvien)
                ->whereIn('hd.loaihoatdong', ['CoVu', 'ToChuc', 'HoTroKyThuat'])
                ->select(
                    'dkhd.madangkyhoatdong',
                    'dkhd.ngaydangky',
                    'dkhd.trangthai',
                    'dkhd.diemdanhqr',
                    'dkhd.thoigiandiemdanh',
                    'hd.tenhoatdong',
                    'hd.loaihoatdong',
                    'hd.thoigianbatdau',
                    'hd.thoigianketthuc',
                    'hd.diadiem',
                    'hd.diemrenluyen',
                    'ct.tencuocthi',
                    'ct.macuocthi'
                )
                ->orderBy('hd.thoigianbatdau', 'desc')
                ->get();

            $registrations = $registrations->map(function($reg) {
                $now = now();
                $start = Carbon::parse($reg->thoigianbatdau);
                $end = Carbon::parse($reg->thoigianketthuc);

                if ($end->lt($now)) {
                    $status = 'ended';
                    $statusLabel = 'Đã kết thúc';
                    $statusColor = 'gray';
                } elseif ($start->lte($now) && $end->gte($now)) {
                    $status = 'ongoing';
                    $statusLabel = 'Đang diễn ra';
                    $statusColor = 'green';
                } else {
                    $status = 'upcoming';
                    $statusLabel = 'Sắp diễn ra';
                    $statusColor = 'blue';
                }

                $canCancel = 
                    !$reg->diemdanhqr && 
                    $start->gt(now()) && 
                    now()->diffInHours($start, false) >= 24;

                return (object)[
                    'madangkyhoatdong' => $reg->madangkyhoatdong,
                    'tencuocthi' => $reg->tencuocthi,
                    'tenhoatdong' => $reg->tenhoatdong,
                    'loaihoatdong' => $reg->loaihoatdong,
                    'thoigianbatdau' => $start,
                    'thoigianketthuc' => $end,
                    'diadiem' => $reg->diadiem,
                    'diemrenluyen' => $reg->diemrenluyen,
                    'ngaydangky' => Carbon::parse($reg->ngaydangky),
                    'trangthai' => $reg->trangthai,
                    'diemdanhqr' => $reg->diemdanhqr,
                    'thoigiandiemdanh' => $reg->thoigiandiemdanh ? Carbon::parse($reg->thoigiandiemdanh) : null,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'statusColor' => $statusColor,
                    'canCancel' => $canCancel,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'profile' => $profile,
                    'activities' => $activities,
                    'certificates' => $certificates,
                    'danhSachLop' => $danhSachLop,
                    'diemRenLuyen' => $diemRenLuyen,
                    'registrations' => $registrations,
                    'competitionRegistrations' => $competitionRegistrations
                ]
            ]);
        }

        // Giảng viên
        if ($user->vaitro === 'GiangVien') {
            $profile = $user->giangVien()
                ->with(['boMon', 'lopChuNhiem'])
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'profile' => $profile,
                    'activities' => [],
                    'certificates' => [],
                    'danhSachLop' => [],
                    'diemRenLuyen' => [],
                    'registrations' => [],
                    'competitionRegistrations' => []
                ]
            ]);
        }

        // Admin
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => null
            ]
        ]);
    }

    /**
     * ⭐ HÀM GỐC: Lấy đăng ký dự thi
     */
    private function getCompetitionRegistrations($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        try {
            $registrations = collect([]);

            // 1. Đăng ký cá nhân
            $caNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                ->leftJoin('baithi as bt', function($join) {
                    $join->on('dkcn.madangkycanhan', '=', 'bt.madangkycanhan')
                        ->where('bt.loaidangky', '=', 'CaNhan');
                })
                ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                ->select(
                    'dkcn.madangkycanhan as id',
                    'ct.tencuocthi',
                    'ct.thoigianbatdau',
                    'ct.thoigianketthuc',
                    'ct.trangthai as trangthaicuocthi',
                    'dkcn.ngaydangky',
                    'dkcn.trangthai',
                    DB::raw("'CaNhan' as loaidangky"),
                    DB::raw("NULL as tendoithi"),
                    DB::raw("NULL as vaitro"),
                    'bt.mabaithi',
                    'bt.thoigiannop',
                    'bt.trangthai as trangthainop'
                )
                ->get();

            // 2. Đăng ký đội nhóm
            $doiNhom = DB::table('thanhviendoithi as tv')
                ->join('doithi as dt', 'tv.madoithi', '=', 'dt.madoithi')
                ->join('dangkydoithi as dkdt', 'dt.madoithi', '=', 'dkdt.madoithi')
                ->join('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
                ->leftJoin('baithi as bt', function($join) {
                    $join->on('dkdt.madangkydoi', '=', 'bt.madangkydoi')
                        ->where('bt.loaidangky', '=', 'DoiNhom');
                })
                ->where('tv.masinhvien', $sinhVien->masinhvien)
                ->select(
                    'dkdt.madangkydoi as id',
                    'ct.tencuocthi',
                    'ct.thoigianbatdau',
                    'ct.thoigianketthuc',
                    'ct.trangthai as trangthaicuocthi',
                    'dkdt.ngaydangky',
                    'dkdt.trangthai',
                    DB::raw("'DoiNhom' as loaidangky"),
                    'dt.tendoithi',
                    'tv.vaitro',
                    'bt.mabaithi',
                    'bt.thoigiannop',
                    'bt.trangthai as trangthainop'
                )
                ->get();

            $registrations = $caNhan->concat($doiNhom)->sortByDesc('ngaydangky');

            return $registrations->map(function($reg) {
                $now = now();
                $start = Carbon::parse($reg->thoigianbatdau);
                $end = Carbon::parse($reg->thoigianketthuc);

                $submitDeadline = $end->copy()->addDay();

                if ($end->lt($now)) {
                    $status = 'ended';
                    $statusLabel = 'Đã kết thúc';
                    $statusColor = 'gray';
                } elseif ($start->lte($now) && $end->gte($now)) {
                    $status = 'ongoing';
                    $statusLabel = 'Đang diễn ra';
                    $statusColor = 'green';
                } else {
                    $status = 'upcoming';
                    $statusLabel = 'Sắp diễn ra';
                    $statusColor = 'blue';
                }

                $canCancel = 
                    $start->gt(now()) &&
                    now()->diffInHours($start, false) >= 24 &&
                    in_array($reg->trangthai, ['Registered']);

                $canSubmit = 
                    $end->lt(now()) &&
                    $submitDeadline->gt(now()) &&
                    !$reg->mabaithi &&
                    in_array($reg->trangthai, ['Registered', 'Confirmed']);

                return (object)[
                    'id' => $reg->id,
                    'loaidangky' => $reg->loaidangky,
                    'tencuocthi' => $reg->tencuocthi,
                    'tendoithi' => $reg->tendoithi,
                    'vaitro' => $reg->vaitro,
                    'thoigianbatdau' => $start,
                    'thoigianketthuc' => $end,
                    'submitDeadline' => $submitDeadline,
                    'ngaydangky' => Carbon::parse($reg->ngaydangky),
                    'trangthai' => $reg->trangthai,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'statusColor' => $statusColor,
                    'canCancel' => $canCancel,
                    'canSubmit' => $canSubmit,
                    'mabaithi' => $reg->mabaithi,
                    'thoigiannop' => $reg->thoigiannop ? Carbon::parse($reg->thoigiannop) : null,
                    'trangthainop' => $reg->trangthainop,
                ];
            })->values();

        } catch (\Exception $e) {
            Log::error('Error fetching competition registrations: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * ⭐ Lấy hoạt động học thuật
     */
    private function getSinhVienActivities($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        $activities = collect([]);

        try {
            $doiThis = DB::table('thanhviendoithi as tv')
                ->join('doithi as dt', 'tv.madoithi', '=', 'dt.madoithi')
                ->join('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
                ->where('tv.masinhvien', $sinhVien->masinhvien)
                ->select(
                    'ct.tencuocthi',
                    'ct.thoigianbatdau',
                    'ct.thoigianketthuc',
                    'dt.tendoithi',
                    'tv.vaitro',
                    'tv.ngaythamgia',
                    'dt.trangthai'
                )
                ->get();

            foreach ($doiThis as $doi) {
                $activities->push([
                    'type' => 'Dự thi theo đội',
                    'title' => $doi->tencuocthi,
                    'subtitle' => 'Đội: ' . $doi->tendoithi,
                    'date' => $doi->ngaythamgia,
                    'role' => $doi->vaitro === 'TruongDoi' ? 'Trưởng đội' : 'Thành viên',
                    'status' => $doi->trangthai,
                    'icon' => 'fa-users',
                    'color' => 'blue',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching team activities: ' . $e->getMessage());
        }

        try {
            $dangKyCaNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                ->select(
                    'ct.tencuocthi',
                    'dkcn.ngaydangky',
                    'dkcn.trangthai'
                )
                ->get();

            foreach ($dangKyCaNhan as $dk) {
                $activities->push([
                    'type' => 'Dự thi cá nhân',
                    'title' => $dk->tencuocthi,
                    'subtitle' => null,
                    'date' => $dk->ngaydangky,
                    'role' => 'Thí sinh',
                    'status' => $dk->trangthai,
                    'icon' => 'fa-user-graduate',
                    'color' => 'green',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching personal registration: ' . $e->getMessage());
        }

        try {
            $hoatDongHoTro = DB::table('dangkyhoatdong as dkhd')
                ->join('hoatdonghotro as hd', 'dkhd.mahoatdong', '=', 'hd.mahoatdong')
                ->join('cuocthi as ct', 'hd.macuocthi', '=', 'ct.macuocthi')
                ->where('dkhd.masinhvien', $sinhVien->masinhvien)
                ->select(
                    'ct.tencuocthi',
                    'hd.tenhoatdong',
                    'hd.loaihoatdong',
                    'dkhd.ngaydangky',
                    'dkhd.trangthai',
                    'dkhd.diemdanhqr',
                    'dkhd.thoigiandiemdanh'
                )
                ->get();

            foreach ($hoatDongHoTro as $hd) {
                $loaiMap = [
                    'HoTroKyThuat' => 'Hỗ trợ kỹ thuật',
                    'CoVu' => 'Cổ vũ',
                    'ToChuc' => 'Tổ chức',
                ];

                $activities->push([
                    'type' => 'Hoạt động hỗ trợ',
                    'title' => $hd->tencuocthi,
                    'subtitle' => $hd->tenhoatdong,
                    'date' => $hd->ngaydangky,
                    'role' => $loaiMap[$hd->loaihoatdong] ?? $hd->loaihoatdong,
                    'status' => $hd->trangthai,
                    'icon' => 'fa-hands-helping',
                    'color' => 'purple',
                    'diem_danh' => $hd->diemdanhqr ? 'Đã điểm danh' : 'Chưa điểm danh',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching support activities: ' . $e->getMessage());
        }

        return $activities->sortByDesc('date')->values();
    }

    /**
     * ⭐ Lấy chứng nhận sinh viên
     */
    private function getSinhVienCertificates($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        try {
            $certificates = collect([]);

            $giaiCaNhan = DB::table('datgiai as dg')
                ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
                ->join('dangkycanhan as dkcn', 'dg.madangkycanhan', '=', 'dkcn.madangkycanhan')
                ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                ->where('dg.loaidangky', 'CaNhan')
                ->select(
                    'dg.madatgiai',
                    'ct.tencuocthi',
                    'dg.tengiai',
                    'dg.giaithuong',
                    'dg.diemrenluyen',
                    'dg.ngaytrao',
                    DB::raw("'CaNhan' as loai")
                )
                ->get();

            $giaiDoiNhom = DB::table('datgiai as dg')
                ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
                ->join('dangkydoithi as dkdt', 'dg.madangkydoi', '=', 'dkdt.madangkydoi')
                ->join('thanhviendoithi as tv', 'dkdt.madoithi', '=', 'tv.madoithi')
                ->where('tv.masinhvien', $sinhVien->masinhvien)
                ->where('dg.loaidangky', 'DoiNhom')
                ->select(
                    'dg.madatgiai',
                    'ct.tencuocthi',
                    'dg.tengiai',
                    'dg.giaithuong',
                    'dg.diemrenluyen',
                    'dg.ngaytrao',
                    DB::raw("'DoiNhom' as loai")
                )
                ->get();

            $certificates = $giaiCaNhan->concat($giaiDoiNhom);

            return $certificates->sortByDesc('ngaytrao')->map(function($cert) {
                return [
                    'id' => $cert->madatgiai,
                    'event' => $cert->tencuocthi,
                    'award' => $cert->tengiai,
                    'prize' => $cert->giaithuong,
                    'points' => $cert->diemrenluyen,
                    'date' => $cert->ngaytrao,
                    'type' => $cert->loai
                ];
            })->values();

        } catch (\Exception $e) {
            Log::error('Error fetching certificates: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * ⭐ Điểm rèn luyện
     */
    private function getDiemRenLuyenDetail($sinhVien)
    {
        if (!$sinhVien) return [
            'details' => collect([]),
            'total' => 0,
            'base' => 70,
            'bonus' => 0,
            'final' => 70,
        ];

        try {
            $diemRenLuyen = DB::table('diemrenluyen as drl')
                ->leftJoin('cuocthi as ct', 'drl.macuocthi', '=', 'ct.macuocthi')
                ->leftJoin('hoatdonghotro as hd', 'drl.mahoatdong', '=', 'hd.mahoatdong')
                ->where('drl.masinhvien', $sinhVien->masinhvien)
                ->select(
                    'drl.madiemrl',
                    'drl.loaihoatdong',
                    'drl.diem',
                    'drl.mota',
                    'drl.ngaycong',
                    'ct.tencuocthi',
                    'hd.tenhoatdong',
                    'hd.loaihoatdong as loai_hoatdong_hotro',
                    'hd.thoigianbatdau',
                    'hd.diadiem'
                )
                ->orderBy('drl.ngaycong', 'desc')
                ->get();

            $details = [];
            $totalPoints = 0;

            $loaiMap = [
                'DatGiai' => 'Đạt giải',
                'DuThi' => 'Dự thi',
                'HoTro' => 'Hỗ trợ',
                'ToChuc' => 'Tổ chức',
                'CoVu' => 'Cổ vũ',
            ];

            foreach ($diemRenLuyen as $diem) {
                $loaiHoatDong = $loaiMap[$diem->loaihoatdong] ?? $diem->loaihoatdong;
                $title = $diem->tencuocthi ?? $diem->tenhoatdong ?? $diem->mota;

                $details[] = [
                    'loai' => $loaiHoatDong,
                    'title' => $title,
                    'diem' => $diem->diem,
                    'ngay' => $diem->ngaycong,
                    'mota' => $diem->mota,
                ];

                $totalPoints += $diem->diem;
            }

            return [
                'details' => collect($details),
                'total' => $totalPoints,
                'base' => 70,
                'bonus' => $totalPoints,
                'final' => 70 + $totalPoints,
            ];

        } catch (\Exception $e) {
            Log::error('Error fetching diem ren luyen: ' . $e->getMessage());
            return [
                'details' => collect([]),
                'total' => 0,
                'base' => 70,
                'bonus' => 0,
                'final' => 70,
            ];
        }
    }

    /**
     * API: Cập nhật ảnh đại diện
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::guard('api')->user();

        if ($request->hasFile('avatar')) {
            if ($user->anhdaidien && Storage::disk('public')->exists($user->anhdaidien)) {
                Storage::disk('public')->delete($user->anhdaidien);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            
            $user->update(['anhdaidien' => $path]);

            return response()->json([
                'success' => true,
                'avatar' => $path,
                'message' => 'Cập nhật ảnh đại diện thành công'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thể upload ảnh'
        ]);
    }

    /**
     * API: Cập nhật thông tin cá nhân
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::guard('api')->user();

        $request->validate([
            'hoten' => 'required|string|max:150',
            'email' => 'required|email|unique:nguoidung,email,' . $user->manguoidung . ',manguoidung',
            'sodienthoai' => 'nullable|string|max:20|regex:/^[0-9]{10,11}$/',
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
            ]);

            if ($user->vaitro === 'SinhVien') {
                $sv = $user->sinhVien;

                $sv->update([
                    'malop' => $request->malop ?? $sv->malop,
                    'namnhaphoc' => $request->namnhaphoc ?? $sv->namnhaphoc,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Hủy đăng ký hoạt động hỗ trợ
     */
    public function cancelActivityRegistration($madangkyhoatdong)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->vaitro !== 'SinhVien') {
            return response()->json(['success' => false, 'message' => 'Chỉ sinh viên mới được thao tác'], 403);
        }

        $sinhVien = $user->sinhVien;

        try {
            $registration = DB::table('dangkyhoatdong as dkhd')
                ->join('hoatdonghotro as hd', 'dkhd.mahoatdong', '=', 'hd.mahoatdong')
                ->where('dkhd.madangkyhoatdong', $madangkyhoatdong)
                ->where('dkhd.masinhvien', $sinhVien->masinhvien)
                ->whereIn('hd.loaihoatdong', ['CoVu', 'ToChuc', 'HoTroKyThuat'])
                ->select('dkhd.*', 'hd.thoigianbatdau', 'hd.tenhoatdong', 'hd.loaihoatdong')
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký']);
            }

            if ($registration->diemdanhqr) {
                return response()->json(['success' => false, 'message' => 'Không thể hủy đăng ký đã điểm danh']);
            }

            $hoursUntilStart = now()->diffInHours(Carbon::parse($registration->thoigianbatdau), false);
            
            if ($hoursUntilStart < 24) {
                return response()->json(['success' => false, 'message' => 'Không thể hủy trong vòng 24 giờ']);
            }

            DB::table('dangkyhoatdong')
                ->where('madangkyhoatdong', $madangkyhoatdong)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đăng ký thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error canceling: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Hủy đăng ký dự thi (cá nhân/đội)
     */
    public function cancelCompetitionRegistration($id)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->vaitro !== 'SinhVien') {
            return response()->json(['success' => false, 'message' => 'Only student allowed'], 403);
        }

        $sinhVien = $user->sinhVien;

        try {
            DB::beginTransaction();

            // Cá nhân
            $dangKyCaNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                ->where('dkcn.madangkycanhan', $id)
                ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                ->select('dkcn.*', 'ct.thoigianbatdau')
                ->first();

            if ($dangKyCaNhan) {
                if (Carbon::parse($dangKyCaNhan->thoigianbatdau)->lt(now())) {
                    return response()->json(['success' => false,'message' => 'Cuộc thi đã bắt đầu, không được hủy']);
                }

                DB::table('dangkycanhan')->where('madangkycanhan', $id)->delete();
                DB::commit();
                
                return response()->json(['success' => true, 'message' => 'Hủy đăng ký cá nhân thành công']);
            }

            // Đội
            $dangKyDoi = DB::table('dangkydoithi as dkdt')
                ->join('doithi as dt', 'dkdt.madoithi', '=', 'dt.madoithi')
                ->join('cuocthi as ct', 'dkdt.macuocthi', '=', 'ct.macuocthi')
                ->where('dkdt.madangkydoi', $id)
                ->select('dkdt.*', 'dt.madoithi', 'dt.matruongdoi', 'dt.tendoithi', 'ct.thoigianbatdau')
                ->first();

            if ($dangKyDoi) {

                if ($dangKyDoi->matruongdoi !== $sinhVien->masinhvien) {
                    return response()->json(['success' => false, 'message' => 'Chỉ TRƯỞNG ĐỘI mới được hủy']);
                }

                if (Carbon::parse($dangKyDoi->thoigianbatdau)->lt(now())) {
                    return response()->json(['success' => false,'message' => 'Cuộc thi đã bắt đầu, không được hủy']);
                }

                DB::table('thanhviendoithi')->where('madoithi', $dangKyDoi->madoithi)->delete();
                DB::table('dangkydoithi')->where('madangkydoi', $id)->delete();
                DB::table('doithi')->where('madoithi', $dangKyDoi->madoithi)->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Hủy đăng ký đội {$dangKyDoi->tendoithi} thành công"
                ]);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Show form nộp bài thi
     */
    public function showSubmitExam($id, $loaidangky)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->vaitro !== 'SinhVien') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $sinhVien = $user->sinhVien;

        try {
            if ($loaidangky === 'CaNhan') {
                $dangky = DB::table('dangkycanhan as dkcn')
                    ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                    ->leftJoin('dethi as dt', 'ct.macuocthi', '=', 'dt.macuocthi')
                    ->where('dkcn.madangkycanhan', $id)
                    ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                    ->first();
            } else {
                $dangky = DB::table('dangkydoithi as dkdt')
                    ->join('doithi as doi', 'dkdt.madoithi', '=', 'doi.madoithi')
                    ->join('cuocthi as ct', 'dkdt.macuocthi', '=', 'ct.macuocthi')
                    ->join('thanhviendoithi as tv', 'doi.madoithi', '=', 'tv.madoithi')
                    ->leftJoin('dethi as dt', 'ct.macuocthi', '=', 'dt.macuocthi')
                    ->where('dkdt.madangkydoi', $id)
                    ->where('tv.masinhvien', $sinhVien->masinhvien)
                    ->first();
            }

            if (!$dangky) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký']);
            }

            $now = now();
            $end = Carbon::parse($dangky->thoigianketthuc);
            $submitDeadline = $end->copy()->addDay();

            if ($now->lt($end)) {
                return response()->json(['success' => false, 'message' => 'Cuộc thi chưa kết thúc']);
            }

            if ($now->gt($submitDeadline)) {
                return response()->json(['success' => false, 'message' => 'Hết hạn nộp bài']);
            }

            $baiThi = DB::table('baithi')
                ->where($loaidangky === 'CaNhan' ? 'madangkycanhan' : 'madangkydoi', $id)
                ->where('loaidangky', $loaidangky)
                ->first();

            if ($baiThi) {
                return response()->json(['success' => false, 'message' => 'Bạn đã nộp bài rồi']);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'dangky' => $dangky,
                    'submitDeadline' => $submitDeadline
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Xử lý nộp bài thi
     */
    public function submitExam(Request $request, $id, $loaidangky)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->vaitro !== 'SinhVien') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $sinhVien = $user->sinhVien;

        $request->validate([
            'filebaithi' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if ($loaidangky === 'CaNhan') {
                $dangky = DB::table('dangkycanhan as dkcn')
                    ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                    ->leftJoin('dethi as dt', 'ct.macuocthi', '=', 'dt.macuocthi')
                    ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
                    ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
                    ->where('dkcn.madangkycanhan', $id)
                    ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                    ->first();
            } else {
                $dangky = DB::table('dangkydoithi as dkdt')
                    ->join('cuocthi as ct', 'dkdt.macuocthi', '=', 'ct.macuocthi')
                    ->leftJoin('dethi as dt', 'ct.macuocthi', '=', 'dt.macuocthi')
                    ->join('doithi as doi', 'dkdt.madoithi', '=', 'doi.madoithi')
                    ->join('thanhviendoithi as tv', 'doi.madoithi', '=', 'tv.madoithi')
                    ->leftJoin('sinhvien as sv', 'tv.masinhvien', '=', 'sv.masinhvien')
                    ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
                    ->where('dkdt.madangkydoi', $id)
                    ->where('tv.masinhvien', $sinhVien->masinhvien)
                    ->first();
            }

            if (!$dangky) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký']);
            }

            $now = now();
            $end = Carbon::parse($dangky->thoigianketthuc);
            $submitDeadline = $end->copy()->addDay();

            if ($now->lt($end) || $now->gt($submitDeadline)) {
                return response()->json(['success' => false, 'message' => 'Không trong thời gian nộp bài']);
            }

            $exists = DB::table('baithi')
                ->where($loaidangky === 'CaNhan' ? 'madangkycanhan' : 'madangkydoi', $id)
                ->where('loaidangky', $loaidangky)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Bạn đã nộp bài']);
            }

            $file = $request->file('filebaithi');
            $extension = $file->getClientOriginalExtension();
            $maBaiThi = 'BT' . time() . rand(1000, 9999);

            $fileName = sprintf(
                '%s_%s_%s_%s.%s',
                $dangky->macuocthi,
                $loaidangky === 'CaNhan' ? $dangky->masinhvien : $dangky->madoithi,
                $this->slugify($loaidangky === 'CaNhan' ? $dangky->hoten : $dangky->tendoithi),
                $maBaiThi,
                $extension
            );

            $path = $file->storeAs('baithis', $fileName, 'public');

            DB::table('baithi')->insert([
                'mabaithi' => $maBaiThi,
                'madethi' => $dangky->madethi,
                'madangkycanhan' => $loaidangky === 'CaNhan' ? $id : null,
                'madangkydoi' => $loaidangky === 'DoiNhom' ? $id : null,
                'loaidangky' => $loaidangky,
                'filebaithi' => $path,
                'thoigiannop' => now(),
                'trangthai' => 'Submitted',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nộp bài thi thành công',
                'file' => $path
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Helper slug
     */
    private function slugify($string)
    {
        $string = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $string);
        $string = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $string);
        $string = preg_replace('/[ìíịỉĩ]/u', 'i', $string);
        $string = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $string);
        $string = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $string);
        $string = preg_replace('/[ỳýỵỷỹ]/u', 'y', $string);
        $string = preg_replace('/[đ]/u', 'd', $string);

        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

        return substr($string, 0, 50);
    }
}

