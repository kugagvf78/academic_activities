<?php

namespace App\Http\Controllers\Web\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Lop;
use Illuminate\Support\Facades\Log;
use App\Models\SinhVien;
use App\Models\GiangVien;
use App\Models\DangKyDuThi;
use App\Models\DangKyHoatDong;
use App\Models\DiemRenLuyen;
use App\Models\DatGiai;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ cá nhân
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        // Lấy thông tin chi tiết theo vai trò
        if ($user->vaitro === 'SinhVien') {
            $profile = $user->sinhVien()
                ->with([
                    'lop.giangvienchunhiem.nguoiDung'
                ])
                ->first();
            
            // Lấy danh sách lớp để chọn (nếu cần)
            $danhSachLop = Lop::with('giangvienchunhiem.nguoiDung')
                ->orderBy('tenlop')
                ->get();
            
            // Lấy hoạt động học thuật của sinh viên
            $activities = $this->getSinhVienActivities($profile);
            
            // Lấy chứng nhận (đạt giải)
            $certificates = $this->getSinhVienCertificates($profile);
            
            // Lấy điểm rèn luyện chi tiết
            $diemRenLuyen = $this->getDiemRenLuyenDetail($profile);

            // ⭐ MỚI: Lấy danh sách đăng ký DỰ THI (cả cá nhân và đội)
            $competitionRegistrations = $this->getCompetitionRegistrations($profile);

            // Lấy danh sách đăng ký cổ vũ VÀ hỗ trợ
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

            // Transform data
            $registrations = $registrations->map(function($reg) {
                $now = now();
                $start = \Carbon\Carbon::parse($reg->thoigianbatdau);
                $end = \Carbon\Carbon::parse($reg->thoigianketthuc);

                // Xác định trạng thái
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

                // Có thể hủy không (chỉ hủy được nếu chưa điểm danh và còn >24h)
                $canCancel = !$reg->diemdanhqr && 
                            $start->gt($now) && 
                            $now->diffInHours($start, false) >= 24;

                return (object)[
                    'madangkyhoatdong' => $reg->madangkyhoatdong,
                    'tencuocthi' => $reg->tencuocthi,
                    'tenhoatdong' => $reg->tenhoatdong,
                    'loaihoatdong' => $reg->loaihoatdong,
                    'thoigianbatdau' => $start,
                    'thoigianketthuc' => $end,
                    'diadiem' => $reg->diadiem,
                    'diemrenluyen' => $reg->diemrenluyen,
                    'ngaydangky' => \Carbon\Carbon::parse($reg->ngaydangky),
                    'trangthai' => $reg->trangthai,
                    'diemdanhqr' => $reg->diemdanhqr,
                    'thoigiandiemdanh' => $reg->thoigiandiemdanh ? \Carbon\Carbon::parse($reg->thoigiandiemdanh) : null,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'statusColor' => $statusColor,
                    'canCancel' => $canCancel,
                ];
            });
            
        } else if ($user->vaitro === 'GiangVien') {
            $profile = $user->giangVien()
                ->with(['boMon', 'lopChuNhiem'])
                ->first();
            $activities = [];
            $certificates = [];
            $danhSachLop = [];
            $diemRenLuyen = [];
            $registrations = collect([]);
            $competitionRegistrations = collect([]); // ⭐ Thêm cho giảng viên
        } else {
            $profile = null;
            $activities = [];
            $certificates = [];
            $danhSachLop = [];
            $diemRenLuyen = [];
            $registrations = collect([]);
            $competitionRegistrations = collect([]); // ⭐ Thêm cho admin
        }

        return view('client.profile.profile', compact(
            'user', 
            'profile', 
            'activities', 
            'certificates', 
            'danhSachLop',
            'diemRenLuyen',
            'registrations',
            'competitionRegistrations' // ⭐ THÊM BIẾN MỚI
        ));
    }

    /**
     * ⭐ HÀM MỚI: Lấy danh sách đăng ký dự thi
     */
    private function getCompetitionRegistrations($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        try {
            $registrations = collect([]);

            // 1. Đăng ký CÁ NHÂN
            $caNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
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
                    DB::raw("NULL as vaitro")
                )
                ->get();

            // 2. Đăng ký THEO ĐỘI (qua ThanhVienDoiThi)
            $doiNhom = DB::table('thanhviendoithi as tv')
                ->join('doithi as dt', 'tv.madoithi', '=', 'dt.madoithi')
                ->join('dangkydoithi as dkdt', 'dt.madoithi', '=', 'dkdt.madoithi')
                ->join('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
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
                    'tv.vaitro'
                )
                ->get();

            // Gộp lại và sắp xếp
            $registrations = $caNhan->concat($doiNhom)->sortByDesc('ngaydangky');

            // Thêm thông tin trạng thái và khả năng hủy
            return $registrations->map(function($reg) {
                $now = now();
                $start = \Carbon\Carbon::parse($reg->thoigianbatdau);
                $end = \Carbon\Carbon::parse($reg->thoigianketthuc);

                // Xác định status
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

                // Có thể hủy không (chỉ hủy được nếu còn >24h và chưa bắt đầu)
                $canCancel = $start->gt($now) && 
                            $now->diffInHours($start, false) >= 24 &&
                            in_array($reg->trangthai, ['Registered']);

                return (object)[
                    'id' => $reg->id,
                    'loaidangky' => $reg->loaidangky,
                    'tencuocthi' => $reg->tencuocthi,
                    'tendoithi' => $reg->tendoithi,
                    'vaitro' => $reg->vaitro,
                    'thoigianbatdau' => $start,
                    'thoigianketthuc' => $end,
                    'ngaydangky' => \Carbon\Carbon::parse($reg->ngaydangky),
                    'trangthai' => $reg->trangthai,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'statusColor' => $statusColor,
                    'canCancel' => $canCancel,
                ];
            })->values();

        } catch (\Exception $e) {
            Log::error('Error fetching competition registrations: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy hoạt động học thuật của sinh viên
     */
    private function getSinhVienActivities($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        $activities = collect([]);

        try {
            // 1. Lấy các đội thi mà sinh viên tham gia (GIỮ NGUYÊN - ĐÚNG RỒI)
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
            // 2. ⚠️ SỬA ĐÂY: Lấy đăng ký dự thi cá nhân từ bảng DANGKYCANHAN
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
            // 3. Lấy các hoạt động hỗ trợ đã đăng ký (GIỮ NGUYÊN - ĐÚNG RỒI)
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
     * Lấy chứng nhận của sinh viên (các giải đã đạt)
     */
    private function getSinhVienCertificates($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        try {
            $certificates = collect([]);

            // 1. Lấy giải từ đăng ký CÁ NHÂN
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

            // 2. Lấy giải từ ĐĂNG KÝ ĐỘI (qua ThanhVienDoiThi)
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

            // Gộp 2 loại giải lại
            $certificates = $giaiCaNhan->concat($giaiDoiNhom);

            return $certificates->sortByDesc('ngaytrao')->map(function($cert) {
                return [
                    'id' => $cert->madatgiai,
                    'event' => $cert->tencuocthi,
                    'award' => $cert->tengiai,
                    'prize' => $cert->giaithuong,
                    'points' => $cert->diemrenluyen,
                    'date' => $cert->ngaytrao,
                    'type' => $cert->loai, // Thêm type để biết là cá nhân hay đội
                ];
            })->values();

        } catch (\Exception $e) {
            Log::error('Error fetching certificates: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy chi tiết điểm rèn luyện của sinh viên
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
                    'hd.tenhoatdong'
                )
                ->orderBy('drl.ngaycong', 'desc')
                ->get();

            $details = [];
            $totalPoints = 0;

            foreach ($diemRenLuyen as $diem) {
                $loaiMap = [
                    'DatGiai' => 'Đạt giải',
                    'DuThi' => 'Dự thi',
                    'HoTro' => 'Hỗ trợ',
                    'ToChuc' => 'Tổ chức',
                ];

                $title = $diem->tencuocthi ?? $diem->tenhoatdong ?? $diem->mota;

                $details[] = [
                    'loai' => $loaiMap[$diem->loaihoatdong] ?? $diem->loaihoatdong,
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
                'base' => 70, // Điểm cơ bản
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
     * Cập nhật ảnh đại diện
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
            
            $user->update(['anhdaidien' => $path]);

            return back()->with('success', 'Cập nhật ảnh đại diện thành công!');
        }

        return back()->with('error', 'Có lỗi xảy ra khi tải ảnh lên');
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::guard('api')->user();
        
        // Validation rules chung
        $rules = [
            'hoten' => 'required|string|max:150',
            'email' => 'required|email|unique:nguoidung,email,' . $user->manguoidung . ',manguoidung',
            'sodienthoai' => 'nullable|string|max:20|regex:/^[0-9]{10,11}$/',
        ];

        $messages = [
            'hoten.required' => 'Họ và tên không được để trống',
            'hoten.max' => 'Họ và tên không được quá 150 ký tự',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'sodienthoai.regex' => 'Số điện thoại phải có 10-11 chữ số',
            'sodienthoai.max' => 'Số điện thoại không được quá 20 ký tự',
        ];

        // Validation riêng cho sinh viên
        if ($user->vaitro === 'SinhVien') {
            $rules['malop'] = 'nullable|exists:lop,malop';
            $rules['namnhaphoc'] = 'nullable|integer|min:2000|max:' . (date('Y') + 1);
            
            $messages['malop.exists'] = 'Lớp không tồn tại';
            $messages['namnhaphoc.integer'] = 'Năm nhập học phải là số';
            $messages['namnhaphoc.min'] = 'Năm nhập học không hợp lệ';
            $messages['namnhaphoc.max'] = 'Năm nhập học không hợp lệ';
        }

        // Validation riêng cho giảng viên
        if ($user->vaitro === 'GiangVien') {
            $rules['chucvu'] = 'nullable|string|max:100';
            $rules['hocvi'] = 'nullable|string|max:100';
            $rules['chuyenmon'] = 'nullable|string|max:255';
            
            $messages['chucvu.max'] = 'Chức vụ không được quá 100 ký tự';
            $messages['hocvi.max'] = 'Học vị không được quá 100 ký tự';
            $messages['chuyenmon.max'] = 'Chuyên môn không được quá 255 ký tự';
        }

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Cập nhật thông tin người dùng
            $user->update([
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
            ]);

            // Cập nhật thông tin riêng theo vai trò
            if ($user->vaitro === 'SinhVien') {
                $sinhVien = $user->sinhVien;
                if ($sinhVien) {
                    $updateData = [];
                    
                    if ($request->filled('malop')) {
                        $updateData['malop'] = $request->malop;
                    }
                    
                    if ($request->filled('namnhaphoc')) {
                        $updateData['namnhaphoc'] = $request->namnhaphoc;
                    }
                    
                    if (!empty($updateData)) {
                        $sinhVien->update($updateData);
                    }
                }
            } elseif ($user->vaitro === 'GiangVien') {
                $giangVien = $user->giangVien;
                if ($giangVien) {
                    $updateData = [];
                    
                    if ($request->filled('chucvu')) {
                        $updateData['chucvu'] = $request->chucvu;
                    }
                    
                    if ($request->filled('hocvi')) {
                        $updateData['hocvi'] = $request->hocvi;
                    }
                    
                    if ($request->filled('chuyenmon')) {
                        $updateData['chuyenmon'] = $request->chuyenmon;
                    }
                    
                    if (!empty($updateData)) {
                        $giangVien->update($updateData);
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xuất báo cáo điểm rèn luyện PDF
     */
    public function exportDiemRenLuyenPDF()
    {
        $user = Auth::guard('api')->user();
        
        if ($user->vaitro !== 'SinhVien') {
            return back()->with('error', 'Chức năng chỉ dành cho sinh viên');
        }

        $sinhVien = $user->sinhVien;
        
        if (!$sinhVien) {
            return back()->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        // TODO: Implement PDF generation
        // Sử dụng package như barryvdh/laravel-dompdf hoặc mpdf
        
        // Lấy thông tin cho PDF
        $diemRenLuyen = $this->getDiemRenLuyenDetail($sinhVien);
        
        // TODO: Generate PDF here
        // $pdf = PDF::loadView('pdf.diem-ren-luyen', compact('user', 'sinhVien', 'diemRenLuyen'));
        // return $pdf->download('diem-ren-luyen-' . $sinhVien->masinhvien . '.pdf');
        
        return back()->with('info', 'Chức năng xuất PDF đang được phát triển');
    }

    /**
     * Hủy đăng ký hoạt động (cổ vũ hoặc hỗ trợ)
     */
    public function cancelActivityRegistration($madangkyhoatdong)
    {
        $user = Auth::guard('api')->user();
        
        if (!$user || $user->vaitro !== 'SinhVien') {
            return back()->with('error', 'Vui lòng đăng nhập với tài khoản sinh viên');
        }

        $sinhVien = $user->sinhVien;
        
        if (!$sinhVien) {
            return back()->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        try {
            // Lấy thông tin đăng ký
            $registration = DB::table('dangkyhoatdong as dkhd')
                ->join('hoatdonghotro as hd', 'dkhd.mahoatdong', '=', 'hd.mahoatdong')
                ->where('dkhd.madangkyhoatdong', $madangkyhoatdong)
                ->where('dkhd.masinhvien', $sinhVien->masinhvien)
                ->whereIn('hd.loaihoatdong', ['CoVu', 'ToChuc', 'HoTroKyThuat']) // Cho phép cả 3 loại
                ->select('dkhd.*', 'hd.thoigianbatdau', 'hd.tenhoatdong', 'hd.loaihoatdong')
                ->first();

            if (!$registration) {
                return back()->with('error', 'Không tìm thấy đăng ký');
            }

            // Kiểm tra đã điểm danh chưa
            if ($registration->diemdanhqr) {
                return back()->with('error', 'Không thể hủy đăng ký đã điểm danh!');
            }

            // Kiểm tra thời gian hủy (phải còn ít nhất 24h)
            $hoursUntilStart = now()->diffInHours(\Carbon\Carbon::parse($registration->thoigianbatdau), false);
            
            if ($hoursUntilStart < 24) {
                return back()->with('error', 'Không thể hủy đăng ký trong vòng 24 giờ trước sự kiện');
            }

            // Xóa đăng ký
            DB::table('dangkyhoatdong')
                ->where('madangkyhoatdong', $madangkyhoatdong)
                ->delete();

            return back()->with('success', 'Đã hủy đăng ký thành công');

        } catch (\Exception $e) {
            Log::error('Error canceling activity registration: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hủy đăng ký dự thi (CẢ CÁ NHÂN VÀ ĐỘI)
     */
    public function cancelCompetitionRegistration($id)
    {
        $user = Auth::guard('api')->user();
        
        if (!$user || $user->vaitro !== 'SinhVien') {
            return back()->with('error', 'Chỉ sinh viên mới được hủy đăng ký dự thi');
        }

        $sinhVien = $user->sinhVien;
        if (!$sinhVien) {
            return back()->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        try {
            DB::beginTransaction();

            // ===== XỬ LÝ HỦY ĐĂNG KÝ CÁ NHÂN =====
            $dangKyCaNhan = DB::table('dangkycanhan as dkcn')
                ->join('cuocthi as ct', 'dkcn.macuocthi', '=', 'ct.macuocthi')
                ->where('dkcn.madangkycanhan', $id)
                ->where('dkcn.masinhvien', $sinhVien->masinhvien)
                ->select('dkcn.*', 'ct.thoigianbatdau', 'ct.tencuocthi')
                ->first();

            if ($dangKyCaNhan) {
                $startTime = \Carbon\Carbon::parse($dangKyCaNhan->thoigianbatdau);

                // Kiểm tra thời gian - chỉ được hủy trước khi cuộc thi bắt đầu
                if ($startTime->lt(now())) {
                    DB::rollBack();
                    return back()->with('error', 'Không thể hủy khi cuộc thi đã bắt đầu');
                }

                // Xóa đăng ký cá nhân
                DB::table('dangkycanhan')
                    ->where('madangkycanhan', $id)
                    ->delete();

                DB::commit();
                return back()->with('success', 'Hủy đăng ký cá nhân thành công!');
            }

            // ===== XỬ LÝ HỦY ĐĂNG KÝ ĐỘI =====
            $dangKyDoi = DB::table('dangkydoithi as dkdt')
                ->join('doithi as dt', 'dkdt.madoithi', '=', 'dt.madoithi')
                ->join('cuocthi as ct', 'dkdt.macuocthi', '=', 'ct.macuocthi')
                ->where('dkdt.madangkydoi', $id)
                ->select(
                    'dkdt.*', 
                    'dt.madoithi', 
                    'dt.tendoithi',
                    'dt.matruongdoi', 
                    'ct.thoigianbatdau', 
                    'ct.tencuocthi'
                )
                ->first();

            if ($dangKyDoi) {
                // Kiểm tra quyền: CHỈ TRƯỞNG ĐỘI MỚI ĐƯỢC HỦY
                if ($dangKyDoi->matruongdoi !== $sinhVien->masinhvien) {
                    DB::rollBack();
                    return back()->with('error', 'Chỉ trưởng đội mới có quyền hủy đăng ký đội thi!');
                }

                $startTime = \Carbon\Carbon::parse($dangKyDoi->thoigianbatdau);

                // Kiểm tra thời gian - chỉ được hủy trước khi cuộc thi bắt đầu
                if ($startTime->lt(now())) {
                    DB::rollBack();
                    return back()->with('error', 'Không thể hủy khi cuộc thi đã bắt đầu');
                }

                // Bước 1: Xóa tất cả thành viên của đội
                DB::table('thanhviendoithi')
                    ->where('madoithi', $dangKyDoi->madoithi)
                    ->delete();

                // Bước 2: Xóa đăng ký đội thi
                DB::table('dangkydoithi')
                    ->where('madangkydoi', $id)
                    ->delete();

                // Bước 3: Xóa đội thi
                DB::table('doithi')
                    ->where('madoithi', $dangKyDoi->madoithi)
                    ->delete();

                DB::commit();
                return back()->with('success', "Hủy đăng ký đội \"{$dangKyDoi->tendoithi}\" thành công!");
            }

            // Không tìm thấy đăng ký nào
            DB::rollBack();
            return back()->with('error', 'Không tìm thấy đăng ký dự thi hoặc bạn không có quyền hủy');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi hủy đăng ký dự thi: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
    