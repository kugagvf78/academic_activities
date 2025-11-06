<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TaiKhoanNguoiDung;

class AuthController extends Controller
{
    public function showLogin()
{
    return view('auth.login');
}

public function showChangePassword()
{
    return view('auth.change-password');
}


    public function login(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required',
            'MatKhau' => 'required',
        ]);

        $user = TaiKhoanNguoiDung::where('TenDangNhap', $request->TenDangNhap)
            ->where('TrangThaiHoatDong', true)
            ->first();

        if ($user && Hash::check($request->MatKhau, $user->MatKhau)) {
            Auth::guard()->login($user);
            $user->update(['LanDangNhapCuoi' => now()]);
            return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công');
        }

        return back()->withErrors(['login' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Đăng xuất thành công');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'MatKhauCu' => 'required',
            'MatKhauMoi' => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->MatKhauCu, $user->MatKhau)) {
            return back()->withErrors(['MatKhauCu' => 'Mật khẩu cũ không đúng']);
        }

        $user->MatKhau = Hash::make($request->MatKhauMoi);
        $user->NgayCapNhat = now();
        $user->NguoiCapNhat = $user->TenDangNhap;
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công');
    }
}
