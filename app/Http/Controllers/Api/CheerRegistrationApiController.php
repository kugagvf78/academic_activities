<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CuocThi;
use App\Models\HoatDongHoTro;
use App\Models\DangKyHoatDong;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheerRegistrationApiController extends Controller
{
    /**
     * API: Lấy danh sách hoạt động cổ vũ còn chỗ
     * GET /api/events/{slug}/cheer
     */
    public function getCheerActivities($slug)
    {
        try {
            $macuocthi = $this->getMaCuocThiFromSlug($slug);

            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
            $now = now();

            // Điều kiện cho phép đăng ký cổ vũ
            if ($cuocthi->trangthai !== 'Approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi chưa mở đăng ký cổ vũ.'
                ], 400);
            }

            $start = $cuocthi->thoigianbatdau;
            $earlyRegister = $start->copy()->subDays(7);

            if ($now->lt($earlyRegister)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đăng ký cổ vũ sẽ mở vào: '.$earlyRegister->format('d/m/Y H:i')
                ], 400);
            }

            if ($now->gte($start)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi đã bắt đầu — không thể đăng ký cổ vũ.'
                ], 400);
            }

            // Lấy các hoạt động còn chỗ
            $hoatdongs = HoatDongHoTro::select('hoatdonghotro.*')
                ->selectRaw('(SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) as dadangky')
                ->where('macuocthi', $macuocthi)
                ->where('loaihoatdong', 'CoVu')
                ->where('thoigianketthuc', '>', $now)
                ->havingRaw('dadangky < soluong')
                ->orderBy('thoigianbatdau', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách hoạt động cổ vũ thành công.',
                'data' => $hoatdongs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy danh sách hoạt động cổ vũ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * API: Đăng ký cổ vũ
     * POST /api/events/{slug}/cheer
     */
    public function registerCheer(Request $request, $slug)
    {
        $request->validate([
            'mahoatdong' => 'required|exists:hoatdonghotro,mahoatdong',
            'masinhvien' => 'required|string|max:20',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $macuocthi = $this->getMaCuocThiFromSlug($slug);
            $now = now();

            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();

            // Check sinh viên
            $sinhvien = SinhVien::where('masinhvien', $request->masinhvien)->first();
            if (!$sinhvien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã sinh viên không tồn tại.'
                ], 400);
            }

            // Lấy hoạt động
            $hd = HoatDongHoTro::select('hoatdonghotro.*')
                ->selectRaw('(SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) as dadangky')
                ->where('mahoatdong', $request->mahoatdong)
                ->where('macuocthi', $macuocthi)
                ->where('loaihoatdong', 'CoVu')
                ->firstOrFail();

            if ($hd->thoigianketthuc <= $now) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hoạt động đã kết thúc.'
                ], 400);
            }

            if ($hd->dadangky >= $hd->soluong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hoạt động đã hết chỗ.'
                ], 400);
            }

            // Check trùng đăng ký
            $tonTai = DangKyHoatDong::where('mahoatdong', $request->mahoatdong)
                ->where('masinhvien', $request->masinhvien)
                ->exists();

            if ($tonTai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đăng ký hoạt động này.'
                ], 400);
            }

            // Tạo mã DK
            $madk = 'DKCV' . Str::upper(Str::random(8));

            DangKyHoatDong::create([
                'madangkyhoatdong' => $madk,
                'mahoatdong'       => $hd->mahoatdong,
                'masinhvien'       => $request->masinhvien,
                'ngaydangky'       => $now,
                'trangthai'        => 'Registered',
                'diemdanhqr'       => false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký cổ vũ thành công!',
                'madangky' => $madk
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Cheer Registration Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi đăng ký cổ vũ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function getMaCuocThiFromSlug($slug)
    {
        $parts = explode('-', $slug);
        return end($parts);
    }
}
