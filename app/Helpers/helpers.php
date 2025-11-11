<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('jwt_user')) {
    /**
     * Lấy user từ JWT token trong cookie
     */
    function jwt_user()
    {
        try {
            $token = request()->cookie('jwt_token');
            
            if (!$token) {
                return null;
            }

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
    /**
     * Kiểm tra đã đăng nhập chưa
     */
    function jwt_check()
    {
        return jwt_user() !== null;
    }
}

if (!function_exists('jwt_guest')) {
    /**
     * Kiểm tra chưa đăng nhập
     */
    function jwt_guest()
    {
        return jwt_user() === null;
    }
}