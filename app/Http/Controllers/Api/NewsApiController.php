<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsApiController extends Controller
{
    /**
     * API: Danh sách tin tức
     */
    public function index(Request $request)
    {
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
                'ct.tencuocthi'
            )
            ->where('tt.trangthai', 'Published');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tt.tieude', 'ILIKE', "%{$search}%")
                  ->orWhere('tt.noidung', 'ILIKE', "%{$search}%");
            });
        }

        // Lọc theo loại
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
            default:
                $query->orderBy('tt.ngaydang', 'desc');
        }

        // Phân trang
        $news = $query->paginate(9)->appends($request->query());

        // Format dữ liệu
        $news->getCollection()->transform(function ($item) {
            $item->date = Carbon::parse($item->ngaydang)->format('d/m/Y');
            $item->date_full = Carbon::parse($item->ngaydang)->format('d/m/Y H:i');
            $item->time_ago = Carbon::parse($item->ngaydang)->diffForHumans();
            $item->category = $this->getCategoryLabel($item->loaitin);
            $item->category_color = $this->getCategoryColor($item->loaitin);
            $item->excerpt = $this->getExcerpt($item->noidung);
            $item->slug = $this->generateSlug($item->tieude, $item->matintuc);
            return $item;
        });

        // Tin nổi bật
        $featured = DB::table('tintuc')
            ->where('trangthai', 'Published')
            ->orderBy('luotxem', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                $item->date = Carbon::parse($item->ngaydang)->format('d/m/Y');
                $item->slug = $this->generateSlug($item->tieude, $item->matintuc);
                return $item;
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
            'stats' => $stats
        ]);
    }

    /**
     * API: Chi tiết tin tức
     */
    public function show($slug)
    {
        // Tách ID từ slug
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
        DB::table('tintuc')->where('matintuc', $matintuc)->increment('luotxem');

        // Format
        $news->date = Carbon::parse($news->ngaydang)->format('d/m/Y');
        $news->date_full = Carbon::parse($news->ngaydang)->format('d/m/Y H:i');
        $news->time_ago = Carbon::parse($news->ngaydang)->diffForHumans();
        $news->category = $this->getCategoryLabel($news->loaitin);
        $news->category_color = $this->getCategoryColor($news->loaitin);
        $news->slug = $slug;

        // Tin liên quan
        $related = DB::table('tintuc')
            ->where('matintuc', '!=', $matintuc)
            ->where('trangthai', 'Published')
            ->where(function($q) use ($news) {
                $q->where('loaitin', $news->loaitin)
                  ->orWhere('macuocthi', $news->macuocthi);
            })
            ->orderBy('ngaydang', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                $item->date = Carbon::parse($item->ngaydang)->format('d/m/Y');
                $item->excerpt = $this->getExcerpt($item->noidung);
                $item->slug = $this->generateSlug($item->tieude, $item->matintuc);
                return $item;
            });

        return response()->json([
            'success' => true,
            'news' => $news,
            'related' => $related
        ]);
    }

    // ================================================================
    // 🎯 Các hàm helper giữ nguyên 100% như Web Controller
    // ================================================================

    private function getCategoryLabel($loaitin)
    {
        $labels = [
            'TinTuc' => 'Tin tức',
            'ThongBao' => 'Thông báo',
            'SuKien' => 'Sự kiện',
        ];

        return $labels[$loaitin] ?? $loaitin;
    }

    private function getCategoryColor($loaitin)
    {
        $colors = [
            'TinTuc' => 'blue',
            'ThongBao' => 'red',
            'SuKien' => 'green',
        ];

        return $colors[$loaitin] ?? 'gray';
    }

    private function generateSlug($tieude, $matintuc)
    {
        $slug = $this->removeVietnameseTones($tieude);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 100);
        return $slug . '-' . $matintuc;
    }

    private function getIdFromSlug($slug)
    {
        $parts = explode('-', $slug);
        return end($parts);
    }

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

        foreach ($unicode as $ascii => $signs) {
            $str = preg_replace("/($signs)/i", $ascii, $str);
        }

        return $str;
    }

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
