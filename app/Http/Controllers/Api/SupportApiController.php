<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CuocThi;
use App\Models\HoatDongHoTro;
use App\Models\SinhVien;
use App\Models\DangKyHoatDong;
use Carbon\Carbon;

class SupportApiController extends Controller
{
    /**
     * 🔥 API lấy danh sách hoạt động hỗ trợ của 1 cuộc thi
     * GET /api/events/{slug}/support
     */
    public function getSupportActivities($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $now = now();

        $hoatdongs = HoatDongHoTro::where('macuocthi', $macuocthi)
            ->where('loaihoatdong', 'HoTroKyThuat')
            ->where('thoigianketthuc', '>', $now)
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách hoạt động hỗ trợ thành công',
            'data' => $hoatdongs
        ]);
    }

    /**
     * 🔥 API đăng ký hỗ trợ Ban tổ chức
     * POST /api/events/support
     */
    public function registerSupport(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'macuocthi' => 'required',
            'mahoatdong' => 'required|exists:hoatdonghotro,mahoatdong',
            'masinhvien' => 'required',
        ], [
            'mahoatdong.required' => 'Vui lòng chọn hoạt động hỗ trợ',
            'masinhvien.required' => 'Mã sinh viên là bắt buộc',
        ]);

        DB::beginTransaction();

        try {
            // Kiểm tra sinh viên có tồn tại
            $sv = SinhVien::where('masinhvien', $validated['masinhvien'])->first();
            if (!$sv) {
                return response()->json(['success' => false, 'message' => 'Mã sinh viên không tồn tại'], 400);
            }

            // Kiểm tra hoạt động có đúng cuộc thi không
            $hoatdong = HoatDongHoTro::where('mahoatdong', $validated['mahoatdong'])
                ->where('macuocthi', $validated['macuocthi'])
                ->whereIn('loaihoatdong', ['HoTroKyThuat', 'ToChuc'])
                ->first();

            if (!$hoatdong) {
                return response()->json(['success' => false, 'message' => 'Hoạt động không hợp lệ'], 400);
            }

            // Kiểm tra hết hạn
            if (now()->gt($hoatdong->thoigianketthuc)) {
                return response()->json(['success' => false, 'message' => 'Hoạt động đã kết thúc'], 400);
            }

            // Kiểm tra trùng đăng ký
            $exists = DangKyHoatDong::where('mahoatdong', $validated['mahoatdong'])
                ->where('masinhvien', $validated['masinhvien'])
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký hoạt động này'], 400);
            }

            // Tạo mã đăng ký
            $madk = 'DKHD' . strtoupper(Str::random(10));

            // Lưu dữ liệu
            DangKyHoatDong::create([
                'madangkyhoatdong' => $madk,
                'mahoatdong' => $validated['mahoatdong'],
                'masinhvien' => $validated['masinhvien'],
                'ngaydangky' => now(),
                'trangthai' => 'Registered',
                'diemdanhqr' => false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký hỗ trợ thành công!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 API kiểm tra MSSV
     * POST /api/events/support/check-student
     */
    public function checkStudent(Request $request)
    {
        $masv = $request->input('masinhvien');
        $exists = SinhVien::where('masinhvien', $masv)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Lấy mã cuộc thi từ slug
     */
    private function getMaCuocThiFromSlug($slug)
    {
        $parts = explode('-', $slug);
        return end($parts);
    }
}