<?php

namespace App\Http\Controllers\Web\Client;

use App\Models\DangKyHoatDong;
use App\Models\HoatDongHoTro;
use App\Models\CuocThi;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    /**
     * Hiển thị form đăng ký hỗ trợ Ban tổ chức theo slug
     */
    public function showSupportForm($slug)
    {
        // Parse slug để lấy macuocthi
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        
        $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
        
        // Kiểm tra trạng thái cuộc thi
        if ($cuocthi->trangthai !== 'Approved' && $cuocthi->trangthai !== 'InProgress') {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi không trong thời gian đăng ký');
        }
        
        // Kiểm tra thời gian đăng ký
        $now = now();
        if ($now->lt($cuocthi->thoigianbatdau) || $now->gt($cuocthi->thoigianketthuc)) {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi không trong thời gian đăng ký');
        }
        
        // Lấy danh sách hoạt động hỗ trợ Ban tổ chức của cuộc thi này
        $hoatdongs = HoatDongHoTro::where('macuocthi', $cuocthi->macuocthi)
            ->where('loaihoatdong', 'HoTroKyThuat')
            ->where('thoigianketthuc', '>=', now())
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        return view('client.events.support', compact('cuocthi', 'hoatdongs', 'slug'));
    }

    /**
     * Xử lý đăng ký hỗ trợ Ban tổ chức
     */
    public function registerSupport(Request $request, $slug)
    {
        // Parse slug để lấy macuocthi
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        
        // Validate dữ liệu
        $validated = $request->validate([
            'mahoatdong' => 'required|exists:hoatdonghotro,mahoatdong',
            'student_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ], [
            'mahoatdong.required' => 'Vui lòng chọn hoạt động hỗ trợ',
            'student_code.required' => 'Vui lòng nhập mã sinh viên',
            'name.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'phone.required' => 'Vui lòng nhập số điện thoại',
        ]);

        DB::beginTransaction();
        
        try {
            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
            
            // Kiểm tra sinh viên có tồn tại không
            $sinhvien = SinhVien::where('masinhvien', $validated['student_code'])->first();
            
            if (!$sinhvien) {
                return back()->withErrors(['student_code' => 'Mã sinh viên không tồn tại trong hệ thống'])
                            ->withInput();
            }

            // Kiểm tra hoạt động thuộc cuộc thi này
            $hoatdong = HoatDongHoTro::where('mahoatdong', $validated['mahoatdong'])
                ->where('macuocthi', $cuocthi->macuocthi)
                ->where('loaihoatdong', 'HoTroKyThuat')  // Chỉ cho phép đăng ký hỗ trợ kỹ thuật
                ->firstOrFail();

            // Kiểm tra đã đăng ký chưa
            $existing = DangKyHoatDong::where('mahoatdong', $validated['mahoatdong'])
                ->where('masinhvien', $sinhvien->masinhvien)
                ->exists();

            if ($existing) {
                return back()->with('error', 'Bạn đã đăng ký hoạt động này rồi!')
                            ->withInput();
            }

            // Kiểm tra thời gian đăng ký (không cho đăng ký sau khi hoạt động bắt đầu)
            if ($hoatdong->thoigianbatdau <= now()) {
                return back()->with('error', 'Hoạt động này đã bắt đầu, không thể đăng ký!')
                            ->withInput();
            }

            // Tạo mã đăng ký
            $madangky = 'DKHD' . Str::upper(Str::random(8));

            // Lưu đăng ký hoạt động
            DangKyHoatDong::create([
                'madangkyhoatdong' => $madangky,
                'mahoatdong' => $validated['mahoatdong'],
                'masinhvien' => $sinhvien->masinhvien,
                'ngaydangky' => now(),
                'trangthai' => 'Registered',
                'diemdanhqr' => false,
            ]);

            DB::commit();

            return back()->with('success', 'Đăng ký hỗ trợ Ban tổ chức thành công! Cảm ơn bạn đã đồng hành cùng chúng tôi. 💪');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Lỗi đăng ký hỗ trợ Ban tổ chức: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Kiểm tra mã sinh viên có tồn tại không (API)
     */
    public function checkStudentCode(Request $request)
    {
        $masinhvien = $request->input('student_code');
        $exists = SinhVien::where('masinhvien', $masinhvien)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    /**
     * Lấy mã cuộc thi từ slug
     */
    private function getMaCuocThiFromSlug($slug)
    {
        // Lấy phần cuối cùng sau dấu gạch ngang cuối cùng
        $parts = explode('-', $slug);
        return end($parts);
    }
}