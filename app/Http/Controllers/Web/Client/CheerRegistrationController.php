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

class CheerRegistrationController extends Controller
{
    /**
     * Hiển thị form đăng ký cổ vũ theo slug
    */
    public function showCheerForm($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();

        $now = now();
        $start = $cuocthi->thoigianbatdau;
        $end = $cuocthi->thoigianketthuc;
        
        // ============================================
        // CHỈ CHO ĐĂNG KÝ CỔ VŨ KHI CUỘC THI SẮP DIỄN RA (APPROVED)
        // ============================================
        
        // Kiểm tra 1: Trạng thái phải là Approved (sắp diễn ra)
        if ($cuocthi->trangthai !== 'Approved') {
            if ($cuocthi->trangthai === 'Draft') {
                return redirect()->route('client.events.show', $slug)
                    ->with('error', 'Cuộc thi chưa được phê duyệt.');
            }
            
            if ($cuocthi->trangthai === 'InProgress') {
                return redirect()->route('client.events.show', $slug)
                    ->with('error', 'Cuộc thi đã bắt đầu, không thể đăng ký cổ vũ nữa.');
            }
            
            if ($cuocthi->trangthai === 'Completed') {
                return redirect()->route('client.events.show', $slug)
                    ->with('error', 'Cuộc thi đã kết thúc.');
            }
            
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi chưa mở đăng ký cổ vũ.');
        }

        // Tính thời điểm mở đăng ký (7 ngày trước khi cuộc thi bắt đầu)
        $earlyRegistrationStart = $start->copy()->subDays(7);

        // Kiểm tra 2: Phải đến thời điểm cho phép đăng ký (7 ngày trước)
        if ($now->lt($earlyRegistrationStart)) {
            return redirect()->route('client.events.show', $slug)
                ->with('info', 'Đăng ký cổ vũ sẽ mở vào ngày ' . $earlyRegistrationStart->format('d/m/Y H:i') . ' (7 ngày trước khi cuộc thi bắt đầu).');
        }

        // Kiểm tra 3: Không cho đăng ký sau khi cuộc thi đã bắt đầu
        if ($now->gte($start)) {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Đã hết thời gian đăng ký cổ vũ. Cuộc thi đã bắt đầu.');
        }

        // ============================================
        // HẾT PHẦN SỬA
        // ============================================

        // Lấy các hoạt động cổ vũ còn thời gian đăng ký (chưa kết thúc)
        $hoatdongs = HoatDongHoTro::select('hoatdonghotro.*')
            ->selectRaw('(SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) as dangkyhoatdongs_count')
            ->where('macuocthi', $cuocthi->macuocthi)
            ->where('loaihoatdong', 'CoVu')
            ->where('thoigianketthuc', '>', $now) // Chưa kết thúc
            ->whereRaw('(SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) < hoatdonghotro.soluong') // Còn chỗ
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        return view('client.events.cheer', compact('cuocthi', 'hoatdongs', 'slug'));
    }

    /**
     * Xử lý đăng ký cổ vũ
     */
    public function registerCheer(Request $request, $slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        // Validate
        $validated = $request->validate([
            'mahoatdong' => 'required|exists:hoatdonghotro,mahoatdong',
            'student_code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^[\+]?[0-9]{10,15}$/',
        ], [
            'mahoatdong.required' => 'Vui lòng chọn hoạt động cổ vũ.',
            'student_code.required' => 'Vui lòng nhập mã sinh viên.',
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
        ]);

        DB::beginTransaction();

        try {
            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
            $now = now();

            // Kiểm tra sinh viên
            $sinhvien = SinhVien::where('masinhvien', $validated['student_code'])->first();
            if (!$sinhvien) {
                return back()->withErrors(['student_code' => 'Mã sinh viên không tồn tại.'])
                            ->withInput();
            }

            // Lấy hoạt động + số lượng đã đăng ký
            // FIX: Sử dụng selectRaw với subquery
            $hoatdong = HoatDongHoTro::select('hoatdonghotro.*')
                ->selectRaw('(SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) as dangkyhoatdongs_count')
                ->where('mahoatdong', $validated['mahoatdong'])
                ->where('macuocthi', $cuocthi->macuocthi)
                ->where('loaihoatdong', 'CoVu')
                ->firstOrFail();

            // Kiểm tra: hoạt động đã kết thúc chưa?
            if ($hoatdong->thoigianketthuc <= $now) {
                return back()->with('error', 'Hoạt động đã kết thúc, không thể đăng ký!')
                            ->withInput();
            }

            // Kiểm tra: còn chỗ?
            if ($hoatdong->dangkyhoatdongs_count >= $hoatdong->soluong) {
                return back()->with('error', 'Hoạt động này đã hết chỗ!')
                            ->withInput();
            }

            // Kiểm tra: đã đăng ký chưa?
            $daDangKy = DangKyHoatDong::where('mahoatdong', $hoatdong->mahoatdong)
                ->where('masinhvien', $sinhvien->masinhvien)
                ->exists();

            if ($daDangKy) {
                return back()->with('error', 'Bạn đã đăng ký hoạt động này rồi!')
                            ->withInput();
            }

            // Tạo mã đăng ký duy nhất
            $madangky = 'DKHD' . Str::upper(Str::random(8));
            while (DangKyHoatDong::where('madangkyhoatdong', $madangky)->exists()) {
                $madangky = 'DKHD' . Str::upper(Str::random(8));
            }

            // Lưu đăng ký
            DangKyHoatDong::create([
                'madangkyhoatdong' => $madangky,
                'mahoatdong' => $hoatdong->mahoatdong,
                'masinhvien' => $sinhvien->masinhvien,
                'ngaydangky' => $now,
                'trangthai' => 'Registered',
                'diemdanhqr' => false,
            ]);

            DB::commit();

            return back()->with('success', 'Đăng ký cổ vũ thành công! Mã đăng ký: <strong>' . $madangky . '</strong>');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi đăng ký cổ vũ: ' . $e->getMessage(), [
                'slug' => $slug,
                'data' => $request->all()
            ]);

            return back()->with('error', 'Đã có lỗi xảy ra. Vui lòng thử lại sau.')
                        ->withInput();
        }
    }

    /**
     * API: Kiểm tra mã sinh viên tồn tại
     */
    public function checkStudentCode(Request $request)
    {
        $request->validate([
            'student_code' => 'required|string|max:20'
        ]);

        $exists = SinhVien::where('masinhvien', $request->student_code)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Lấy mã cuộc thi từ slug (an toàn hơn)
     */
    private function getMaCuocThiFromSlug($slug)
    {
        if (empty($slug)) {
            abort(404);
        }

        $parts = explode('-', $slug);
        $macuocthi = end($parts);

        // Kiểm tra định dạng mã (VD: CT001, CT009,...)
        if (!preg_match('/^CT\d+$/', $macuocthi)) {
            abort(404, 'Slug không hợp lệ');
        }

        return $macuocthi;
    }
}