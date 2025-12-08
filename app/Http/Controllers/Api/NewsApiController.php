<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsApiController extends Controller
{
    /**
     * GET /api/news
     * Lấy danh sách tin tức với phân trang
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('tintuc as tt')
                ->leftJoin('cuocthi as ct', 'tt.macuocthi', '=', 'ct.macuocthi')
                ->select(
                    'tt.matintuc',
                    'tt.tieude',
                    'tt.noidung',
                    'tt.macuocthi',
                    'tt.loaitin',
                    'tt.tacgia',
                    'tt.luotxem',
                    'tt.trangthai',
                    'tt.ngaydang',
                    'tt.hinhanh',
                    'ct.tencuocthi'
                )
                ->where('tt.trangthai', 'Published');

            // Tìm kiếm theo tiêu đề hoặc nội dung
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('tt.tieude', 'ILIKE', "%{$search}%")
                      ->orWhere('tt.noidung', 'ILIKE', "%{$search}%");
                });
            }

            // Lọc theo loại tin
            if ($request->filled('category') && $request->category !== 'all') {
                $categoryMap = [
                    'contest' => 'TinTuc',
                    'announcement' => 'ThongBao',
                    'seminar' => 'SuKien',
                ];
                
                $category = $categoryMap[$request->category] ?? $request->category;
                $query->where('tt.loaitin', $category);
            }

            // Sắp xếp
            $sort = $request->get('sort', 'newest');
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('tt.ngaydang', 'asc');
                    break;
                case 'popular':
                    $query->orderBy('tt.luotxem', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('tt.ngaydang', 'desc');
                    break;
            }

            // Phân trang
            $perPage = $request->get('per_page', 10);
            $news = $query->paginate($perPage);

            // Transform data
            $news->getCollection()->transform(function ($item) {
                return $this->transformNews($item);
            });

            // Lấy tin tức nổi bật (xem nhiều nhất)
            $featured = DB::table('tintuc')
                ->where('trangthai', 'Published')
                ->orderBy('luotxem', 'desc')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    return $this->transformNews($item);
                });

            // Thống kê
            $stats = [
                'total' => DB::table('tintuc')->where('trangthai', 'Published')->count(),
                'this_month' => DB::table('tintuc')
                    ->where('trangthai', 'Published')
                    ->whereMonth('ngaydang', date('m'))
                    ->whereYear('ngaydang', date('Y'))
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'news' => $news,
                'featured' => $featured,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách tin tức',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/news/{slug}
     * Lấy chi tiết tin tức theo slug
     */
    public function show($slug)
    {
        try {
            // Parse slug để lấy matintuc
            $matintuc = $this->getIdFromSlug($slug);

            $news = DB::table('tintuc as tt')
                ->leftJoin('cuocthi as ct', 'tt.macuocthi', '=', 'ct.macuocthi')
                ->where('tt.matintuc', $matintuc)
                ->select(
                    'tt.*',
                    'ct.tencuocthi',
                    'ct.thoigianbatdau',
                    'ct.thoigianketthuc'
                )
                ->first();

            if (!$news || $news->trangthai !== 'Published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tin tức'
                ], 404);
            }

            // Tăng lượt xem
            DB::table('tintuc')
                ->where('matintuc', $matintuc)
                ->increment('luotxem');

            // Transform data
            $news = $this->transformNews($news, true);

            // Tin tức liên quan
            $related = DB::table('tintuc')
                ->where('matintuc', '!=', $matintuc)
                ->where('trangthai', 'Published')
                ->where(function($query) use ($news) {
                    $query->where('loaitin', $news->loaitin)
                          ->orWhere('macuocthi', $news->macuocthi);
                })
                ->orderBy('ngaydang', 'desc')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    return $this->transformNews($item);
                });

            return response()->json([
                'success' => true,
                'news' => $news,
                'related' => $related,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết tin tức',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform news data cho API response
     */
    private function transformNews($item, $isDetail = false)
    {
        $transformed = (object)[
            'matintuc' => $item->matintuc,
            'tieude' => $item->tieude,
            'noidung' => $item->noidung,
            'macuocthi' => $item->macuocthi,
            'loaitin' => $item->loaitin,
            'tacgia' => $item->tacgia,
            'luotxem' => $item->luotxem,
            'trangthai' => $item->trangthai,
            'ngaydang' => Carbon::parse($item->ngaydang)->format('d/m/Y H:i'),
            'hinhanh' => $item->hinhanh ?? null,
            'tencuocthi' => $item->tencuocthi ?? null,
            'slug' => $this->generateSlug($item->tieude, $item->matintuc),
        ];

        // Nếu không phải detail, thêm excerpt
        if (!$isDetail) {
            $transformed->excerpt = $this->getExcerpt($item->noidung);
        }

        // Thêm thông tin cuộc thi nếu là detail
        if ($isDetail && isset($item->thoigianbatdau)) {
            $transformed->thoigianbatdau = $item->thoigianbatdau;
            $transformed->thoigianketthuc = $item->thoigianketthuc;
        }

        return $transformed;
    }

    /**
     * Tạo slug từ tiêu đề
     */
    private function generateSlug($tieude, $matintuc)
    {
        // Loại bỏ dấu tiếng Việt
        $slug = $this->removeVietnameseTones($tieude);
        
        // Chuyển thành lowercase và thay khoảng trắng bằng dấu gạch ngang
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Giới hạn độ dài và thêm mã tin tức
        $slug = substr($slug, 0, 100);
        return $slug . '-' . $matintuc;
    }

    /**
     * Lấy ID từ slug
     */
    private function getIdFromSlug($slug)
    {
        $parts = explode('-', $slug);
        return end($parts);
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    private function removeVietnameseTones($str)
    {
        $unicode = [
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        
        foreach($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        
        return $str;
    }

    /**
     * Tạo excerpt từ nội dung
     */
    private function getExcerpt($content, $length = 150)
    {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Trim whitespace
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        // Cut to length
        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length) . '...';
        }
        
        return $text;
    }
}