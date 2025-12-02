<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Hiển thị danh sách cuộc thi
     */
    public function index(Request $request)
    {
        // ===== TÍNH TOÁN THỐNG KÊ CHO HERO SECTION =====
        // FIXED: Chỉ đếm cuộc thi không phải Draft
        $totalEvents = DB::table('cuocthi')
            ->where('trangthai', '!=', 'Draft')
            ->count();
        
        // Đếm tổng sinh viên tham gia từ cả 2 bảng (loại bỏ trùng lặp)
        try {
            $totalStudents = DB::table('dangkycanhan')
                ->join('cuocthi', 'dangkycanhan.macuocthi', '=', 'cuocthi.macuocthi')
                ->where('cuocthi.trangthai', '!=', 'Draft')
                ->select('dangkycanhan.masinhvien')
                ->union(
                    DB::table('dangkydoithi')
                        ->join('cuocthi', 'dangkydoithi.macuocthi', '=', 'cuocthi.macuocthi')
                        ->join('doithi', 'dangkydoithi.madoithi', '=', 'doithi.madoithi')
                        ->join('thanhviendoithi', 'doithi.madoithi', '=', 'thanhviendoithi.madoithi')
                        ->where('cuocthi.trangthai', '!=', 'Draft')
                        ->select('thanhviendoithi.masinhvien')
                )
                ->distinct()
                ->count();
        } catch (\Exception $e) {
            // Cách 2: Nếu không có bảng thanhviendoithi
            $totalStudents = DB::table('dangkycanhan')
                ->join('cuocthi', 'dangkycanhan.macuocthi', '=', 'cuocthi.macuocthi')
                ->where('cuocthi.trangthai', '!=', 'Draft')
                ->distinct('dangkycanhan.masinhvien')
                ->count() + 
                DB::table('dangkydoithi')
                    ->join('cuocthi', 'dangkydoithi.macuocthi', '=', 'cuocthi.macuocthi')
                    ->where('cuocthi.trangthai', '!=', 'Draft')
                    ->distinct('dangkydoithi.masinhvien')
                    ->count();
        }
        
        // FIXED: Chỉ đếm giải thưởng của cuộc thi không phải Draft
        $totalPrizes = DB::table('datgiai')
            ->join('cuocthi', 'datgiai.macuocthi', '=', 'cuocthi.macuocthi')
            ->where('cuocthi.trangthai', '!=', 'Draft')
            ->count();
        
        // ===== QUERY DANH SÁCH CUỘC THI =====
        $query = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->select(
                'ct.macuocthi',
                'ct.tencuocthi',
                'ct.loaicuocthi',
                'ct.mota',
                'ct.mucdich',
                'ct.doituongthamgia',
                'ct.thoigianbatdau',
                'ct.thoigianketthuc',
                'ct.diadiem',
                'ct.soluongthanhvien',
                'ct.hinhthucthamgia',
                'ct.trangthai',
                'ct.dutrukinhphi',
                'bm.tenbomon',
                DB::raw('(
                    (SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                    (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)
                ) as soluongdangky')
            )
            // ✅ THÊM ĐIỀU KIỆN NÀY: Chỉ hiển thị cuộc thi không phải Draft
            ->where('ct.trangthai', '!=', 'Draft');

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ct.tencuocthi', 'ILIKE', "%{$search}%")
                ->orWhere('ct.mota', 'ILIKE', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->status;
            $now = Carbon::now();

            switch ($status) {
                case 'upcoming':
                    $query->where('ct.thoigianbatdau', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('ct.thoigianbatdau', '<=', $now)
                        ->where('ct.thoigianketthuc', '>=', $now);
                    break;
                case 'ended':
                    $query->where('ct.thoigianketthuc', '<', $now);
                    break;
            }
        }

        // Lọc theo loại cuộc thi (category)
        if ($request->filled('category')) {
            $query->where('ct.loaicuocthi', $request->category);
        }

        // Lọc theo ngày bắt đầu
        if ($request->filled('from_date')) {
            $query->whereDate('ct.thoigianbatdau', '>=', $request->from_date);
        }

        // Lọc theo ngày kết thúc
        if ($request->filled('to_date')) {
            $query->whereDate('ct.thoigianketthuc', '<=', $request->to_date);
        }

        // Sắp xếp: Cuộc thi sắp diễn ra lên đầu
        $query->orderByRaw("
            CASE 
                WHEN ct.thoigianbatdau > NOW() THEN 1
                WHEN ct.thoigianbatdau <= NOW() AND ct.thoigianketthuc >= NOW() THEN 2
                ELSE 3
            END
        ")
        ->orderBy('ct.thoigianbatdau', 'desc');

        // Phân trang
        $perPage = 6;
        $events = $query->paginate($perPage)->appends($request->query());

        // Transform data
        $events->getCollection()->transform(function ($event) {
            $event->status_label = $this->getStatusLabel($event);
            $event->status_color = $this->getStatusColor($event);
            $event->slug = $this->generateSlug($event->tencuocthi, $event->macuocthi);
            $event->days_remaining = $this->getDaysRemaining($event);
            $event->prize_display = $this->formatPrize($event->dutrukinhphi);
            
            return $event;
        });

        // Lấy danh sách loại cuộc thi (không bao gồm Draft)
        $categories = DB::table('cuocthi')
            ->select('loaicuocthi')
            ->distinct()
            ->whereNotNull('loaicuocthi')
            ->where('trangthai', '!=', 'Draft')
            ->pluck('loaicuocthi');

        return view('client.events.index', compact('events', 'categories', 'totalEvents', 'totalStudents', 'totalPrizes'));
    }

    /**
     * Hiển thị chi tiết cuộc thi
     */
    public function show($slug)
    {
        // Parse slug để lấy macuocthi
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $event = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv', 'bm.matruongbomon', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('ct.macuocthi', $macuocthi)
            ->select(
                'ct.*',
                'bm.tenbomon',
                'bm.mota as motabomon',
                'nd.hoten as truongbomon',
                // FIXED: Tính tổng đăng ký từ cả 2 bảng
                DB::raw('(
                    (SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                    (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)
                ) as soluongdangky'),
                // FIXED: Đếm số đội từ bảng dangkydoithi
                DB::raw('(SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi) as soluongdoi')
            )
            ->first();

        if (!$event) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Lấy danh sách vòng thi
        $vongthi = DB::table('vongthi')
            ->where('macuocthi', $macuocthi)
            ->orderBy('thutu')
            ->get();

        // Lấy kế hoạch cuộc thi
        $kehoach = DB::table('kehoachcuocthi as kh')
            ->leftJoin('nguoidung as nd', 'kh.nguoiduyet', '=', 'nd.tendangnhap')
            ->where('kh.macuocthi', $macuocthi)
            ->select('kh.*', 'nd.hoten as tennguoiduyet')
            ->first();

        // Lấy danh sách ban tổ chức
        $bantochuc = DB::table('ban as b')
            ->leftJoin('phanconggiangvien as pc', 'b.maban', '=', 'pc.maban')
            ->leftJoin('giangvien as gv', 'pc.magiangvien', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('b.macuocthi', $macuocthi)
            ->select(
                'b.tenban',
                'b.mota as motaban',
                DB::raw('COUNT(pc.maphanCong) as sothanhvien')
            )
            ->groupBy('b.maban', 'b.tenban', 'b.mota')
            ->get();

        // Thêm các thuộc tính bổ sung
        $event->status_label = $this->getStatusLabel($event);
        $event->status_color = $this->getStatusColor($event);
        $event->slug = $slug;
        $event->days_remaining = $this->getDaysRemaining($event);
        $event->prize_display = $this->formatPrize($event->dutrukinhphi);
        $event->can_register = $this->canRegister($event);

        return view('client.events.show', compact('event', 'vongthi', 'kehoach', 'bantochuc'));
    }

    /**
     * Lấy nhãn trạng thái
     */
    private function getStatusLabel($event)
    {
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        $end = Carbon::parse($event->thoigianketthuc);

        if ($now->lt($start)) {
            return 'Sắp diễn ra';
        } elseif ($now->between($start, $end)) {
            return 'Đang diễn ra';
        } else {
            return 'Đã kết thúc';
        }
    }

    /**
     * Lấy màu trạng thái
     */
    private function getStatusColor($event)
    {
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        $end = Carbon::parse($event->thoigianketthuc);

        if ($now->lt($start)) {
            return 'yellow'; // Sắp diễn ra
        } elseif ($now->between($start, $end)) {
            return 'green'; // Đang diễn ra
        } else {
            return 'gray'; // Đã kết thúc
        }
    }

    /**
     * Tạo slug từ tên cuộc thi
     */
    private function generateSlug($tencuocthi, $macuocthi)
    {
        // Loại bỏ dấu tiếng Việt
        $slug = $this->removeVietnameseTones($tencuocthi);
        
        // Chuyển thành lowercase và thay khoảng trắng bằng dấu gạch ngang
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Thêm mã cuộc thi vào cuối
        return $slug . '-' . $macuocthi;
    }

    /**
     * Lấy mã cuộc thi từ slug
     */
    private function getMaCuocThiFromSlug($slug)
    {
        // Lấy phần cuối cùng sau dấu gạch ngang cuối cùng
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
     * Tính số ngày còn lại
     */
    private function getDaysRemaining($event)
    {
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        $end = Carbon::parse($event->thoigianketthuc);

        if ($now->lt($start)) {
            return $start->diffInDays($now);
        } elseif ($now->between($start, $end)) {
            return $end->diffInDays($now);
        }

        return 0;
    }

    /**
     * Format giải thưởng
     */
    private function formatPrize($amount)
    {
        if (!$amount) return 'Chưa công bố';

        if ($amount >= 1000000) {
            return number_format($amount / 1000000, 0) . 'M';
        }

        return number_format($amount) . 'đ';
    }

    /**
     * Kiểm tra có thể đăng ký không
     */
    private function canRegister($event)
    {
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        
        // CHỈ cho đăng ký khi cuộc thi CHƯA BẮT ĐẦU
        // và trạng thái là Approved và có hình thức tham gia hợp lệ
        return $now->lt($start) && 
            in_array($event->trangthai, ['Approved', 'InProgress']) &&
            !empty($event->hinhthucthamgia);
    }

    /**
     * Hiển thị form đăng ký dự thi
     */
    public function showRegisterForm($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $event = DB::table('cuocthi')
            ->where('macuocthi', $macuocthi)
            ->first();

        if (!$event) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        if (!$this->canRegister($event)) {
            $now = Carbon::now();
            $start = Carbon::parse($event->thoigianbatdau);
            $end = Carbon::parse($event->thoigianketthuc);
            
            // Tùy chỉnh thông báo theo trạng thái
            if ($now->gte($start) && $now->lte($end)) {
                $message = 'Cuộc thi đang diễn ra, không thể đăng ký thêm';
            } elseif ($now->gt($end)) {
                $message = 'Cuộc thi đã kết thúc';
            } else {
                $message = 'Cuộc thi này hiện không nhận đăng ký';
            }
            
            return redirect()
                ->route('client.events.show', $slug)
                ->with('error', $message);
        }

        return view('client.events.register', compact('event', 'slug'));
    }

    /**
     * Hiển thị form đăng ký cổ vũ
     */
    public function showCheerForm($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $event = DB::table('cuocthi')
            ->where('macuocthi', $macuocthi)
            ->first();

        if (!$event) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Kiểm tra có thể đăng ký không
        if (!$this->canRegister($event)) {
            return redirect()
                ->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi này hiện không nhận đăng ký');
        }

        // Lấy danh sách hoạt động cổ vũ
        $hoatdongs = DB::table('hoatdonghotro')
            ->where('macuocthi', $macuocthi)
            ->where('loaihoatdong', 'CoVu')
            ->where('thoigianketthuc', '>=', now())
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        // Thêm slug vào event
        $event->slug = $slug;

        // Alias để phù hợp với blade template
        $cuocthi = $event;

        return view('client.events.cheer', compact('cuocthi', 'hoatdongs', 'slug'));
    }

    /**
     * Hiển thị form đăng ký hỗ trợ
     */
    public function showSupportForm($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $cuocthi = DB::table('cuocthi')
            ->where('macuocthi', $macuocthi)
            ->first();

        if (!$cuocthi) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Kiểm tra trạng thái và thời gian
        if (!$this->canRegister($cuocthi)) {
            return redirect()
                ->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi này hiện không nhận đăng ký');
        }

        // LẤY CẢ 2 LOẠI: ToChuc VÀ HoTroKyThuat
        $hoatdongs = DB::table('hoatdonghotro')
            ->where('macuocthi', $macuocthi)
            ->where('loaihoatdong', 'HoTroKyThuat')
            ->where('thoigianketthuc', '>=', now())
            ->orderBy('loaihoatdong', 'asc')
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        return view('client.events.support', compact('cuocthi', 'hoatdongs', 'slug'));
    }

    public function apiIndex()
    {
        try {
            $events = DB::table('cuocthi as ct')
                ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
                ->select(
                    'ct.macuocthi',
                    'ct.tencuocthi',
                    'ct.loaicuocthi',
                    'ct.mota',
                    'ct.thoigianbatdau',
                    'ct.thoigianketthuc',
                    'ct.trangthai',
                    'bm.tenbomon'
                )
                ->where('ct.trangthai', '!=', 'Draft')
                ->orderBy('ct.thoigianbatdau', 'desc')
                ->get();

            // Thêm thông tin trạng thái & số ngày còn lại
            $events->transform(function ($event) {
                $now = now();
                $start = \Carbon\Carbon::parse($event->thoigianbatdau);
                $end = \Carbon\Carbon::parse($event->thoigianketthuc);

                if ($now->lt($start)) {
                    $event->trangthai_label = 'Sắp diễn ra';
                } elseif ($now->between($start, $end)) {
                    $event->trangthai_label = 'Đang diễn ra';
                } else {
                    $event->trangthai_label = 'Đã kết thúc';
                }

                $event->songayconlai = max(0, $end->diffInDays($now, false) * -1);
                return $event;
            });

            return response()->json([
                'status' => true,
                'data' => $events
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tải danh sách cuộc thi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}