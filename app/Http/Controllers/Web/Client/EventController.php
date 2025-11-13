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
                DB::raw('(SELECT COUNT(*) FROM dangkyduthi WHERE macuocthi = ct.macuocthi) as soluongdangky')
            );

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
                    // Sắp diễn ra
                    $query->where('ct.thoigianbatdau', '>', $now);
                    break;
                case 'ongoing':
                    // Đang diễn ra
                    $query->where('ct.thoigianbatdau', '<=', $now)
                          ->where('ct.thoigianketthuc', '>=', $now);
                    break;
                case 'ended':
                    // Đã kết thúc
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

        // Transform data để thêm các thuộc tính cần thiết
        $events->getCollection()->transform(function ($event) {
            $event->status_label = $this->getStatusLabel($event);
            $event->status_color = $this->getStatusColor($event);
            $event->slug = $this->generateSlug($event->tencuocthi, $event->macuocthi);
            $event->days_remaining = $this->getDaysRemaining($event);
            $event->prize_display = $this->formatPrize($event->dutrukinhphi);
            
            return $event;
        });

        // Lấy danh sách loại cuộc thi để hiển thị trong filter
        $categories = DB::table('cuocthi')
            ->select('loaicuocthi')
            ->distinct()
            ->whereNotNull('loaicuocthi')
            ->pluck('loaicuocthi');

        return view('client.events.index', compact('events', 'categories'));
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
                DB::raw('(SELECT COUNT(*) FROM dangkyduthi WHERE macuocthi = ct.macuocthi) as soluongdangky'),
                DB::raw('(SELECT COUNT(DISTINCT madoithi) FROM dangkyduthi WHERE macuocthi = ct.macuocthi AND madoithi IS NOT NULL) as soluongdoi')
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
            return 'Đang mở đăng ký';
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
        $end = Carbon::parse($event->thoigianketthuc);

        // Chỉ cho đăng ký khi cuộc thi chưa bắt đầu hoặc đang diễn ra
        // và trạng thái là Approved hoặc InProgress
        return $now->lte($end) && 
               in_array($event->trangthai, ['Approved', 'InProgress']);
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
            return redirect()
                ->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi này hiện không nhận đăng ký');
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

        // Lấy danh sách hoạt động cổ vũ
        $hoatdong = DB::table('hoatdonghotro')
            ->where('macuocthi', $macuocthi)
            ->where('loaihoatdong', 'CoVu')
            ->get();

        return view('client.events.cheer', compact('event', 'slug', 'hoatdong'));
    }

    /**
     * Hiển thị form đăng ký hỗ trợ
     */
    public function showSupportForm($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);

        $event = DB::table('cuocthi')
            ->where('macuocthi', $macuocthi)
            ->first();

        if (!$event) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Lấy danh sách hoạt động hỗ trợ
        $hoatdong = DB::table('hoatdonghotro')
            ->where('macuocthi', $macuocthi)
            ->whereIn('loaihoatdong', ['HoTroKyThuat', 'ToChuc'])
            ->get();

        return view('client.events.support', compact('event', 'slug', 'hoatdong'));
    }
}