<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\NguoiDung;
use App\Models\SinhVien;
use App\Models\GiangVien;
use App\Models\PasswordResetOtp;
use App\Mail\OtpMail;
use Symfony\Component\Mailer\Exception\TransportException;


class AuthController extends Controller
{
    // ====================================================================
    // ĐĂNG KÝ
    // ====================================================================
    
    /**
     * Đăng ký người dùng mới (API)
     */
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

    /**
     * Hiển thị form đăng ký
     */
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

    /**
     * Xử lý đăng ký từ form web
     */
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

    // ====================================================================
    // ĐĂNG NHẬP
    // ====================================================================

    /**
     * Hiển thị form đăng nhập
     */
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

    /**
     * Đăng nhập API (JWT)
     */
    public function login(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ]);

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

    /**
     * Đăng nhập WEB
     */
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

    // ====================================================================
    // QUÊN MẬT KHẨU - OTP
    // ====================================================================

    /**
     * Hiển thị form quên mật khẩu (Bước 1: Nhập email)
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi mã OTP qua email (Bước 1)
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'Email' => 'required|email'
        ], [
            'Email.required' => 'Vui lòng nhập email',
            'Email.email' => 'Email không hợp lệ'
        ]);

        $user = NguoiDung::where('email', $request->Email)->first();

        if (!$user) {
            return back()->withErrors([
                'Email' => 'Email không tồn tại trong hệ thống'
            ])->withInput();
        }

        // Xóa các OTP cũ
        PasswordResetOtp::where('email', $request->Email)->delete();

        // Tạo mã OTP 6 số
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu OTP vào database
        PasswordResetOtp::create([
            'email' => $request->Email,
            'otp' => $otp,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(5),
            'is_used' => false
        ]);

        // Gửi email
        try {
            Mail::to($request->Email)->send(new OtpMail($otp, $user->hoten));

            // Lưu email vào session
            session(['email' => $request->Email]);

            return redirect()->route('password.verify-otp')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Gửi mã OTP thành công!',
                    'message' => 'Vui lòng kiểm tra email của bạn. Mã OTP có hiệu lực trong 5 phút.'
                ]);

        } catch (\Exception $e) {
            Log::error('Email Sending Error: ' . $e->getMessage());
            
            return back()->withErrors([
                'Email' => 'Không thể gửi email. Vui lòng thử lại sau.'
            ])->withInput();
        }
    }

    /**
     * Hiển thị form nhập OTP (Bước 2)
     */
    public function showVerifyOtp(Request $request)
    {
        // DEBUG
        Log::info('showVerifyOtp called', [
            'session_email' => session('email'),
            'all_session' => session()->all()
        ]);

        if (!session('email')) {
            Log::warning('No email in session, redirecting to forgot password');
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Xác thực mã OTP (Bước 2)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP',
            'otp.digits' => 'Mã OTP phải có 6 chữ số'
        ]);

        $email = session('email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $otpRecord = PasswordResetOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp' => 'Mã OTP không hợp lệ hoặc đã hết hạn'
            ])->withInput();
        }

        session(['otp_verified' => true, 'otp_id' => $otpRecord->id]);

        return redirect()->route('password.reset')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Xác thực thành công!',
                'message' => 'Vui lòng nhập mật khẩu mới.'
            ]);
    }

    /**
     * Hiển thị form đặt lại mật khẩu (Bước 3)
     */
    public function showResetPassword()
    {
        if (!session('otp_verified') || !session('email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    /**
     * Xử lý đặt lại mật khẩu (Bước 3)
     */
    public function resetPasswordWithOtp(Request $request)
    {
        if (!session('otp_verified') || !session('email') || !session('otp_id')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Phiên làm việc đã hết hạn']);
        }

        $request->validate([
            'MatKhau' => 'required|string|min:6|confirmed',
        ], [
            'MatKhau.required' => 'Vui lòng nhập mật khẩu mới',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        $email = session('email');
        $otpId = session('otp_id');

        $user = NguoiDung::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['error' => 'Không tìm thấy người dùng']);
        }

        $user->update([
            'matkhau' => Hash::make($request->MatKhau)
        ]);

        PasswordResetOtp::where('id', $otpId)->update(['is_used' => true]);

        session()->forget(['email', 'otp_verified', 'otp_id']);

        PasswordResetOtp::clearExpired();

        return redirect()->route('login')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Đặt lại mật khẩu thành công!',
                'message' => 'Bạn có thể đăng nhập bằng mật khẩu mới.'
            ]);
    }

    /**
     * Gửi lại mã OTP
     */
    public function resendOtp(Request $request)
    {
        $email = session('email');
        
        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = NguoiDung::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Email không tồn tại']);
        }

        PasswordResetOtp::where('email', $email)->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email' => $email,
            'otp' => $otp,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(5),
            'is_used' => false
        ]);

        try {
            Mail::to($email)->send(new OtpMail($otp, $user->hoten));

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Gửi lại mã OTP thành công!',
                'message' => 'Vui lòng kiểm tra email của bạn.'
            ]);

        } catch (\Exception $e) {
            Log::error('Resend OTP Error: ' . $e->getMessage());
            
            return back()->withErrors([
                'error' => 'Không thể gửi email. Vui lòng thử lại sau.'
            ]);
        }
    }

    /**
     * Parse lỗi email
     */
    // private function parseEmailError($errorMessage)
    // {
    //     if (str_contains($errorMessage, 'does not match a verified Sender Identity') || 
    //         str_contains($errorMessage, 'Sender Identity')) {
    //         return 'Email gửi chưa được xác thực trong hệ thống SendGrid. Vui lòng liên hệ quản trị viên.';
    //     }
        
    //     if (str_contains($errorMessage, 'Authentication failed') || 
    //         str_contains($errorMessage, 'Invalid credentials') ||
    //         str_contains($errorMessage, 'Username and Password not accepted')) {
    //         return 'API Key SendGrid không hợp lệ hoặc đã hết hạn. Vui lòng liên hệ quản trị viên.';
    //     }
        
    //     if (str_contains($errorMessage, 'Connection timeout') || 
    //         str_contains($errorMessage, 'Connection refused') ||
    //         str_contains($errorMessage, 'Could not connect to host')) {
    //         return 'Không thể kết nối đến máy chủ SendGrid. Vui lòng kiểm tra kết nối mạng.';
    //     }
        
    //     if (str_contains($errorMessage, 'Daily sending limit')) {
    //         return 'Đã đạt giới hạn gửi email trong ngày. Vui lòng thử lại sau.';
    //     }

    //     if (str_contains($errorMessage, 'SSL') || str_contains($errorMessage, 'TLS')) {
    //         return 'Lỗi bảo mật kết nối. Vui lòng kiểm tra cấu hình MAIL_ENCRYPTION.';
    //     }

    //     // Log lỗi chi tiết để debug
    //     Log::error('Unknown email error: ' . $errorMessage);

    //     return 'Không thể gửi email: ' . $errorMessage;
    // }

    // ====================================================================
    // ĐỔI MẬT KHẨU (Khi đã đăng nhập)
    // ====================================================================

    /**
     * Hiển thị form đổi mật khẩu
     */
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Đổi mật khẩu
     */
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

        $user->update([
            'matkhau' => Hash::make($request->MatKhauMoi)
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Đổi mật khẩu thành công!',
            'message' => 'Mật khẩu của bạn đã được cập nhật.'
        ]);
    }

    // ====================================================================
    // ĐĂNG XUẤT & TIỆN ÍCH
    // ====================================================================

    /**
     * Lấy thông tin người dùng hiện tại
     */
    public function me()
    {
        $user = Auth::guard('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Không tìm thấy người dùng'], 401);
        }

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

    /**
     * Đăng xuất
     */
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

    /**
     * Làm mới token
     */
    public function refresh()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * Trả về token
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }
}