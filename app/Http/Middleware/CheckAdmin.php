<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = jwt_user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập!');
        }

        // Kiểm tra xem có phải giảng viên không
        if ($user->vaitro !== 'GiangVien') {
            return redirect()->route('client.home')
                ->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        // Kiểm tra xem có phải admin không
        $giangVien = $user->giangVien;
        
        if (!$giangVien || !$giangVien->is_admin) {
            return redirect()->route('giangvien.profile.index')
                ->with('error', 'Bạn không có quyền truy cập trang quản trị!');
        }

        return $next($request);
    }
}