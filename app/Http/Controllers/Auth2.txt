<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\NguoiDung;
use App\Models\SinhVien;
use App\Models\GiangVien;

class AuthController extends Controller
{
    // ============================
    //  API: Đăng ký người dùng
    // ============================

    public function register(Request $request)
    {
        $request->validate([
            'Email' => 'required|email|unique:nguoidung,email',
            'MatKhau' => 'required|string|min:6',
            'HoTen' => 'nullable|string|max:150',
            'SoDienThoai' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $count = SinhVien::count() + 1;
            $maSinhVien = '20' . date('y') . str_pad($count, 6, '0', STR_PAD_LEFT);

            $maNguoiDung = 'ND' . str_pad(NguoiDung::count() + 1, 6, '0', STR_PAD_LEFT);

            $user = NguoiDung::create([
                'manguoidung' => $maNguoiDung,
                'tendangnhap' => $maSinhVien,
                'matkhau' => Hash::make($request->MatKhau),
                'email' => $request->Email,
                'hoten' => $request->HoTen,
                'sodienthoai' => $request->SoDienThoai,
                'vaitro' => 'SinhVien',
                'trangthai' => 'Active',
            ]);

            SinhVien::create([
                'masinhvien' => $maSinhVien,
                'manguoidung' => $maNguoiDung,
                'trangthai' => 'Active',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user' => $user,
                    'ma_sinh_vien' => $maSinhVien
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================
    //  WEB: Form đăng ký
    // ============================

    public function showRegister(Request $request)
    {
        if ($request->cookie('jwt_token')) {
            try {
                Auth::guard('api')->setToken($request->cookie('jwt_token'));
                if (Auth::guard('api')->check()) {
                    return redirect()->route('client.home');
                }
            } catch (\Exception $e) {}
        }

        return view('auth.register');
    }

    // ============================
    //  WEB: Xử lý đăng ký
    // ============================

    public function webRegister(Request $request)
    {
        $request->validate([
            'MatKhau' => 'required|string|min:6|confirmed',
            'HoTen' => 'required|string|max:150',
            'Email' => 'required|email|unique:nguoidung,email',
            'SoDienThoai' => 'nullable|string|max:20',
            'VaiTro' => 'required|in:SinhVien,GiangVien',
        ]);

        try {
            DB::beginTransaction();

            $maNguoiDung = 'ND' . str_pad(NguoiDung::count() + 1, 6, '0', STR_PAD_LEFT);

            if ($request->VaiTro === 'SinhVien') {
                $count = SinhVien::count() + 1;
                $maVaiTro = '20' . date('y') . str_pad($count, 6, '0', STR_PAD_LEFT);
            } else {
                $count = GiangVien::count() + 1;
                $maVaiTro = 'GV' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            $user = NguoiDung::create([
                'manguoidung' => $maNguoiDung,
                'tendangnhap' => $maVaiTro,
                'matkhau' => Hash::make($request->MatKhau),
                'hoten' => $request->HoTen,
                'email' => $request->Email,
                'sodienthoai' => $request->SoDienThoai,
                'vaitro' => $request->VaiTro,
                'trangthai' => 'Active',
            ]);

            if ($request->VaiTro === 'SinhVien') {
                SinhVien::create([
                    'masinhvien' => $maVaiTro,
                    'manguoidung' => $maNguoiDung,
                    'trangthai' => 'Active',
                ]);
            } else {
                GiangVien::create([
                    'magiangvien' => $maVaiTro,
                    'manguoidung' => $maNguoiDung,
                ]);
            }

            DB::commit();

            $token = Auth::guard('api')->login($user);
            $cookie = cookie('jwt_token', $token, 60 * 24, '/', null, false, true);

            $vaiTroText = $request->VaiTro === 'SinhVien' ? 'Sinh viên' : 'Giảng viên';

            return redirect()->route('client.home')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Đăng ký thành công!',
                    'message' => "Mã {$vaiTroText} của bạn là: {$maVaiTro}."
                ])
                ->cookie($cookie);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ])->withInput();
        }
    }

    // ============================
    //  WEB: Form đăng nhập
    // ============================

    public function showLogin(Request $request)
    {
        if ($request->cookie('jwt_token')) {
            try {
                Auth::guard('api')->setToken($request->cookie('jwt_token'));
                if (Auth::guard('api')->check()) {
                    return redirect()->route('client.home');
                }
            } catch (\Exception $e) {}
        }
        return view('auth.login');
    }

    // ============================
    //  API: Đăng nhập
    // ============================

    public function login(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ]);

        $user = NguoiDung::where('tendangnhap', $request->TenDangNhap)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->matkhau)) {
            return response()->json([
                'status' => false,
                'message' => 'Sai mã sinh viên/giảng viên hoặc mật khẩu'
            ], 401);
        }

        if ($user->trangthai !== 'Active') {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa'
            ], 403);
        }

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
                'user' => $user
            ]
        ]);
    }

    // ============================
    //  WEB: Đăng nhập
    // ============================

    public function webLogin(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ]);

        $user = NguoiDung::where('tendangnhap', $request->TenDangNhap)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->matkhau)) {
            return back()->withErrors([
                'TenDangNhap' => 'Mã sinh viên/giảng viên hoặc mật khẩu không đúng.'
            ])->withInput();
        }

        if ($user->trangthai !== 'Active') {
            return back()->withErrors([
                'TenDangNhap' => 'Tài khoản của bạn đã bị khóa.'
            ])->withInput();
        }

        $token = Auth::guard('api')->login($user);
        Auth::guard('web')->login($user);

        $cookie = cookie('jwt_token', $token, 60 * 24 * 7, '/', null, false, true);

        if ($user->isAdmin()) {
            $redirectRoute = 'admin.dashboard';
        } else {
            $redirectRoute = match ($user->vaitro) {
                'GiangVien' => 'giangvien.profile.index',
                'SinhVien' => 'profile.index',
                default => 'client.home',
            };
        }

        return redirect()->route($redirectRoute)
            ->with('success', 'Đăng nhập thành công!')
            ->cookie($cookie);
    }

    // ============================
    //  API: Lấy thông tin người dùng
    // ============================

    public function me()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 401);
        }

        $detail = null;

        if ($user->vaitro === 'SinhVien') {
            $detail = SinhVien::where('manguoidung', $user->manguoidung)->first();
        } elseif ($user->vaitro === 'GiangVien') {
            $detail = GiangVien::where('manguoidung', $user->manguoidung)->first();
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin thành công',
            'data' => [
                'user' => $user,
                'detail' => $detail
            ]
        ]);
    }

    // ============================
    //  Đăng xuất
    // ============================

    public function logout(Request $request)
    {
        try {
            Auth::guard('api')->logout();
            Auth::guard('web')->logout();
        } catch (\Exception $e) {}

        $cookie = cookie()->forget('jwt_token');

        return redirect()->route('login')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Đăng xuất thành công!',
                'message' => 'Hẹn gặp lại bạn!'
            ])
            ->cookie($cookie);
    }

    // ============================
    //  API: Refresh token
    // ============================

    public function refresh()
    {
        $token = Auth::guard('api')->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Token refreshed',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
                'user' => Auth::guard('api')->user()
            ]
        ]);
    }

    // ============================
    //  WEB: Form đổi mật khẩu
    // ============================

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    // ============================
    //  Đổi mật khẩu
    // ============================

    public function changePassword(Request $request)
    {
        $request->validate([
            'MatKhauCu' => 'required|string',
            'MatKhauMoi' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::guard('api')->user();

        if (!$user) {
            return back()->withErrors(['MatKhauCu' => 'Không tìm thấy người dùng']);
        }

        if (!Hash::check($request->MatKhauCu, $user->matkhau)) {
            return back()->withErrors(['MatKhauCu' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update([
            'matkhau' => Hash::make($request->MatKhauMoi)
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Đổi mật khẩu thành công!',
            'message' => 'Mật khẩu mới đã được cập nhật.'
        ]);
    }

    // ============================
    //  Quên mật khẩu
    // ============================

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['Email' => 'required|email']);

        $user = NguoiDung::where('email', $request->Email)->first();

        if (!$user) {
            return back()->withErrors(['Email' => 'Email không tồn tại trong hệ thống']);
        }

        $status = Password::broker('users')->sendResetLink(['email' => $request->Email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('toast', [
                'type' => 'success',
                'title' => 'Đã gửi link!',
                'message' => 'Hãy kiểm tra email của bạn.'
            ])
            : back()->withErrors(['Email' => 'Không thể gửi link đặt lại mật khẩu']);
    }

    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'Email' => 'required|email',
            'MatKhau' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('users')->reset(
            [
                'email' => $request->Email,
                'password' => $request->MatKhau,
                'password_confirmation' => $request->MatKhau_confirmation,
                'token' => $request->token
            ],
            function ($user, $password) {
                $user->forceFill([
                    'matkhau' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('toast', [
                'type' => 'success',
                'title' => 'Đặt mật khẩu thành công!',
                'message' => 'Bạn có thể đăng nhập lại.'
            ])
            : back()->withErrors(['Email' => 'Không thể đặt lại mật khẩu, vui lòng thử lại.']);
    }
}
