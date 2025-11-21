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
    function is_admin()
    {
        $user = jwt_user();
        return $user && $user->vaitro === 'Admin';
    }
}

if (!function_exists('profile_url')) {
    function profile_url()
    {
        $user = jwt_user();
        if (!$user) return route('login');
        
        return match($user->vaitro) {
            'Admin' => route('client.home'),
            'GiangVien' => route('giangvien.profile.index'),
            'SinhVien' => route('profile.index'),
            default => route('client.home'),
        };
    }
}