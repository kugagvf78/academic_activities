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
                ->with(['lop.giangvienchunhiem.nguoiDung'])
                ->first();

            $activities = $this->getSinhVienActivities($profile);
            $certificates = $this->getSinhVienCertificates($profile);
            $diemRenLuyen = $this->getDiemRenLuyenDetail($profile);
            $competitionRegistrations = $this->getCompetitionRegistrations($profile);

            // Lấy đăng ký hoạt động hỗ trợ + cổ vũ
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

            $registrations = $registrations->map(function ($reg) {
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

                return [
                    'madangkyhoatdong' => $reg->madangkyhoatdong,
                    'tencuocthi' => $reg->tencuocthi,
                    'tenhoatdong' => $reg->tenhoatdong,
                    'loaihoatdong' => $reg->loaihoatdong,
                    'thoigianbatdau' => $start->toISOString(),
                    'thoigianketthuc' => $end->toISOString(),
                    'diadiem' => $reg->diadiem,
                    'diemrenluyen' => $reg->diemrenluyen,
                    'ngaydangky' => Carbon::parse($reg->ngaydangky)->toISOString(),
                    'trangthai' => $reg->trangthai,
                    'diemdanhqr' => $reg->diemdanhqr,
                    'thoigiandiemdanh' => $reg->thoigiandiemdanh ? Carbon::parse($reg->thoigiandiemdanh)->toISOString() : null,
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
                    'diemRenLuyen' => null,
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
     * Lấy hoạt động học thuật của sinh viên
     */
    private function getSinhVienActivities($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        $activities = collect([]);

        try {
            // 1️⃣ DỰ THI THEO ĐỘI
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
                    'date' => Carbon::parse($doi->ngaythamgia)->toISOString(),
                    'role' => $doi->vaitro === 'TruongDoi' ? 'Trưởng đội' : 'Thành viên',
                    'status' => $doi->trangthai,
                    'icon' => 'users',
                    'color' => 'blue',
                ]);
            }

            // 2️⃣ DỰ THI CÁ NHÂN
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
                    'date' => Carbon::parse($dk->ngaydangky)->toISOString(),
                    'role' => 'Thí sinh',
                    'status' => $dk->trangthai,
                    'icon' => 'user-graduate',
                    'color' => 'green',
                ]);
            }

            // 3️⃣ HOẠT ĐỘNG HỖ TRỢ / CỔ VŨ
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
                    'date' => Carbon::parse($hd->ngaydangky)->toISOString(),
                    'role' => $loaiMap[$hd->loaihoatdong] ?? $hd->loaihoatdong,
                    'status' => $hd->trangthai,
                    'icon' => 'hands-helping',
                    'color' => 'purple',
                    'attendance' => $hd->diemdanhqr ? 'Đã điểm danh' : 'Chưa điểm danh',
                    'attendanceTime' => $hd->thoigiandiemdanh
                        ? Carbon::parse($hd->thoigiandiemdanh)->toISOString()
                        : null,
                ]);
            }

            return $activities->sortByDesc('date')->values();
        } catch (\Exception $e) {
            Log::error('Error fetching activities (API): ' . $e->getMessage());
            return collect([]);
        }
    }


    /**
     * Lấy chứng nhận (đạt giải) - Dùng bảng DatGiai
     */
    private function getSinhVienCertificates($profile)
    {
        if (!$profile) return [];

        try {
            $results = DB::table('datgiai as dg')
                ->join('cuocthi as ct', 'dg.macuocthi', '=', 'ct.macuocthi')
                ->leftJoin('dangkycanhan as dkcn', function ($join) {
                    $join->on('dg.madangkycanhan', '=', 'dkcn.madangkycanhan')
                        ->where('dg.loaidangky', '=', 'CaNhan');
                })
                ->leftJoin('dangkydoithi as dkdt', function ($join) {
                    $join->on('dg.madangkydoi', '=', 'dkdt.madangkydoi')
                        ->where('dg.loaidangky', '=', 'DoiNhom');
                })
                ->leftJoin('doithi as doi', 'dkdt.madoithi', '=', 'doi.madoithi')
                ->where(function ($query) use ($profile) {
                    $query->where('dkcn.masinhvien', $profile->masinhvien)
                        ->orWhereExists(function ($q) use ($profile) {
                            $q->select(DB::raw(1))
                                ->from('thanhviendoithi')
                                ->whereColumn('thanhviendoithi.madoithi', 'doi.madoithi')
                                ->where('thanhviendoithi.masinhvien', $profile->masinhvien);
                        });
                })
                ->select(
                    'dg.madatgiai',
                    'dg.macuocthi',
                    'dg.madangkycanhan',
                    'dg.madangkydoi',
                    'dg.loaidangky',
                    'dg.tengiai',
                    'dg.giaithuong',
                    'dg.diemrenluyen',
                    'dg.ngaytrao',
                    'ct.tencuocthi',
                    DB::raw('CASE WHEN dg.loaidangky = \'CaNhan\' THEN NULL ELSE doi.tendoithi END as tendoithi')
                )
                ->orderBy('dg.ngaytrao', 'desc')
                ->get();

            return $results;
        } catch (\Exception $e) {
            Log::error('Error fetching certificates: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy điểm rèn luyện chi tiết
     */
    private function getDiemRenLuyenDetail($profile)
    {
        if (!$profile) return null;

        try {
            // ✅ Điểm cơ bản của sinh viên
            $diemCoban = 70;

            // Lấy tất cả điểm rèn luyện của sinh viên
            $diemList = DB::table('diemrenluyen')
                ->where('masinhvien', $profile->masinhvien)
                ->orderBy('ngaycong', 'desc')
                ->get();

            if ($diemList->isEmpty()) {
                // ✅ Chưa có điểm thưởng nào - vẫn có 70 điểm
                return [
                    'details' => [],
                    'total' => $diemCoban,
                    'base' => $diemCoban,
                    'bonus' => 0,
                    'final' => $diemCoban,
                ];
            }

            // ✅ Tính tổng điểm thưởng
            $tongDiemThuong = $diemList->sum('diem');
            $tongDiem = $diemCoban + $tongDiemThuong;

            // Format chi tiết
            $details = $diemList->map(function ($item) {
                return [
                    'loai' => $item->loaihoatdong ?? 'Khác',
                    'title' => $this->formatTitleDiemRL($item),
                    'diem' => $item->diem,
                    'ngay' => $item->ngaycong ? Carbon::parse($item->ngaycong)->toISOString() : null,
                    'mota' => $item->mota ?? '',
                    'color' => $this->getColorByLoai($item->loaihoatdong),
                    'icon' => $this->getIconByLoai($item->loaihoatdong),
                ];
            })->toArray();

            return [
                'details' => $details,
                'total' => $tongDiem,        // ✅ 70 + bonus
                'base' => $diemCoban,        // ✅ 70
                'bonus' => $tongDiemThuong,  // ✅ Tổng điểm thưởng
                'final' => $tongDiem,        // ✅ = total
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching diem ren luyen: ' . $e->getMessage());
            return null;
        }
    }

    private function formatTitleDiemRL($item)
    {
        if ($item->loaihoatdong === 'DatGiai') {
            return 'Đạt giải';
        } elseif (in_array($item->loaihoatdong, ['CoVu', 'ToChuc', 'HoTroKyThuat'])) {
            return 'Hoạt động hỗ trợ';
        }
        return $item->mota ?? 'Hoạt động khác';
    }

    private function getColorByLoai($loai)
    {
        switch ($loai) {
            case 'DatGiai':
                return 'gold';
            case 'CoVu':
                return 'blue';
            case 'ToChuc':
                return 'green';
            case 'HoTroKyThuat':
                return 'purple';
            default:
                return 'gray';
        }
    }

    private function getIconByLoai($loai)
    {
        switch ($loai) {
            case 'DatGiai':
                return 'trophy';
            case 'CoVu':
                return 'flag';
            case 'ToChuc':
                return 'users';
            case 'HoTroKyThuat':
                return 'tools';
            default:
                return 'star';
        }
    }

    /**
     * Lấy đăng ký dự thi
     */
    private function getCompetitionRegistrations($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        try {
            // 1. Đăng ký cá nhân
            $caNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                ->leftJoin('baithi as bt', function ($join) {
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
                ->leftJoin('baithi as bt', function ($join) {
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

            // Gộp và xử lý
            $registrations = $caNhan->merge($doiNhom)->map(function ($reg) {
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

                $canCancel = !$reg->mabaithi &&
                    $start->gt($now) &&
                    $now->diffInHours($start, false) >= 24;

                $canSubmit = $now->gte($start) &&
                    $now->lte($end) &&
                    !$reg->mabaithi;

                return [
                    'id' => $reg->id,
                    'tencuocthi' => $reg->tencuocthi,
                    'thoigianbatdau' => $start->toISOString(),
                    'thoigianketthuc' => $end->toISOString(),
                    'trangthaicuocthi' => $reg->trangthaicuocthi,
                    'ngaydangky' => Carbon::parse($reg->ngaydangky)->toISOString(),
                    'trangthai' => $reg->trangthai,
                    'loaidangky' => $reg->loaidangky,
                    'tendoithi' => $reg->tendoithi,
                    'vaitro' => $reg->vaitro,
                    'mabaithi' => $reg->mabaithi,
                    'thoigiannop' => $reg->thoigiannop ? Carbon::parse($reg->thoigiannop)->toISOString() : null,
                    'trangthainop' => $reg->trangthainop,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'statusColor' => $statusColor,
                    'canCancel' => $canCancel,
                    'canSubmit' => $canSubmit,
                ];
            })->sortByDesc('ngaydangky')->values();

            return $registrations;
        } catch (\Exception $e) {
            Log::error('Error fetching competition registrations: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * API: Update Avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh',
            'avatar.image' => 'File phải là ảnh',
            'avatar.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, hoặc gif',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB',
        ]);

        $user = Auth::guard('api')->user();

        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($user->anhdaidien && Storage::disk('public')->exists($user->anhdaidien)) {
                Storage::disk('public')->delete($user->anhdaidien);
            }

            // Lưu ảnh mới
            $path = $request->file('avatar')->store('avatars', 'public');

            // Cập nhật đường dẫn ảnh vào cơ sở dữ liệu
            $user->update(['anhdaidien' => $path]);

            return back()->with('success', 'Cập nhật ảnh đại diện thành công!');
        }

        return back()->with('error', 'Có lỗi xảy ra khi tải ảnh lên');
    }

    /**
     * API: Update Info
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'hoten' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:nguoidung,email,' . $user->manguoidung . ',manguoidung',
            'sodienthoai' => 'nullable|string|max:15',
        ]);

        try {
            DB::beginTransaction();

            // Update bảng nguoidung
            $updateData = array_filter([
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
            ]);

            if (!empty($updateData)) {
                DB::table('nguoidung')
                    ->where('manguoidung', $user->manguoidung)
                    ->update($updateData);
            }

            // Update bảng sinhvien nếu cần
            if ($user->vaitro === 'SinhVien' && $request->has('malop')) {
                DB::table('sinhvien')
                    ->where('masinhvien', $user->sinhVien->masinhvien)
                    ->update(['malop' => $request->malop]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating info: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Hủy đăng ký hoạt động
     */
    public function cancelActivityRegistration($madangkyhoatdong)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->vaitro !== 'SinhVien') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $registration = DB::table('dangkyhoatdong as dkhd')
                ->join('hoatdonghotro as hd', 'dkhd.mahoatdong', '=', 'hd.mahoatdong')
                ->where('dkhd.madangkyhoatdong', $madangkyhoatdong)
                ->where('dkhd.masinhvien', $user->sinhVien->masinhvien)
                ->select('dkhd.*', 'hd.thoigianbatdau')
                ->first();

            if (!$registration) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký'], 404);
            }

            // Kiểm tra điều kiện hủy
            if ($registration->diemdanhqr) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không thể hủy vì bạn đã điểm danh'], 400);
            }

            $now = now();
            $start = Carbon::parse($registration->thoigianbatdau);

            if ($start->lte($now)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không thể hủy vì hoạt động đã bắt đầu'], 400);
            }

            if ($now->diffInHours($start, false) < 24) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không thể hủy trong vòng 24h trước khi bắt đầu'], 400);
            }

            // Xóa đăng ký
            DB::table('dangkyhoatdong')
                ->where('madangkyhoatdong', $madangkyhoatdong)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hủy đăng ký thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel activity error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Hủy đăng ký cuộc thi
     */
    public function cancelCompetitionRegistration($id)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->vaitro !== 'SinhVien') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Kiểm tra cá nhân
            $caNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                ->where('dkcn.madangkycanhan', $id)
                ->where('dkcn.masinhvien', $user->sinhVien->masinhvien)
                ->select('dkcn.*', 'ct.thoigianbatdau')
                ->first();

            if ($caNhan) {
                $start = Carbon::parse($caNhan->thoigianbatdau);

                if ($start->lte(now())) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Cuộc thi đã bắt đầu'], 400);
                }

                if (now()->diffInHours($start, false) < 24) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Không thể hủy trong vòng 24h'], 400);
                }

                DB::table('dangkycanhan')->where('madangkycanhan', $id)->delete();
                DB::commit();

                return response()->json(['success' => true, 'message' => 'Hủy đăng ký thành công']);
            }

            // Kiểm tra đội nhóm
            $doiNhom = DB::table('dangkydoithi as dkdt')
                ->join('doithi as dt', 'dkdt.madoithi', '=', 'dt.madoithi')
                ->join('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
                ->join('thanhviendoithi as tv', 'dt.madoithi', '=', 'tv.madoithi')
                ->where('dkdt.madangkydoi', $id)
                ->where('tv.masinhvien', $user->sinhVien->masinhvien)
                ->select('dkdt.*', 'ct.thoigianbatdau', 'tv.vaitro', 'dt.madoithi')
                ->first();

            if ($doiNhom) {
                if ($doiNhom->vaitro !== 'TruongNhom') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Chỉ trưởng nhóm mới có thể hủy'], 403);
                }

                $start = Carbon::parse($doiNhom->thoigianbatdau);

                if ($start->lte(now())) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Cuộc thi đã bắt đầu'], 400);
                }

                if (now()->diffInHours($start, false) < 24) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Không thể hủy trong vòng 24h'], 400);
                }

                // Xóa đăng ký đội
                DB::table('dangkydoithi')->where('madangkydoi', $id)->delete();

                // Xóa thành viên
                DB::table('thanhviendoithi')->where('madoithi', $doiNhom->madoithi)->delete();

                // Xóa đội
                DB::table('doithi')->where('madoithi', $doiNhom->madoithi)->delete();

                DB::commit();

                return response()->json(['success' => true, 'message' => 'Hủy đăng ký thành công']);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel competition error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
                    ->select('dkcn.*', 'ct.*', 'dt.madethi', 'dt.tendethi')
                    ->first();
            } else {
                $dangky = DB::table('dangkydoithi as dkdt')
                    ->join('doithi as doi', 'dkdt.madoithi', '=', 'doi.madoithi')
                    ->join('cuocthi as ct', 'dkdt.macuocthi', '=', 'ct.macuocthi')
                    ->join('thanhviendoithi as tv', 'doi.madoithi', '=', 'tv.madoithi')
                    ->leftJoin('dethi as dt', 'ct.macuocthi', '=', 'dt.macuocthi')
                    ->where('dkdt.madangkydoi', $id)
                    ->where('tv.masinhvien', $sinhVien->masinhvien)
                    ->select('dkdt.*', 'ct.*', 'dt.madethi', 'dt.tendethi', 'doi.tendoithi')
                    ->first();
            }

            if (!$dangky) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký'], 404);
            }

            $now = now();
            $start = Carbon::parse($dangky->thoigianbatdau);
            $end = Carbon::parse($dangky->thoigianketthuc);

            // Kiểm tra thời gian nộp (trong khi thi đang diễn ra)
            if ($now->lt($start)) {
                return response()->json(['success' => false, 'message' => 'Cuộc thi chưa bắt đầu'], 400);
            }

            if ($now->gt($end)) {
                return response()->json(['success' => false, 'message' => 'Cuộc thi đã kết thúc'], 400);
            }

            // Kiểm tra đã nộp chưa
            $baiThi = DB::table('baithi')
                ->where($loaidangky === 'CaNhan' ? 'madangkycanhan' : 'madangkydoi', $id)
                ->where('loaidangky', $loaidangky)
                ->first();

            if ($baiThi) {
                return response()->json(['success' => false, 'message' => 'Bạn đã nộp bài rồi'], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'dangky' => $dangky,
                    'canSubmit' => true
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Show submit exam error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
                    ->select('dkcn.*', 'ct.*', 'dt.madethi', 'sv.masinhvien', 'nd.hoten')
                    ->first();
            } else {
                $dangky = DB::table('dangkydoithi as dkdt')
                    ->join('cuocthi as ct', 'dkdt.macuocthi', '=', 'ct.macuocthi')
                    ->leftJoin('dethi as dt', 'ct.macuocthi', '=', 'dt.macuocthi')
                    ->join('doithi as doi', 'dkdt.madoithi', '=', 'doi.madoithi')
                    ->join('thanhviendoithi as tv', 'doi.madoithi', '=', 'tv.madoithi')
                    ->where('dkdt.madangkydoi', $id)
                    ->where('tv.masinhvien', $sinhVien->masinhvien)
                    ->select('dkdt.*', 'ct.*', 'dt.madethi', 'doi.tendoithi', 'doi.madoithi')
                    ->first();
            }

            if (!$dangky) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đăng ký'], 404);
            }

            $now = now();
            $start = Carbon::parse($dangky->thoigianbatdau);
            $end = Carbon::parse($dangky->thoigianketthuc);

            if ($now->lt($start)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Cuộc thi chưa bắt đầu'], 400);
            }

            if ($now->gt($end)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Đã hết hạn nộp bài'], 400);
            }

            // Kiểm tra đã nộp chưa
            $exists = DB::table('baithi')
                ->where($loaidangky === 'CaNhan' ? 'madangkycanhan' : 'madangkydoi', $id)
                ->where('loaidangky', $loaidangky)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Bạn đã nộp bài'], 400);
            }

            // Upload file
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

            // Lưu database
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
            Log::error('Submit exam error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
