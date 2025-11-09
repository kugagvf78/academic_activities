<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\TaiKhoanNguoiDung;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLogin()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required',
            'MatKhau' => 'required',
        ]);

        // 🔍 Tìm người dùng đang hoạt động
        $user = TaiKhoanNguoiDung::where('TenDangNhap', $request->TenDangNhap)
            ->where('TrangThaiHoatDong', true)
            ->first();

        // ❌ Sai tài khoản hoặc mật khẩu
        if (!$user || !Hash::check($request->MatKhau, $user->MatKhau)) {
            return back()->with([
                'toast' => [
                    'type' => 'error',
                    'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
                ]
            ]);
        }

        // ✅ Đăng nhập session Laravel
        Auth::login($user);

        // 🕓 Cập nhật lần đăng nhập cuối
        $user->LanDangNhapCuoi = now();
        $user->save();

        // 🎟️ Sinh JWT token (tuỳ mày có xài API hay không)
        $token = JWTAuth::fromUser($user);
        session(['jwt_token' => $token]);

        return redirect()->route('client.home')->with([
            'toast' => [
                'type' => 'success',
                'message' => 'Đăng nhập thành công!',
            ]
        ]);
    }

    // Đăng xuất
    public function logout()
    {
        // Hủy JWT token nếu có
        if (session()->has('jwt_token')) {
            try {
                JWTAuth::invalidate(session('jwt_token'));
            } catch (\Exception $e) {}
        }

        // Hủy session Laravel
        Auth::logout();
        session()->flush();

        return redirect()->route('login')->with([
            'toast' => [
                'type' => 'info',
                'message' => 'Bạn đã đăng xuất.',
            ]
        ]);
    }

    // Hiển thị form đổi mật khẩu
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    // Xử lý đổi mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'MatKhauCu' => 'required',
            'MatKhauMoi' => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        // ❌ Sai mật khẩu cũ
        if (!Hash::check($request->MatKhauCu, $user->MatKhau)) {
            return back()->with([
                'toast' => [
                    'type' => 'error',
                    'message' => 'Mật khẩu cũ không đúng.',
                ]
            ]);
        }

        // ✅ Cập nhật mật khẩu mới
        $user->MatKhau = Hash::make($request->MatKhauMoi);
        $user->NgayCapNhat = now();
        $user->NguoiCapNhat = $user->TenDangNhap;
        $user->save();

        return back()->with([
            'toast' => [
                'type' => 'success',
                'message' => 'Đổi mật khẩu thành công!',
            ]
        ]);
    }
}
