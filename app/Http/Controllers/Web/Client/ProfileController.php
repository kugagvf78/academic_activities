<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

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
            $profile = $user->sinhVien()->with('lop')->first();
            
            // Lấy hoạt động học thuật của sinh viên
            $activities = $this->getSinhVienActivities($profile);
            
            // Lấy điểm rèn luyện
            $diemRenLuyen = $this->getDiemRenLuyen($profile);
            
            // Lấy chứng nhận (placeholder - cần tạo bảng chứng nhận)
            $certificates = [];
            
        } else if ($user->vaitro === 'GiangVien') {
            $profile = $user->giangVien()->with('boMon')->first();
            $activities = [];
            $diemRenLuyen = null;
            $certificates = [];
        } else {
            $profile = null;
            $activities = [];
            $diemRenLuyen = null;
            $certificates = [];
        }

        return view('client.profile', compact('user', 'profile', 'activities', 'diemRenLuyen', 'certificates'));
    }

    /**
     * Lấy hoạt động học thuật của sinh viên
     */
    private function getSinhVienActivities($sinhVien)
    {
        if (!$sinhVien) return collect([]);

        $activities = collect([]);

        // Lấy các cuộc thi đã đăng ký
        $dangKyDuThi = $sinhVien->dangKyDuThis()
            ->with('cuocThi')
            ->orderBy('ngaydangky', 'desc')
            ->get();

        foreach ($dangKyDuThi as $dangKy) {
            $activities->push([
                'type' => 'dự thi',
                'title' => $dangKy->cuocThi->tencuocthi ?? 'Cuộc thi',
                'date' => $dangKy->ngaydangky,
                'role' => 'Thí sinh',
                'status' => $dangKy->trangthai,
                'points' => 10, // Điểm mặc định
            ]);
        }

        // Lấy các hoạt động đã đăng ký
        $dangKyHoatDong = $sinhVien->dangKyHoatDongs()
            ->with('cuocThi')
            ->orderBy('ngaydangky', 'desc')
            ->get();

        foreach ($dangKyHoatDong as $dangKy) {
            $activities->push([
                'type' => $dangKy->vaitro,
                'title' => $dangKy->cuocThi->tencuocthi ?? 'Cuộc thi',
                'date' => $dangKy->ngaydangky,
                'role' => $this->translateRole($dangKy->vaitro),
                'status' => $dangKy->trangthai,
                'points' => $this->getPointsByRole($dangKy->vaitro),
            ]);
        }

        return $activities->sortByDesc('date')->take(12);
    }

    /**
     * Lấy điểm rèn luyện
     */
    private function getDiemRenLuyen($sinhVien)
    {
        if (!$sinhVien) return null;

        // Lấy tổng điểm từ bảng diemrenluyen
        $diemRenLuyenRecords = $sinhVien->diemRenLuyens()
            ->orderBy('hocky', 'desc')
            ->orderBy('namhoc', 'desc')
            ->get();

        $tongDiem = $diemRenLuyenRecords->sum('tongdiem');

        // Lấy chi tiết điểm gần nhất
        $chiTiet = [];
        
        // Từ đăng ký dự thi
        $dangKyDuThi = $sinhVien->dangKyDuThis()
            ->with('cuocThi')
            ->where('trangthai', 'Approved')
            ->orderBy('ngaydangky', 'desc')
            ->take(5)
            ->get();

        foreach ($dangKyDuThi as $dangKy) {
            $chiTiet[] = [
                'ten' => 'Tham gia ' . ($dangKy->cuocThi->tencuocthi ?? 'Cuộc thi'),
                'ngay' => $dangKy->ngaydangky,
                'diem' => 10,
            ];
        }

        // Từ đăng ký hoạt động
        $dangKyHoatDong = $sinhVien->dangKyHoatDongs()
            ->with('cuocThi')
            ->where('trangthai', 'Approved')
            ->orderBy('ngaydangky', 'desc')
            ->take(5)
            ->get();

        foreach ($dangKyHoatDong as $dangKy) {
            $chiTiet[] = [
                'ten' => $this->translateRole($dangKy->vaitro) . ' ' . ($dangKy->cuocThi->tencuocthi ?? 'Cuộc thi'),
                'ngay' => $dangKy->ngaydangky,
                'diem' => $this->getPointsByRole($dangKy->vaitro),
            ];
        }

        return [
            'tong' => $tongDiem ?: 0,
            'chi_tiet' => collect($chiTiet)->sortByDesc('ngay')->take(10),
        ];
    }

    /**
     * Dịch vai trò sang tiếng Việt
     */
    private function translateRole($role)
    {
        $roles = [
            'CoVu' => 'Cổ vũ',
            'HoTro' => 'Hỗ trợ',
            'ThiSinh' => 'Thí sinh',
        ];

        return $roles[$role] ?? $role;
    }

    /**
     * Lấy điểm theo vai trò
     */
    private function getPointsByRole($role)
    {
        $points = [
            'CoVu' => 5,
            'HoTro' => 10,
            'ThiSinh' => 10,
        ];

        return $points[$role] ?? 0;
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
        $request->validate([
            'HoTen' => 'nullable|string|max:150',
            'Email' => 'nullable|email|unique:nguoidung,email,' . Auth::guard('api')->id() . ',manguoidung',
            'SoDienThoai' => 'nullable|string|max:20',
        ], [
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã được sử dụng',
            'SoDienThoai.max' => 'Số điện thoại không được quá 20 ký tự',
        ]);

        $user = Auth::guard('api')->user();
        
        $user->update([
            'hoten' => $request->HoTen,
            'email' => $request->Email,
            'sodienthoai' => $request->SoDienThoai,
        ]);

        return back()->with('success', 'Cập nhật thông tin thành công!');
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
        $diemRenLuyen = $this->getDiemRenLuyen($sinhVien);

        // TODO: Implement PDF generation
        // Sử dụng package như barryvdh/laravel-dompdf
        
        return back()->with('info', 'Chức năng xuất PDF đang được phát triển');
    }
}