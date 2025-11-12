<?php

namespace App\Http\Controllers\API;

use App\Models\GiangVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiangVienController extends BaseApiController
{
    /**
     * GET /api/giang-vien - Lấy danh sách giảng viên
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search', '');

            $query = GiangVien::with(['nguoiDung', 'boMon']);

            // Tìm kiếm
            if ($search) {
                $query->whereHas('nguoiDung', function ($q) use ($search) {
                    $q->where('hoten', 'LIKE', "%{$search}%")
                      ->orWhere('tendangnhap', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Lọc theo bộ môn
            if ($request->has('mabomon')) {
                $query->where('mabomon', $request->mabomon);
            }

            // Lọc theo chức vụ
            if ($request->has('chucvu')) {
                $query->where('chucvu', $request->chucvu);
            }

            $giangViens = $query->paginate($perPage);

            return $this->successResponse($giangViens, 'Lấy danh sách giảng viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/giang-vien/{id} - Lấy thông tin chi tiết giảng viên
     */
    public function show($id)
    {
        try {
            $giangVien = GiangVien::with(['nguoiDung', 'boMon', 'lopChuNhiem'])
                ->where('magiangvien', $id)
                ->first();

            if (!$giangVien) {
                return $this->notFoundResponse('Không tìm thấy giảng viên');
            }

            return $this->successResponse($giangVien, 'Lấy thông tin giảng viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * POST /api/giang-vien - Tạo giảng viên mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaNguoiDung' => 'required|string|exists:nguoidung,manguoidung',
            'MaBoMon' => 'nullable|string|exists:bomon,mabomon',
            'ChucVu' => 'nullable|string|max:100',
            'HocVi' => 'nullable|string|max:50',
            'ChuyenMon' => 'nullable|string',
        ], [
            'MaNguoiDung.required' => 'Mã người dùng là bắt buộc',
            'MaNguoiDung.exists' => 'Người dùng không tồn tại',
            'MaBoMon.exists' => 'Bộ môn không tồn tại',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Tạo mã giảng viên tự động
            $maGiangVien = 'GV' . str_pad(GiangVien::count() + 1, 6, '0', STR_PAD_LEFT);

            $giangVien = GiangVien::create([
                'magiangvien' => $maGiangVien,
                'manguoidung' => $request->MaNguoiDung,
                'mabomon' => $request->MaBoMon,
                'chucvu' => $request->ChucVu,
                'hocvi' => $request->HocVi,
                'chuyenmon' => $request->ChuyenMon,
            ]);

            $giangVien->load(['nguoiDung', 'boMon']);

            return $this->successResponse($giangVien, 'Tạo giảng viên thành công', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * PUT /api/giang-vien/{id} - Cập nhật giảng viên
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'MaBoMon' => 'nullable|string|exists:bomon,mabomon',
            'ChucVu' => 'nullable|string|max:100',
            'HocVi' => 'nullable|string|max:50',
            'ChuyenMon' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $giangVien = GiangVien::where('magiangvien', $id)->first();

            if (!$giangVien) {
                return $this->notFoundResponse('Không tìm thấy giảng viên');
            }

            $updateData = [];
            if ($request->has('MaBoMon')) $updateData['mabomon'] = $request->MaBoMon;
            if ($request->has('ChucVu')) $updateData['chucvu'] = $request->ChucVu;
            if ($request->has('HocVi')) $updateData['hocvi'] = $request->HocVi;
            if ($request->has('ChuyenMon')) $updateData['chuyenmon'] = $request->ChuyenMon;

            $giangVien->update($updateData);
            $giangVien->load(['nguoiDung', 'boMon']);

            return $this->successResponse($giangVien, 'Cập nhật giảng viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /api/giang-vien/{id} - Xóa giảng viên
     */
    public function destroy($id)
    {
        try {
            $giangVien = GiangVien::where('magiangvien', $id)->first();

            if (!$giangVien) {
                return $this->notFoundResponse('Không tìm thấy giảng viên');
            }

            // Kiểm tra xem giảng viên có dữ liệu liên quan không
            if ($giangVien->phanCongs()->exists() || $giangVien->lopChuNhiem()->exists()) {
                return $this->errorResponse('Không thể xóa giảng viên đã có dữ liệu liên quan', null, 400);
            }

            $giangVien->delete();

            return $this->successResponse(null, 'Xóa giảng viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/giang-vien/profile - Lấy thông tin giảng viên đang đăng nhập
     */
    public function profile(Request $request)
    {
        try {
            $user = auth('api')->user();
            
            if (!$user) {
                return $this->unauthorizedResponse('Vui lòng đăng nhập');
            }

            $giangVien = GiangVien::with(['nguoiDung', 'boMon', 'lopChuNhiem', 'phanCongs'])
                ->where('manguoidung', $user->manguoidung)
                ->first();

            if (!$giangVien) {
                return $this->notFoundResponse('Không tìm thấy thông tin giảng viên');
            }

            return $this->successResponse($giangVien, 'Lấy thông tin thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }
}