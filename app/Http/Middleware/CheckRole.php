<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = jwt_user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập!');
        }

        if (!in_array($user->vaitro, $roles)) {
            return $this->redirectToOwnProfile($user->vaitro);
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