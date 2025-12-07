<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminNewsController extends Controller
{
    /**
     * Lấy danh sách tin tức (API)
     */
    public function index(Request $request)
    {
        $query = DB::table('tintuc as tt')
            ->leftJoin('cuocthi as ct', 'tt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('giangvien as gv', 'tt.tacgia', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->select(
                'tt.matintuc',
                'tt.tieude',
                'tt.noidung',
                'tt.macuocthi',
                'tt.loaitin',
                'tt.hinhanh',
                'tt.tacgia',
                'tt.luotxem',
                'tt.trangthai',
                'tt.ngaydang',
                'tt.ngaycapnhat',
                'ct.tencuocthi',
                'nd.hoten as tentacgia'
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tt.tieude', 'ILIKE', "%{$search}%")
                  ->orWhere('tt.noidung', 'ILIKE', "%{$search}%")
                  ->orWhere('ct.tencuocthi', 'ILIKE', "%{$search}%");
            });
        }

        // Lọc theo loại tin
        if ($request->filled('loaitin') && $request->loaitin !== 'all') {
            $query->where('tt.loaitin', $request->loaitin);
        }

        // Lọc theo trạng thái
        if ($request->filled('trangthai') && $request->trangthai !== 'all') {
            $query->where('tt.trangthai', $request->trangthai);
        }

        // Lọc theo cuộc thi
        if ($request->filled('macuocthi') && $request->macuocthi !== 'all') {
            $query->where('tt.macuocthi', $request->macuocthi);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'ngaydang');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy("tt.$sortBy", $sortOrder);

        // Phân trang
        $perPage = $request->get('per_page', 10);
        $news = $query->paginate($perPage);

        // Transform data
        $news->getCollection()->transform(function ($item) {
            $item->ngaydang_formatted = $item->ngaydang ? Carbon::parse($item->ngaydang)->format('d/m/Y H:i') : null;
            $item->ngaycapnhat_formatted = $item->ngaycapnhat ? Carbon::parse($item->ngaycapnhat)->format('d/m/Y H:i') : null;
            $item->excerpt = $this->getExcerpt($item->noidung);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Thống kê tin tức
     */
    public function statistics()
    {
        $stats = [
            'total' => DB::table('tintuc')->count(),
            'published' => DB::table('tintuc')->where('trangthai', 'Published')->count(),
            'draft' => DB::table('tintuc')->where('trangthai', 'Draft')->count(),
            'archived' => DB::table('tintuc')->where('trangthai', 'Archived')->count(),
            'by_type' => DB::table('tintuc')
                ->select('loaitin', DB::raw('count(*) as total'))
                ->groupBy('loaitin')
                ->get(),
            'this_month' => DB::table('tintuc')
                ->whereMonth('ngaydang', date('m'))
                ->whereYear('ngaydang', date('Y'))
                ->count(),
            'total_views' => DB::table('tintuc')->sum('luotxem'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Lấy danh sách cuộc thi để chọn
     */
    public function getCuocThi()
    {
        $cuocthis = DB::table('cuocthi')
            ->select('macuocthi', 'tencuocthi', 'trangthai')
            ->orderBy('thoigianbatdau', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cuocthis
        ]);
    }

    /**
     * Lấy chi tiết tin tức
     */
    public function show($id)
    {
        $news = DB::table('tintuc as tt')
            ->leftJoin('cuocthi as ct', 'tt.macuocthi', '=', 'ct.macuocthi')
            ->leftJoin('giangvien as gv', 'tt.tacgia', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('tt.matintuc', $id)
            ->select(
                'tt.*',
                'ct.tencuocthi',
                'nd.hoten as tentacgia'
            )
            ->first();

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        $news->ngaydang_formatted = $news->ngaydang ? Carbon::parse($news->ngaydang)->format('d/m/Y H:i') : null;
        $news->ngaycapnhat_formatted = $news->ngaycapnhat ? Carbon::parse($news->ngaycapnhat)->format('d/m/Y H:i') : null;

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Tạo tin tức mới
     */
    public function store(Request $request)
    {
        // Thêm logging chi tiết
        Log::info('=== NEWS STORE REQUEST ===');
        Log::info('URL: ' . $request->fullUrl());
        Log::info('Method: ' . $request->method());
        Log::info('Headers: ', $request->headers->all());
        Log::info('All Input: ', $request->all());
        Log::info('Has File: ' . ($request->hasFile('hinhanh') ? 'YES' : 'NO'));
        Log::info('Content-Type: ' . $request->header('Content-Type'));
        
        $validator = Validator::make($request->all(), [
            'tieude' => 'required|string|max:255',
            'noidung' => 'required|string',
            'loaitin' => 'required|in:TinTuc,ThongBao,SuKien',
            'trangthai' => 'required|in:Draft,Published,Archived',
            'macuocthi' => 'nullable|exists:cuocthi,macuocthi',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'tieude.required' => 'Tiêu đề không được để trống',
            'noidung.required' => 'Nội dung không được để trống',
            'loaitin.required' => 'Vui lòng chọn loại tin',
            'loaitin.in' => 'Loại tin không hợp lệ',
            'trangthai.required' => 'Vui lòng chọn trạng thái',
            'trangthai.in' => 'Trạng thái không hợp lệ',
            'macuocthi.exists' => 'Cuộc thi không tồn tại',
            'hinhanh.image' => 'File phải là ảnh',
            'hinhanh.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif',
            'hinhanh.max' => 'Ảnh không được vượt quá 2MB',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Lấy thông tin giảng viên admin
            $user = jwt_user();
            Log::info('User from JWT:', ['user' => $user]);
            
            if (!$user) {
                Log::error('User not found from JWT');
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 401);
            }

            $giangvien = DB::table('giangvien')
                ->where('manguoidung', $user->manguoidung)
                ->first();

            Log::info('Giangvien:', ['giangvien' => $giangvien]);

            if (!$giangvien) {
                Log::error('Giangvien not found for user: ' . $user->manguoidung);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin giảng viên'
                ], 404);
            }

            // Tạo mã tin tức
            $matintuc = $this->generateMatintuc();
            Log::info('Generated matintuc: ' . $matintuc);

            // Upload ảnh nếu có
            $hinhanhPath = null;
            if ($request->hasFile('hinhanh')) {
                Log::info('Processing image upload...');
                $file = $request->file('hinhanh');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Tạo thư mục nếu chưa tồn tại
                $uploadPath = public_path('uploads/news');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                    Log::info('Created directory: ' . $uploadPath);
                }
                
                $file->move($uploadPath, $filename);
                $hinhanhPath = 'uploads/news/' . $filename;
                Log::info('Image uploaded: ' . $hinhanhPath);
            }

            // Insert tin tức
            $insertData = [
                'matintuc' => $matintuc,
                'tieude' => $request->tieude,
                'noidung' => $request->noidung,
                'macuocthi' => $request->macuocthi,
                'loaitin' => $request->loaitin,
                'hinhanh' => $hinhanhPath,
                'tacgia' => $giangvien->magiangvien,
                'luotxem' => 0,
                'trangthai' => $request->trangthai,
                'ngaydang' => now(),
                'ngaycapnhat' => now(),
            ];
            
            Log::info('Inserting data:', $insertData);
            
            DB::table('tintuc')->insert($insertData);
            
            Log::info('News created successfully: ' . $matintuc);

            return response()->json([
                'success' => true,
                'message' => 'Tạo tin tức thành công',
                'data' => ['matintuc' => $matintuc]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating news: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Cập nhật tin tức
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tieude' => 'required|string|max:255',
            'noidung' => 'required|string',
            'loaitin' => 'required|in:TinTuc,ThongBao,SuKien',
            'trangthai' => 'required|in:Draft,Published,Archived',
            'macuocthi' => 'nullable|exists:cuocthi,macuocthi',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $news = DB::table('tintuc')->where('matintuc', $id)->first();

            if (!$news) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tin tức'
                ], 404);
            }

            $updateData = [
                'tieude' => $request->tieude,
                'noidung' => $request->noidung,
                'macuocthi' => $request->macuocthi,
                'loaitin' => $request->loaitin,
                'trangthai' => $request->trangthai,
                'ngaycapnhat' => now(),
            ];

            // Upload ảnh mới nếu có
            if ($request->hasFile('hinhanh')) {
                // Xóa ảnh cũ
                if ($news->hinhanh && file_exists(public_path($news->hinhanh))) {
                    unlink(public_path($news->hinhanh));
                }

                $file = $request->file('hinhanh');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/news'), $filename);
                $updateData['hinhanh'] = 'uploads/news/' . $filename;
            }

            DB::table('tintuc')->where('matintuc', $id)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tin tức thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa tin tức
     */
    public function destroy($id)
    {
        try {
            $news = DB::table('tintuc')->where('matintuc', $id)->first();

            if (!$news) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tin tức'
                ], 404);
            }

            // Xóa ảnh nếu có
            if ($news->hinhanh && file_exists(public_path($news->hinhanh))) {
                unlink(public_path($news->hinhanh));
            }

            DB::table('tintuc')->where('matintuc', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa tin tức thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa nhiều tin tức
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'string|exists:tintuc,matintuc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Xóa ảnh của các tin tức
            $news = DB::table('tintuc')->whereIn('matintuc', $request->ids)->get();
            foreach ($news as $item) {
                if ($item->hinhanh && file_exists(public_path($item->hinhanh))) {
                    unlink(public_path($item->hinhanh));
                }
            }

            DB::table('tintuc')->whereIn('matintuc', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công ' . count($request->ids) . ' tin tức'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái tin tức
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'trangthai' => 'required|in:Draft,Published,Archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $news = DB::table('tintuc')->where('matintuc', $id)->first();

            if (!$news) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tin tức'
                ], 404);
            }

            DB::table('tintuc')->where('matintuc', $id)->update([
                'trangthai' => $request->trangthai,
                'ngaycapnhat' => now(),
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
     * Tạo mã tin tức
     */
    private function generateMatintuc()
    {
        do {
            $matintuc = 'TT' . date('Ymd') . rand(1000, 9999);
        } while (DB::table('tintuc')->where('matintuc', $matintuc)->exists());

        return $matintuc;
    }

    /**
     * Tạo excerpt từ nội dung
     */
    private function getExcerpt($content, $length = 150)
    {
        $text = strip_tags($content);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length) . '...';
        }
        
        return $text;
    }
}