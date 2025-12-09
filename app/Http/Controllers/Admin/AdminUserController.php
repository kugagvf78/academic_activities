<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\GiangVien;
use App\Models\SinhVien;
use App\Models\Lop;
use App\Models\BoMon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Lấy danh sách người dùng với phân trang và tìm kiếm
     */
    public function index(Request $request)
    {
        try {
            $query = NguoiDung::with(['giangVien.boMon', 'sinhVien.lop']);

            // Tìm kiếm
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('manguoidung', 'LIKE', "%{$search}%")
                      ->orWhere('hoten', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('tendangnhap', 'LIKE', "%{$search}%");
                });
            }

            // Lọc theo vai trò
            if ($request->filled('vaitro')) {
                $query->where('vaitro', $request->vaitro);
            }

            // Lọc theo trạng thái
            if ($request->filled('trangthai')) {
                $query->where('trangthai', $request->trangthai);
            }

            // Lọc theo lớp (cho sinh viên)
            if ($request->filled('malop')) {
                $query->whereHas('sinhVien', function($q) use ($request) {
                    $q->where('malop', $request->malop);
                });
            }

            // Lọc theo bộ môn (cho giảng viên)
            if ($request->filled('mabomon')) {
                $query->whereHas('giangVien', function($q) use ($request) {
                    $q->where('mabomon', $request->mabomon);
                });
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'ngaytao');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Phân trang
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            $users->getCollection()->transform(function ($user) {

                if ($user->anhdaidien && Storage::disk('public')->exists($user->anhdaidien)) {
                    $user->avatar_url = Storage::url($user->anhdaidien);
                } else {
                    $user->avatar_url = asset('images/users/avt.jpg'); // Avatar mặc định
                }

                return $user;
            });


            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách người dùng thành công',
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết người dùng
     */
    public function show($id)
    {
        try {
            $user = NguoiDung::with([
                'giangVien.boMon',
                'giangVien.lopChuNhiem',
                'giangVien.phanCongs',
                'sinhVien.lop',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin người dùng thành công',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Tạo người dùng mới
     */
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'manguoidung' => 'required|string|max:50|unique:nguoidung,manguoidung',
            'tendangnhap' => 'required|string|max:50|unique:nguoidung,tendangnhap',
            'matkhau' => 'required|string|min:6',
            'hoten' => 'required|string|max:100',
            'email' => 'required|email|unique:nguoidung,email',
            'sodienthoai' => 'nullable|string|max:20',
            'vaitro' => ['required', Rule::in(['Admin', 'GiangVien', 'SinhVien'])],
            'trangthai' => ['required', Rule::in(['Active', 'Inactive'])],
        ];

        // Thêm validation cho giảng viên
        if ($request->vaitro === 'GiangVien') {
            $rules['magiangvien'] = 'required|string|max:50|unique:giangvien,magiangvien';
            $rules['mabomon'] = 'nullable|exists:bomon,mabomon';
            $rules['chucvu'] = 'nullable|string|max:100';
            $rules['hocvi'] = 'nullable|string|max:50';
            $rules['chuyenmon'] = 'nullable|string|max:200';
            $rules['is_admin'] = 'boolean';
        }

        // Thêm validation cho sinh viên
        if ($request->vaitro === 'SinhVien') {
            $rules['masinhvien'] = 'required|string|max:50|unique:sinhvien,masinhvien';
            $rules['malop'] = 'nullable|exists:lop,malop';
            $rules['namnhaphoc'] = 'nullable|integer|min:2000|max:2100';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Tạo người dùng
            $user = NguoiDung::create([
                'manguoidung' => $request->manguoidung,
                'tendangnhap' => $request->tendangnhap,
                'matkhau' => Hash::make($request->matkhau),
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
                'vaitro' => $request->vaitro,
                'trangthai' => $request->trangthai,
            ]);

            // Tạo thông tin giảng viên
            if ($request->vaitro === 'GiangVien') {
                GiangVien::create([
                    'magiangvien' => $request->magiangvien,
                    'manguoidung' => $user->manguoidung,
                    'mabomon' => $request->mabomon,
                    'chucvu' => $request->chucvu,
                    'hocvi' => $request->hocvi,
                    'chuyenmon' => $request->chuyenmon,
                    'is_admin' => $request->get('is_admin', false),
                ]);
            }

            // Tạo thông tin sinh viên
            if ($request->vaitro === 'SinhVien') {
                SinhVien::create([
                    'masinhvien' => $request->masinhvien,
                    'manguoidung' => $user->manguoidung,
                    'malop' => $request->malop,
                    'namnhaphoc' => $request->namnhaphoc,
                    'trangthai' => 'Active',
                ]);
            }

            DB::commit();

            // Load relationships
            $user->load(['giangVien', 'sinhVien']);

            return response()->json([
                'success' => true,
                'message' => 'Tạo người dùng thành công',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $user = NguoiDung::findOrFail($id);

        $rules = [
            'tendangnhap' => ['required', 'string', 'max:50', Rule::unique('nguoidung')->ignore($user->manguoidung, 'manguoidung')],
            'hoten' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('nguoidung')->ignore($user->manguoidung, 'manguoidung')],
            'sodienthoai' => 'nullable|string|max:20',
            'trangthai' => ['required', Rule::in(['Active', 'Inactive'])],
        ];

        // Thêm validation cho giảng viên
        if ($user->vaitro === 'GiangVien') {
            $rules['mabomon'] = 'nullable|exists:bomon,mabomon';
            $rules['chucvu'] = 'nullable|string|max:100';
            $rules['hocvi'] = 'nullable|string|max:50';
            $rules['chuyenmon'] = 'nullable|string|max:200';
            $rules['is_admin'] = 'boolean';
        }

        // Thêm validation cho sinh viên
        if ($user->vaitro === 'SinhVien') {
            $rules['malop'] = 'nullable|exists:lop,malop';
            $rules['namnhaphoc'] = 'nullable|integer|min:2000|max:2100';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Cập nhật thông tin người dùng
            $user->update([
                'tendangnhap' => $request->tendangnhap,
                'hoten' => $request->hoten,
                'email' => $request->email,
                'sodienthoai' => $request->sodienthoai,
                'trangthai' => $request->trangthai,
            ]);

            // Cập nhật thông tin giảng viên
            if ($user->vaitro === 'GiangVien' && $user->giangVien) {
                $user->giangVien->update([
                    'mabomon' => $request->mabomon,
                    'chucvu' => $request->chucvu,
                    'hocvi' => $request->hocvi,
                    'chuyenmon' => $request->chuyenmon,
                    'is_admin' => $request->get('is_admin', $user->giangVien->is_admin),
                ]);
            }

            // Cập nhật thông tin sinh viên
            if ($user->vaitro === 'SinhVien' && $user->sinhVien) {
                $user->sinhVien->update([
                    'malop' => $request->malop,
                    'namnhaphoc' => $request->namnhaphoc,
                ]);
            }

            DB::commit();

            $user->load(['giangVien', 'sinhVien']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = NguoiDung::findOrFail($id);

            // Xóa thông tin liên quan
            if ($user->giangVien) {
                $user->giangVien->delete();
            }
            if ($user->sinhVien) {
                $user->sinhVien->delete();
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa người dùng thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset mật khẩu người dùng
     */
    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'matkhau_moi' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = NguoiDung::findOrFail($id);
            $user->update([
                'matkhau' => Hash::make($request->matkhau_moi)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reset mật khẩu thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi reset mật khẩu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chuyển đổi trạng thái người dùng
     */
    public function toggleStatus($id)
    {
        try {
            $user = NguoiDung::findOrFail($id);
            $user->update([
                'trangthai' => $user->trangthai === 'Active' ? 'Inactive' : 'Active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê người dùng
     */
    public function statistics()
    {
        try {
            $stats = [
                'tong_nguoi_dung' => NguoiDung::count(),
                'giang_vien' => NguoiDung::where('vaitro', 'GiangVien')->count(),
                'sinh_vien' => NguoiDung::where('vaitro', 'SinhVien')->count(),
                'hoat_dong' => NguoiDung::where('trangthai', 'Active')->count(),
                'khong_hoat_dong' => NguoiDung::where('trangthai', 'Inactive')->count(),
                'moi_trong_thang' => NguoiDung::whereMonth('ngaytao', now()->month)
                    ->whereYear('ngaytao', now()->year)
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Lấy thống kê thành công',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách lớp
     */
    public function getLops()
    {
        try {
            $lops = Lop::select('malop', 'tenlop')->get();

            return response()->json([
                'success' => true,
                'data' => $lops
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách lớp',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách bộ môn
     */
    public function getBoMons()
    {
        try {
            $bomons = BoMon::select('mabomon', 'tenbomon')->get();

            return response()->json([
                'success' => true,
                'data' => $bomons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bộ môn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import người dùng từ Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Xử lý import Excel
            // Cần cài đặt: composer require maatwebsite/excel
            
            return response()->json([
                'success' => true,
                'message' => 'Import thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi import',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất danh sách người dùng ra Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // Export logic
            return response()->json([
                'success' => true,
                'message' => 'Xuất Excel thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xuất Excel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất danh sách người dùng ra PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Export PDF logic
            return response()->json([
                'success' => true,
                'message' => 'Xuất PDF thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xuất PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa nhiều người dùng
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:nguoidung,manguoidung'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            NguoiDung::whereIn('manguoidung', $request->ids)->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công ' . count($request->ids) . ' người dùng'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kích hoạt nhiều người dùng
     */
    public function bulkActivate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:nguoidung,manguoidung',
            'trangthai' => ['required', Rule::in(['Active', 'Inactive'])]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            NguoiDung::whereIn('manguoidung', $request->ids)
                ->update(['trangthai' => $request->trangthai]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công cho ' . count($request->ids) . ' người dùng'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * SINH MÃ NGƯỜI DÙNG
     * POST /api/admin/users/generate-code
     */
    public function generateCode(Request $request)
    {
        try {
            $vaitro = $request->input('vaitro', 'ND'); // Default ND
            
            // Xác định prefix theo vai trò
            $prefix = match ($vaitro) {
                'GiangVien' => 'GV',
                'SinhVien' => 'SV', // Tạm, nhưng ta sẽ không dùng trong mã
                default => 'ND'
            };

            // 👉 SINH VIÊN: mã kiểu số, không có SV
            // Format: YY + 8 số = 10 ký tự
            if ($prefix === 'SV') {
                $year = date('y'); // 2 số cuối năm

                // Lấy mã lớn nhất trong năm đó (bắt đầu bằng YY)
                $lastUser = NguoiDung::where('manguoidung', 'LIKE', "{$year}%")
                    ->whereRaw('LENGTH(manguoidung) = 10') // đảm bảo đúng 10 số
                    ->orderBy('manguoidung', 'desc')
                    ->first();

                if ($lastUser) {
                    // Lấy phần 8 số phía sau
                    $lastNumber = intval(substr($lastUser->manguoidung, 2));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                // Format: YYxxxxxxxx
                $code = $year . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
            }
            // 👉 GIẢNG VIÊN: GV0001
            elseif ($prefix === 'GV') {
                $lastUser = NguoiDung::where('manguoidung', 'LIKE', 'GV%')
                    ->orderBy('manguoidung', 'desc')
                    ->first();

                if ($lastUser) {
                    $lastNumber = intval(substr($lastUser->manguoidung, 2));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $code = 'GV' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
            // 👉 NGƯỜI DÙNG: ND0001
            else {
                $lastUser = NguoiDung::where('manguoidung', 'LIKE', 'ND%')
                    ->orderBy('manguoidung', 'desc')
                    ->first();

                if ($lastUser) {
                    $lastNumber = intval(substr($lastUser->manguoidung, 2));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $code = 'ND' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }

            // Chống trùng mã
            while (NguoiDung::where('manguoidung', $code)->exists()) {
                $newNumber++;
                if ($prefix === 'SV') {
                    $code = $year . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
                } else {
                    $code = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                }
            }

            return response()->json([
                'success' => true,
                'code' => $code,
                'message' => 'Sinh mã thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi sinh mã',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}