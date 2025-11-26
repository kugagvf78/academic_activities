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
    // Đăng ký người dùng mới (API)
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

            // Tạo mã sinh viên tự động
            $count = SinhVien::count() + 1;
            $maSinhVien = '20' . date('y') . str_pad($count, 6, '0', STR_PAD_LEFT);
            
            // Tạo mã người dùng
            $maNguoiDung = 'ND' . str_pad(NguoiDung::count() + 1, 6, '0', STR_PAD_LEFT);

            // ✅ Tên đăng nhập = Mã sinh viên
            $user = NguoiDung::create([
                'manguoidung' => $maNguoiDung,
                'tendangnhap' => $maSinhVien, // Tên đăng nhập = Mã sinh viên
                'matkhau' => Hash::make($request->MatKhau),
                'email' => $request->Email,
                'hoten' => $request->HoTen,
                'sodienthoai' => $request->SoDienThoai,
                'vaitro' => 'SinhVien',
                'trangthai' => 'Active',
            ]);

            // ✅ Tạo bản ghi SinhVien
            SinhVien::create([
                'masinhvien' => $maSinhVien,
                'manguoidung' => $maNguoiDung,
                'trangthai' => 'Active',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Đăng ký thành công',
                'user' => $user,
                'ma_sinh_vien' => $maSinhVien
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị form đăng ký
    public function showRegister(Request $request)
    {
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
            'MatKhau' => 'required|string|min:6|confirmed',
            'HoTen' => 'required|string|max:150',
            'Email' => 'required|email|unique:nguoidung,email',
            'SoDienThoai' => 'nullable|string|max:20',
            'VaiTro' => 'required|in:SinhVien,GiangVien',
        ], [
            'MatKhau.required' => 'Vui lòng nhập mật khẩu',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp',
            'HoTen.required' => 'Vui lòng nhập họ tên',
            'Email.required' => 'Vui lòng nhập email',
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã được sử dụng',
            'VaiTro.required' => 'Vui lòng chọn vai trò',
            'VaiTro.in' => 'Vai trò không hợp lệ',
        ]);

        try {
            DB::beginTransaction();

            // Tạo mã người dùng
            $maNguoiDung = 'ND' . str_pad(NguoiDung::count() + 1, 6, '0', STR_PAD_LEFT);
            
            // Tạo mã và tên đăng nhập dựa trên vai trò
            if ($request->VaiTro === 'SinhVien') {
                $count = SinhVien::count() + 1;
                $maVaiTro = '20' . date('y') . str_pad($count, 6, '0', STR_PAD_LEFT);
            } else {
                $count = GiangVien::count() + 1;
                $maVaiTro = 'GV' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            // ✅ Tên đăng nhập = Mã sinh viên/giảng viên
            $user = NguoiDung::create([
                'manguoidung' => $maNguoiDung,
                'tendangnhap' => $maVaiTro, // Tên đăng nhập = Mã SV/GV
                'matkhau' => Hash::make($request->MatKhau),
                'hoten' => $request->HoTen,
                'email' => $request->Email,
                'sodienthoai' => $request->SoDienThoai,
                'vaitro' => $request->VaiTro,
                'trangthai' => 'Active',
            ]);

            // ✅ Tạo bản ghi tương ứng trong SinhVien hoặc GiangVien
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

            // Đăng nhập luôn sau khi đăng ký
            $token = Auth::guard('api')->login($user);
            $cookie = cookie('jwt_token', $token, 60 * 24, '/', null, false, true);

            $vaiTroText = $request->VaiTro === 'SinhVien' ? 'Sinh viên' : 'Giảng viên';
            
            return redirect()->route('client.home')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Đăng ký thành công!',
                    'message' => "Mã {$vaiTroText} của bạn là: {$maVaiTro}. Đây cũng là tên đăng nhập của bạn."
                ])
                ->cookie($cookie);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage()
            ])->withInput();
        }
    }

    // Hiển thị form đăng nhập
    public function showLogin(Request $request)
    {
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
        $request->validate([
            'TenDangNhap' => 'required|string', // Mã sinh viên hoặc mã giảng viên
            'MatKhau' => 'required|string',
        ]);

        // ✅ Tìm user bằng tên đăng nhập (là mã sinh viên/giảng viên)
        $user = NguoiDung::where('tendangnhap', $request->TenDangNhap)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->matkhau)) {
            return response()->json(['error' => 'Sai mã sinh viên/giảng viên hoặc mật khẩu'], 401);
        }

        if ($user->trangthai !== 'Active') {
            return response()->json(['error' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        $token = Auth::guard('api')->login($user);

        return $this->respondWithToken($token);
    }

    // Đăng nhập WEB
    public function webLogin(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ], [
            'TenDangNhap.required' => 'Vui lòng nhập mã sinh viên/giảng viên',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu',
        ]);

        $user = NguoiDung::where('tendangnhap', $request->TenDangNhap)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->matkhau)) {
            return back()->withErrors([
                'TenDangNhap' => 'Mã sinh viên/giảng viên hoặc mật khẩu không đúng.'
            ])->withInput($request->only('TenDangNhap'));
        }

        if ($user->trangthai !== 'Active') {
            return back()->withErrors([
                'TenDangNhap' => 'Tài khoản của bạn đã bị khóa.'
            ])->withInput($request->only('TenDangNhap'));
        }

        $token = Auth::guard('api')->login($user);
        Auth::guard('web')->login($user);

        $cookie = cookie('jwt_token', $token, 60 * 24 * 7, '/', null, false, true);

        // Redirect theo vai trò, ưu tiên admin
        if ($user->isAdmin()) {
            $redirectRoute = 'admin.dashboard';
        } else {
            $redirectRoute = match($user->vaitro) {
                'GiangVien' => 'giangvien.profile.index',
                'SinhVien' => 'profile.index',
                default => 'client.home',
            };
        }

        return redirect()->route($redirectRoute)
            ->with('success', 'Đăng nhập thành công!')
            ->cookie($cookie);
    }

    // Lấy thông tin người dùng hiện tại
    public function me()
    {
        $user = Auth::guard('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Không tìm thấy người dùng'], 401);
        }

        // Lấy thêm thông tin sinh viên hoặc giảng viên
        $additionalInfo = null;
        if ($user->vaitro === 'SinhVien') {
            $additionalInfo = SinhVien::where('manguoidung', $user->manguoidung)->first();
        } elseif ($user->vaitro === 'GiangVien') {
            $additionalInfo = GiangVien::where('manguoidung', $user->manguoidung)->first();
        }

        return response()->json([
            'user' => $user,
            'detail' => $additionalInfo
        ]);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        try {
            Auth::guard('api')->logout();
            Auth::guard('web')->logout();
        } catch (\Exception $e) {
            // Ignore
        }

        $cookie = cookie()->forget('jwt_token');

        return redirect()->route('login')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Đăng xuất thành công!',
                'message' => 'Hẹn gặp lại bạn! Chúc bạn một ngày tốt lành.'
            ])
            ->cookie($cookie);
    }

    // Làm mới token
    public function refresh()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    // Hiển thị form đổi mật khẩu
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    // Đổi mật khẩu
    public function changePassword(Request $request)
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

        if (!Hash::check($request->MatKhauCu, $user->matkhau)) {
            return back()->withErrors([
                'MatKhauCu' => 'Mật khẩu hiện tại không đúng'
            ]);
        }

        // ✅ Sử dụng update với tên cột thực
        $user->update([
            'matkhau' => Hash::make($request->MatKhauMoi)
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Đổi mật khẩu thành công!',
            'message' => 'Mật khẩu của bạn đã được cập nhật.'
        ]);
    }

    // Trả về token
    protected function respondWithToken($token)
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
        $request->validate(['Email' => 'required|email']);

        $user = NguoiDung::where('email', $request->Email)->first();

        if (!$user) {
            return back()->withErrors(['Email' => 'Email không tồn tại trong hệ thống']);
        }

        $status = Password::broker('users')->sendResetLink(
            ['email' => $request->Email]
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('toast', [
                'type' => 'success',
                'title' => 'Gửi link thành công!',
                'message' => 'Link đặt lại mật khẩu đã được gửi đến email của bạn.'
            ])
            : back()->withErrors(['Email' => 'Không thể gửi link đặt lại mật khẩu']);
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
            'Email' => 'required|email',
            'MatKhau' => 'required|min:6|confirmed',
        ], [
            'Email.required' => 'Vui lòng nhập email',
            'Email.email' => 'Email không hợp lệ',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu mới',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        $status = Password::broker('users')->reset(
            [
                'email' => $request->Email,
                'password' => $request->MatKhau,
                'password_confirmation' => $request->MatKhau_confirmation,
                'token' => $request->token
            ],
            function ($user, $password) {
                // ✅ Sử dụng forceFill với tên cột thực
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
                'title' => 'Đặt lại mật khẩu thành công!',
                'message' => 'Bạn có thể đăng nhập bằng mật khẩu mới.'
            ])
            : back()->withErrors(['Email' => 'Không thể đặt lại mật khẩu. Vui lòng thử lại.']);
    }
}