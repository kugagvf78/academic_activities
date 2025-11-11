<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\NguoiDung;

class AuthController extends Controller
{
    // Đăng ký người dùng mới
    public function register(Request $request)
    {
        $request->validate([
            'ten_dang_nhap' => 'required|string|unique:nguoi_dung',
            'mat_khau' => 'required|string|min:6',
            'email' => 'nullable|email|unique:nguoi_dung',
            'ho_ten' => 'nullable|string|max:150',
            'so_dien_thoai' => 'nullable|string|max:20',
        ]);

        $user = NguoiDung::create([
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'mat_khau' => Hash::make($request->mat_khau),
            'email' => $request->email,
            'ho_ten' => $request->ho_ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'trang_thai' => true,
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user
        ], 201);
    }
    // Hiển thị form đăng ký
    public function showRegister(Request $request)
    {
        // Nếu đã đăng nhập → chuyển về trangTrang chủ
        if ($request->cookie('jwt_token')) {
            try {
                Auth::guard('api')->setToken($request->cookie('jwt_token'));
                if (Auth::guard('api')->check()) {
                    return redirect()->route('client.home');
                }
            } catch (\Exception $e) {
                // Token lỗi → bỏ qua
            }
        }

        return view('auth.register');
    }

    // Xử lý đăng ký từ form web
    public function webRegister(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string|unique:nguoi_dung,ten_dang_nhap',
            'MatKhau' => 'required|string|min:6|confirmed',
            'HoTen' => 'nullable|string|max:150',
            'Email' => 'required|email|unique:nguoi_dung,email',
            'SoDienThoai' => 'nullable|string|max:20',
        ], [
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập',
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp',
            'Email.required' => 'Vui lòng nhập email',
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã được sử dụng',
        ]);

        $user = NguoiDung::create([
            'ten_dang_nhap' => $request->TenDangNhap,
            'mat_khau' => Hash::make($request->MatKhau),
            'ho_ten' => $request->HoTen,
            'email' => $request->Email,
            'so_dien_thoai' => $request->SoDienThoai,
            'trang_thai' => true,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        // Đăng nhập luôn sau khi đăng ký
        $token = Auth::guard('api')->login($user);
        $cookie = cookie('jwt_token', $token, 60 * 24, '/', null, false, true);

        return redirect()->route('client.home')
            ->with('success', 'Đăng ký thành công! Chào mừng bạn.')
            ->cookie($cookie);
    }

    // Hiển thị form đăng nhập
    public function showLogin(Request $request) // SỬA: Thêm Request $request
    {
        // Kiểm tra JWT token trong cookie
        if ($request->cookie('jwt_token')) {
            try {
                Auth::guard('api')->setToken($request->cookie('jwt_token'));
                if (Auth::guard('api')->check()) {
                    return redirect()->route('client.home');
                }
            } catch (\Exception $e) {
                // Token không hợp lệ
            }
        }
        return view('auth.login');
    }

    // Đăng nhập API (JWT)
    public function login(Request $request)
    {
        $credentials = $request->only('ten_dang_nhap', 'mat_khau');

        if (!$token = Auth::guard('api')->attempt([
            'ten_dang_nhap' => $credentials['ten_dang_nhap'],
            'password' => $credentials['mat_khau']
        ])) {
            return response()->json(['error' => 'Sai tên đăng nhập hoặc mật khẩu'], 401);
        }

        return $this->respondWithToken($token);
    }

    // Đăng nhập WEB (JWT thuần túy + tạo session cho Blade)
    public function webLogin(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ], [
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu',
        ]);

        $user = NguoiDung::where('ten_dang_nhap', $request->TenDangNhap)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->mat_khau)) {
            return back()->withErrors([
                'TenDangNhap' => 'Tên đăng nhập hoặc mật khẩu không đúng.'
            ])->withInput($request->only('TenDangNhap'));
        }

        if (!$user->trang_thai) {
            return back()->withErrors([
                'TenDangNhap' => 'Tài khoản của bạn đã bị khóa.'
            ])->withInput($request->only('TenDangNhap'));
        }

        // ✅ Tạo JWT token cho API
        $token = Auth::guard('api')->login($user);

        // ✅ Đồng thời đăng nhập vào guard 'web' để Blade hiển thị user
        Auth::guard('web')->login($user);

        // ✅ Tạo cookie chứa token (tùy chọn – cho API sử dụng)
        $cookie = cookie('jwt_token', $token, 60 * 24, '/', null, false, true);

        // ✅ Chuyển hướng về trang chủ với thông báo
        return redirect()->route('client.home')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Đăng nhập thành công! Chào mừng ' . ($user->ho_ten ?? $user->ten_dang_nhap)
            ])
            ->cookie($cookie);
    }


    // Lấy thông tin người dùng hiện tại
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        try {
            Auth::guard('api')->logout();
        } catch (\Exception $e) {
            // Ignore
        }

        $cookie = cookie()->forget('jwt_token');

        return redirect()->route('login')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Đăng xuất thành công!'
            ])
            ->cookie($cookie);
    }

    // Làm mới token
    public function refresh()
    {
        $token = Auth::guard('api')->refresh(); // Gán vào biến
        return $this->respondWithToken($token);
    }

    // Hiển thị form đổi mật khẩu
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    // Đổi mật khẩu
    public function changePassword(Request $request) // SỬA: Thêm Request $request
    {
        $request->validate([
            'MatKhauCu' => 'required|string',
            'MatKhauMoi' => 'required|string|min:6|confirmed',
        ], [
            'MatKhauCu.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'MatKhauMoi.required' => 'Vui lòng nhập mật khẩu mới',
            'MatKhauMoi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'MatKhauMoi.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        $user = Auth::guard('api')->user();

        if (!$user) {
            return back()->withErrors(['MatKhauCu' => 'Không tìm thấy người dùng']);
        }

        if (!Hash::check($request->MatKhauCu, $user->mat_khau)) {
            return back()->withErrors([
                'MatKhauCu' => 'Mật khẩu hiện tại không đúng'
            ]);
        }

        $user->update([
            'mat_khau' => Hash::make($request->MatKhauMoi)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    // Trả về token
    protected function respondWithToken($token) // SỬA: Thêm tham số $token
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }

    // Hiển thị form quên mật khẩu
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Gửi link reset
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Hiển thị form đặt lại mật khẩu
    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Xử lý đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'mat_khau' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công!')
            : back()->withErrors(['email' => [__($status)]]);
    }
}