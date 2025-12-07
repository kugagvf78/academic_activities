<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('jwt_user')) {
    function jwt_user()
    {
        try {
            $token = request()->cookie('jwt_token');
            if (!$token) return null;
            Auth::guard('api')->setToken($token);
            if (Auth::guard('api')->check()) {
                return Auth::guard('api')->user();
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('jwt_check')) {
    function jwt_check()
    {
        return jwt_user() !== null;
    }
}

if (!function_exists('jwt_guest')) {
    function jwt_guest()
    {
        return jwt_user() === null;
    }
}

if (!function_exists('is_sinhvien')) {
    function is_sinhvien()
    {
        $user = jwt_user();
        return $user && $user->vaitro === 'SinhVien';
    }
}

if (!function_exists('is_giangvien')) {
    function is_giangvien()
    {
        $user = jwt_user();
        return $user && $user->vaitro === 'GiangVien';
    }
}

if (!function_exists('is_admin')) {
    /**
     * ✅ FIXED: Kiểm tra user có phải Admin không
     * Admin = Giảng viên có is_admin = true (Trưởng bộ môn)
     */
    function is_admin()
    {
        $user = jwt_user();
        
        // Kiểm tra user và vaitro
        if (!$user || $user->vaitro !== 'GiangVien') {
            return false;
        }
        
        // ✅ FIX: Phải load relationship giangVien trước
        if (!isset($user->giangVien)) {
            // Nếu chưa load, load ngay
            $user->load('giangVien');
        }
        
        // Kiểm tra giảng viên có is_admin = true không
        // Dùng == thay vì === vì có thể là 1 (int) hoặc true (bool)
        return $user->giangVien && ($user->giangVien->is_admin == true || $user->giangVien->is_admin == 1);
    }
}

if (!function_exists('profile_url')) {
    /**
     * ✅ FIXED: Lấy URL profile phù hợp với vai trò
     */
    function profile_url()
    {
        $user = jwt_user();
        if (!$user) return route('login');
        
        // ✅ FIX: Kiểm tra Admin (GiangVien với is_admin = true) TRƯỚC
        if ($user->vaitro === 'GiangVien') {
            // Load relationship nếu chưa load
            if (!isset($user->giangVien)) {
                $user->load('giangVien');
            }
            
            // Kiểm tra is_admin
            if ($user->giangVien && ($user->giangVien->is_admin == true || $user->giangVien->is_admin == 1)) {
                return route('admin.dashboard');  // Admin
            }
            
            return route('giangvien.profile.index');  // Giảng viên thường
        }
        
        // Sinh viên
        if ($user->vaitro === 'SinhVien') {
            return route('profile.index');
        }
        
        // Mặc định
        return route('client.home');
    }
}