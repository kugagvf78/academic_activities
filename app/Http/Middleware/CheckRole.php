<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = jwt_user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập!');
        }

        // Lấy vai trò từ field 'vaitro'
        $userRole = $user->vaitro;

        if (!$userRole || !in_array($userRole, $roles)) {
            Log::warning('Access denied', [
                'user_role' => $userRole,
                'required_roles' => $roles,
                'user_id' => $user->manguoidung,
            ]);
            return $this->redirectToOwnProfile($userRole);
        }

        return $next($request);
    }

    private function redirectToOwnProfile($vaitro)
    {
        $message = 'Bạn không có quyền truy cập trang này!';

        return match($vaitro) {
            'Admin' => redirect()->route('client.home')->with('error', $message),
            'GiangVien' => redirect()->route('giangvien.profile.index')->with('error', $message),
            'SinhVien' => redirect()->route('profile.index')->with('error', $message),
            default => redirect()->route('client.home')->with('error', $message),
        };
    }
}