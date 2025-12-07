<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminCompetitionController extends Controller
{
    /**
     * Lấy danh sách cuộc thi với phân trang và tìm kiếm
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $sortBy = $request->input('sort_by', 'ngaytao');
            $sortOrder = $request->input('sort_order', 'desc');

            $query = DB::table('cuocthi')
                ->select('cuocthi.*')
                ->orderBy($sortBy, $sortOrder);

            // Tìm kiếm
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('tencuocthi', 'ILIKE', "%{$search}%")
                      ->orWhere('mota', 'ILIKE', "%{$search}%");
                });
            }

            // Lọc theo trạng thái
            if ($status) {
                $query->where('trangthai', $status);
            }

            $competitions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $competitions->items(),
                'pagination' => [
                    'total' => $competitions->total(),
                    'per_page' => $competitions->perPage(),
                    'current_page' => $competitions->currentPage(),
                    'last_page' => $competitions->lastPage(),
                    'from' => $competitions->firstItem(),
                    'to' => $competitions->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê cuộc thi
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => DB::table('cuocthi')->count(),
                'chuanbi' => DB::table('cuocthi')->where('trangthai', 'ChuanBi')->count(),
                'dangdienra' => DB::table('cuocthi')->where('trangthai', 'DangDienRa')->count(),
                'ketthuc' => DB::table('cuocthi')->where('trangthai', 'KetThuc')->count(),
                'dahuy' => DB::table('cuocthi')->where('trangthai', 'DaHuy')->count(),
                'total_participants' => DB::table('dangky')->count(),
                'total_submissions' => DB::table('bailam')->count(),
            ];

            // Cuộc thi theo tháng (6 tháng gần nhất)
            $monthlyData = DB::table('cuocthi')
                ->select(
                    DB::raw("TO_CHAR(ngaytao, 'YYYY-MM') as month"),
                    DB::raw('COUNT(*) as count')
                )
                ->where('ngaytao', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'monthly' => $monthlyData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết cuộc thi
     */
    public function show($id)
    {
        try {
            $competition = DB::table('cuocthi')
                ->where('macuocthi', $id)
                ->first();

            if (!$competition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc thi'
                ], 404);
            }

            // Lấy thống kê liên quan
            $participantsCount = DB::table('dangky')
                ->where('macuocthi', $id)
                ->count();

            $submissionsCount = DB::table('bailam')
                ->where('macuocthi', $id)
                ->count();

            $competition->participants_count = $participantsCount;
            $competition->submissions_count = $submissionsCount;

            return response()->json([
                'success' => true,
                'data' => $competition
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo cuộc thi mới
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tencuocthi' => 'required|string|max:255',
                'mota' => 'nullable|string',
                'thoigianbatdau' => 'required|date',
                'thoigianketthuc' => 'required|date|after:thoigianbatdau',
                'trangthai' => 'required|in:ChuanBi,DangDienRa,KetThuc,DaHuy',
                'loaicuocthi' => 'nullable|string|max:100',
                'hinhanh' => 'nullable|string',
            ], [
                'tencuocthi.required' => 'Tên cuộc thi là bắt buộc',
                'thoigianbatdau.required' => 'Thời gian bắt đầu là bắt buộc',
                'thoigianketthuc.required' => 'Thời gian kết thúc là bắt buộc',
                'thoigianketthuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu',
                'trangthai.required' => 'Trạng thái là bắt buộc',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $macuocthi = DB::table('cuocthi')->insertGetId([
                'tencuocthi' => $request->tencuocthi,
                'mota' => $request->mota,
                'thoigianbatdau' => $request->thoigianbatdau,
                'thoigianketthuc' => $request->thoigianketthuc,
                'trangthai' => $request->trangthai,
                'loaicuocthi' => $request->loaicuocthi,
                'hinhanh' => $request->hinhanh,
                'ngaytao' => now(),
                'ngaycapnhat' => now(),
            ], 'macuocthi');

            $competition = DB::table('cuocthi')->where('macuocthi', $macuocthi)->first();

            return response()->json([
                'success' => true,
                'message' => 'Tạo cuộc thi thành công',
                'data' => $competition
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật cuộc thi
     */
    public function update(Request $request, $id)
    {
        try {
            $competition = DB::table('cuocthi')->where('macuocthi', $id)->first();

            if (!$competition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc thi'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'tencuocthi' => 'sometimes|required|string|max:255',
                'mota' => 'nullable|string',
                'thoigianbatdau' => 'sometimes|required|date',
                'thoigianketthuc' => 'sometimes|required|date|after:thoigianbatdau',
                'trangthai' => 'sometimes|required|in:ChuanBi,DangDienRa,KetThuc,DaHuy',
                'loaicuocthi' => 'nullable|string|max:100',
                'hinhanh' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = array_filter($request->only([
                'tencuocthi',
                'mota',
                'thoigianbatdau',
                'thoigianketthuc',
                'trangthai',
                'loaicuocthi',
                'hinhanh'
            ]), function($value) {
                return $value !== null;
            });

            $updateData['ngaycapnhat'] = now();

            DB::table('cuocthi')
                ->where('macuocthi', $id)
                ->update($updateData);

            $updated = DB::table('cuocthi')->where('macuocthi', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cuộc thi thành công',
                'data' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa cuộc thi
     */
    public function destroy($id)
    {
        try {
            $competition = DB::table('cuocthi')->where('macuocthi', $id)->first();

            if (!$competition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc thi'
                ], 404);
            }

            // Kiểm tra xem có đăng ký nào không
            $hasRegistrations = DB::table('dangky')
                ->where('macuocthi', $id)
                ->exists();

            if ($hasRegistrations) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa cuộc thi đã có người đăng ký. Vui lòng hủy cuộc thi thay vì xóa.'
                ], 400);
            }

            DB::table('cuocthi')->where('macuocthi', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa cuộc thi thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái cuộc thi
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trangthai' => 'required|in:ChuanBi,DangDienRa,KetThuc,DaHuy'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $competition = DB::table('cuocthi')->where('macuocthi', $id)->first();

            if (!$competition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc thi'
                ], 404);
            }

            DB::table('cuocthi')
                ->where('macuocthi', $id)
                ->update([
                    'trangthai' => $request->trangthai,
                    'ngaycapnhat' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa nhiều cuộc thi
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Kiểm tra xem có cuộc thi nào có đăng ký không
            $hasRegistrations = DB::table('dangky')
                ->whereIn('macuocthi', $request->ids)
                ->exists();

            if ($hasRegistrations) {
                return response()->json([
                    'success' => false,
                    'message' => 'Một số cuộc thi đã có người đăng ký, không thể xóa'
                ], 400);
            }

            $deleted = DB::table('cuocthi')
                ->whereIn('macuocthi', $request->ids)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deleted} cuộc thi"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}