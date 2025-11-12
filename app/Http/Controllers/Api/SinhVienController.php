<?php

namespace App\Http\Controllers\API;

use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SinhVienController extends BaseApiController
{
    /**
     * GET /api/sinh-vien - Lấy danh sách sinh viên
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search', '');

            $query = SinhVien::with(['nguoiDung', 'lop']);

            // Tìm kiếm
            if ($search) {
                $query->whereHas('nguoiDung', function ($q) use ($search) {
                    $q->where('hoten', 'LIKE', "%{$search}%")
                      ->orWhere('tendangnhap', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Lọc theo lớp
            if ($request->has('malop')) {
                $query->where('malop', $request->malop);
            }

            // Lọc theo trạng thái
            if ($request->has('trangthai')) {
                $query->where('trangthai', $request->trangthai);
            }

            $sinhViens = $query->paginate($perPage);

            return $this->successResponse($sinhViens, 'Lấy danh sách sinh viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/sinh-vien/{id} - Lấy thông tin chi tiết sinh viên
     */
    public function show($id)
    {
        try {
            $sinhVien = SinhVien::with(['nguoiDung', 'lop'])
                ->where('masinhvien', $id)
                ->first();

            if (!$sinhVien) {
                return $this->notFoundResponse('Không tìm thấy sinh viên');
            }

            return $this->successResponse($sinhVien, 'Lấy thông tin sinh viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * POST /api/sinh-vien - Tạo sinh viên mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaNguoiDung' => 'required|string|exists:nguoidung,manguoidung',
            'MaLop' => 'nullable|string|exists:lop,malop',
            'NamNhapHoc' => 'nullable|integer|min:1900|max:2100',
            'DiemRenLuyen' => 'nullable|numeric|min:0|max:100',
        ], [
            'MaNguoiDung.required' => 'Mã người dùng là bắt buộc',
            'MaNguoiDung.exists' => 'Người dùng không tồn tại',
            'MaLop.exists' => 'Lớp không tồn tại',
            'NamNhapHoc.integer' => 'Năm nhập học phải là số nguyên',
            'DiemRenLuyen.numeric' => 'Điểm rèn luyện phải là số',
            'DiemRenLuyen.max' => 'Điểm rèn luyện tối đa là 100',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Tạo mã sinh viên tự động
            $maSinhVien = 'SV' . str_pad(SinhVien::count() + 1, 6, '0', STR_PAD_LEFT);

            $sinhVien = SinhVien::create([
                'masinhvien' => $maSinhVien,
                'manguoidung' => $request->MaNguoiDung,
                'malop' => $request->MaLop,
                'namnhaphoc' => $request->NamNhapHoc,
                'diemrenluyen' => $request->DiemRenLuyen ?? 0,
                'trangthai' => 'Active',
            ]);

            $sinhVien->load(['nguoiDung', 'lop']);

            return $this->successResponse($sinhVien, 'Tạo sinh viên thành công', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * PUT /api/sinh-vien/{id} - Cập nhật sinh viên
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'MaLop' => 'nullable|string|exists:lop,malop',
            'NamNhapHoc' => 'nullable|integer|min:1900|max:2100',
            'DiemRenLuyen' => 'nullable|numeric|min:0|max:100',
            'TrangThai' => 'nullable|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $sinhVien = SinhVien::where('masinhvien', $id)->first();

            if (!$sinhVien) {
                return $this->notFoundResponse('Không tìm thấy sinh viên');
            }

            $updateData = [];
            if ($request->has('MaLop')) $updateData['malop'] = $request->MaLop;
            if ($request->has('NamNhapHoc')) $updateData['namnhaphoc'] = $request->NamNhapHoc;
            if ($request->has('DiemRenLuyen')) $updateData['diemrenluyen'] = $request->DiemRenLuyen;
            if ($request->has('TrangThai')) $updateData['trangthai'] = $request->TrangThai;

            $sinhVien->update($updateData);
            $sinhVien->load(['nguoiDung', 'lop']);

            return $this->successResponse($sinhVien, 'Cập nhật sinh viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /api/sinh-vien/{id} - Xóa sinh viên
     */
    public function destroy($id)
    {
        try {
            $sinhVien = SinhVien::where('masinhvien', $id)->first();

            if (!$sinhVien) {
                return $this->notFoundResponse('Không tìm thấy sinh viên');
            }

            // Kiểm tra xem sinh viên có dữ liệu liên quan không
            if ($sinhVien->dangKyDuThis()->exists() || $sinhVien->thanhVienDoiThis()->exists()) {
                return $this->errorResponse('Không thể xóa sinh viên đã có dữ liệu liên quan', null, 400);
            }

            $sinhVien->delete();

            return $this->successResponse(null, 'Xóa sinh viên thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/sinh-vien/profile - Lấy thông tin sinh viên đang đăng nhập
     */
    public function profile(Request $request)
    {
        try {
            $user = auth('api')->user();
            
            if (!$user) {
                return $this->unauthorizedResponse('Vui lòng đăng nhập');
            }

            $sinhVien = SinhVien::with(['nguoiDung', 'lop', 'dangKyDuThis'])
                ->where('manguoidung', $user->manguoidung)
                ->first();

            if (!$sinhVien) {
                return $this->notFoundResponse('Không tìm thấy thông tin sinh viên');
            }

            return $this->successResponse($sinhVien, 'Lấy thông tin thành công');

        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra: ' . $e->getMessage(), null, 500);
        }
    }
}