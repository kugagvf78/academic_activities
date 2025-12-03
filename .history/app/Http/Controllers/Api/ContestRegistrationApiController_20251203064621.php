<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CuocThi;
use App\Models\SinhVien;
use App\Models\DoiThi;
use App\Models\DangKyCaNhan;
use App\Models\DangKyDoiThi;
use App\Models\ThanhVienDoiThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContestApiController extends Controller
{
    /**
     * API: Hiển thị form đăng ký cuộc thi
     * GET /api/events/{slug}/register
     */
    public function showRegistrationForm($slug)
    {
        try {
            $macuocthi = $this->getMaCuocThiFromSlug($slug);

            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();

            // Kiểm tra trạng thái cuộc thi
            if (!in_array($cuocthi->trangthai, ['Approved', 'InProgress'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi không mở đăng ký'
                ], 400);
            }

            // Kiểm tra thời gian - CHỈ CHO ĐĂNG KÝ KHI CHƯA BẮT ĐẦU
            $now = now();
            $start = $cuocthi->thoigianbatdau;

            if ($now->gte($start)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi đã bắt đầu, không thể đăng ký thêm'
                ], 400);
            }

            // Kiểm tra hình thức tham gia có hợp lệ không
            if (empty($cuocthi->hinhthucthamgia)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi chưa xác định hình thức tham gia'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Form đăng ký cuộc thi',
                'data' => [
                    'cuocthi' => $cuocthi,
                    'slug' => $slug,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin cuộc thi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Xử lý đăng ký cuộc thi
     * POST /api/events/{slug}/register
     */
    public function register(Request $request, $slug)
    {
        try {
            $macuocthi = $this->getMaCuocThiFromSlug($slug);
            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();

            // Kiểm tra hình thức tham gia
            $requestType = $request->input('type');
            if ($cuocthi->hinhthucthamgia === 'CaNhan' && $requestType !== 'individual') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi này chỉ cho phép đăng ký cá nhân'
                ], 400);
            }

            if ($cuocthi->hinhthucthamgia === 'DoiNhom' && $requestType !== 'team') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc thi này chỉ cho phép đăng ký theo đội/nhóm'
                ], 400);
            }

            if ($cuocthi->hinhthucthamgia === 'CaHai' && !in_array($requestType, ['individual', 'team'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hình thức đăng ký không hợp lệ'
                ], 400);
            }

            // Validate dữ liệu
            $rules = [
                'type' => 'required|in:individual,team',
                'main_name' => 'required|string|max:255',
                'main_student_code' => 'required|string|max:50',
                'main_email' => 'required|email|max:255',
                'main_phone' => 'required|string|max:20',
                'team_name' => 'required|string|max:255',
                'note' => 'nullable|string|max:1000',
            ];

            if ($requestType === 'team') {
                $rules['members'] = 'required|array|min:1';
                $rules['members.*.name'] = 'required|string|max:255';
                $rules['members.*.student_code'] = 'required|string|max:50';
                $rules['members.*.email'] = 'required|email|max:255';
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Kiểm tra sinh viên chính có tồn tại không
            $sinhvienChinh = SinhVien::where('masinhvien', $validated['main_student_code'])->first();

            if (!$sinhvienChinh) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã sinh viên không tồn tại trong hệ thống'
                ], 400);
            }

            // Xử lý đăng ký cá nhân
            if ($validated['type'] === 'individual') {
                $daDangKyCaNhan = DangKyCaNhan::where('macuocthi', $macuocthi)
                    ->where('masinhvien', $sinhvienChinh->masinhvien)
                    ->exists();

                if ($daDangKyCaNhan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã đăng ký cá nhân cuộc thi này rồi!'
                    ], 400);
                }

                // Tạo đăng ký cá nhân
                $madangkycanhan = 'DKCN' . Str::upper(Str::random(8));

                DangKyCaNhan::create([
                    'madangkycanhan' => $madangkycanhan,
                    'macuocthi' => $macuocthi,
                    'masinhvien' => $sinhvienChinh->masinhvien,
                    'ngaydangky' => now(),
                    'trangthai' => 'Registered',
                    'ghichu' => $validated['note'] ?? null,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đăng ký cá nhân thành công!'
                ], 200);
            }

            // Xử lý đăng ký theo đội
            if ($validated['type'] === 'team') {
                if (empty($validated['members']) || count($validated['members']) < 1) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Đội thi phải có ít nhất 1 thành viên ngoài trưởng đội'
                    ], 400);
                }

                // Tạo đội thi
                $madoithi = 'DT' . Str::upper(Str::random(8));

                $sothanhvien = 1 + count($validated['members']);

                $doithi = DoiThi::create([
                    'madoithi' => $madoithi,
                    'tendoithi' => $validated['team_name'],
                    'macuocthi' => $macuocthi,
                    'matruongdoi' => $sinhvienChinh->masinhvien,
                    'sothanhvien' => $sothanhvien,
                    'ngaydangky' => now(),
                    'trangthai' => 'Active',
                ]);

                // Lưu trưởng đội vào bảng ThanhVienDoiThi
                $mathanhvienTruongDoi = 'TV' . Str::upper(Str::random(8));

                ThanhVienDoiThi::create([
                    'mathanhvien' => $mathanhvienTruongDoi,
                    'madoithi' => $madoithi,
                    'masinhvien' => $sinhvienChinh->masinhvien,
                    'vaitro' => 'TruongDoi',
                    'ngaythamgia' => now(),
                ]);

                // Thêm thành viên nhóm
                foreach ($validated['members'] as $member) {
                    $sinhvienThanhVien = SinhVien::where('masinhvien', $member['student_code'])->first();

                    if (!$sinhvienThanhVien) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Mã sinh viên {$member['student_code']} không tồn tại"
                        ], 400);
                    }

                    // Kiểm tra thành viên đã đăng ký cá nhân chưa
                    $thanhVienDaDangKyCaNhan = DangKyCaNhan::where('macuocthi', $macuocthi)
                        ->where('masinhvien', $sinhvienThanhVien->masinhvien)
                        ->exists();

                    if ($thanhVienDaDangKyCaNhan) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Sinh viên {$member['name']} đã đăng ký cá nhân cuộc thi này"
                        ], 400);
                    }

                    // Kiểm tra thành viên đã trong đội khác chưa
                    $daTrongDoiKhac = ThanhVienDoiThi::join('doithi', 'thanhviendoithi.madoithi', '=', 'doithi.madoithi')
                        ->where('doithi.macuocthi', $macuocthi)
                        ->where('thanhviendoithi.masinhvien', $sinhvienThanhVien->masinhvien)
                        ->exists();

                    if ($daTrongDoiKhac) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Sinh viên {$member['name']} đã tham gia đội khác trong cuộc thi này"
                        ], 400);
                    }

                    $mathanhvien = 'TV' . Str::upper(Str::random(8));

                    ThanhVienDoiThi::create([
                        'mathanhvien' => $mathanhvien,
                        'madoithi' => $madoithi,
                        'masinhvien' => $sinhvienThanhVien->masinhvien,
                        'vaitro' => 'ThanhVien',
                        'ngaythamgia' => now(),
                    ]);
                }

                // Tạo đăng ký đội thi
                $madangkydoi = 'DKDT' . Str::upper(Str::random(8));

                DangKyDoiThi::create([
                    'madangkydoi' => $madangkydoi,
                    'macuocthi' => $macuocthi,
                    'madoithi' => $madoithi,
                    'ngaydangky' => now(),
                    'trangthai' => 'Registered',
                    'ghichu' => $validated['note'] ?? null,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đăng ký đội thi thành công!'
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Lỗi đăng ký cuộc thi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký',
                'error' => $e->getMessage(),
            ], 500);
        }
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
