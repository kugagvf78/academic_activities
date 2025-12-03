<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventApiController extends Controller
{
    /**
     * API: Lấy danh sách cuộc thi
     * GET /api/events
     */
    public function index(Request $request)
    {
        try {
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
                // Chỉ hiển thị cuộc thi không phải Draft
                ->where('ct.trangthai', '!=', 'Draft');

            // ===== CÁC BỘ LỌC =====

            // Tìm kiếm theo tên hoặc mô tả
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ct.tencuocthi', 'ILIKE', "%{$search}%")
                        ->orWhere('ct.mota', 'ILIKE', "%{$search}%");
                });
            }

            // Lọc theo trạng thái thời gian
            if ($request->filled('status')) {
                $status = $request->status;
                $now = Carbon::now();

                switch ($status) {
                    case 'upcoming': // Sắp diễn ra
                        $query->where('ct.thoigianbatdau', '>', $now);
                        break;
                    case 'ongoing': // Đang diễn ra
                        $query->where('ct.thoigianbatdau', '<=', $now)
                            ->where('ct.thoigianketthuc', '>=', $now);
                        break;
                    case 'ended': // Đã kết thúc
                        $query->where('ct.thoigianketthuc', '<', $now);
                        break;
                }
            }

            // Lọc theo loại cuộc thi
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

            // Lọc theo hình thức tham gia
            if ($request->filled('hinhthuc')) {
                $query->where('ct.hinhthucthamgia', $request->hinhthuc);
            }

            // ===== SẮP XẾP =====
            $sortBy = $request->get('sort_by', 'priority'); // Mặc định sắp xếp theo ưu tiên
            $sortOrder = $request->get('sort_order', 'asc');

            if ($sortBy === 'priority') {
                // Sắp xếp: Sắp diễn ra -> Đang diễn ra -> Đã kết thúc
                $query->orderByRaw("
                    CASE 
                        WHEN ct.thoigianbatdau > NOW() THEN 1
                        WHEN ct.thoigianbatdau <= NOW() AND ct.thoigianketthuc >= NOW() THEN 2
                        ELSE 3
                    END
                ")
                    ->orderBy('ct.thoigianbatdau', 'desc');
            } else {
                $query->orderBy("ct.{$sortBy}", $sortOrder);
            }

            // ===== PHÂN TRANG =====
            $perPage = $request->get('per_page', 10); // Mặc định 10 items/page
            $events = $query->paginate($perPage);

            // ===== TRANSFORM DATA =====
            $events->getCollection()->transform(function ($event) {
                return [
                    'macuocthi' => $event->macuocthi,
                    'tencuocthi' => $event->tencuocthi,
                    'loaicuocthi' => $event->loaicuocthi,
                    'mota' => $event->mota,
                    'mucdich' => $event->mucdich,
                    'doituongthamgia' => $event->doituongthamgia,
                    'thoigianbatdau' => $event->thoigianbatdau,
                    'thoigianketthuc' => $event->thoigianketthuc,
                    'diadiem' => $event->diadiem,
                    'soluongthanhvien' => $event->soluongthanhvien,
                    'hinhthucthamgia' => $event->hinhthucthamgia,
                    'trangthai' => $event->trangthai,
                    'tenbomon' => $event->tenbomon,
                    'soluongdangky' => $event->soluongdangky,

                    // Thông tin bổ sung
                    'status_label' => $this->getStatusLabel($event),
                    'status_color' => $this->getStatusColor($event),
                    'slug' => $this->generateSlug($event->tencuocthi, $event->macuocthi),
                    'days_remaining' => $this->getDaysRemaining($event),
                    'prize_display' => $this->formatPrize($event->dutrukinhphi),
                    'can_register' => $this->canRegister($event),
                ];
            });

            // ===== TRẢ VỀ RESPONSE =====
            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách cuộc thi thành công',
                'data' => $events->items(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                    'last_page' => $events->lastPage(),
                    'from' => $events->firstItem(),
                    'to' => $events->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách cuộc thi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Lấy chi tiết cuộc thi
     * GET /api/events/{macuocthi}
     */
    public function show($macuocthi)
{
    try {
        $event = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->leftJoin('giangvien as gv', 'bm.matruongbomon', '=', 'gv.magiangvien')
            ->leftJoin('nguoidung as nd', 'gv.manguoidung', '=', 'nd.manguoidung')
            ->where('ct.macuocthi', $macuocthi)
            ->where('ct.trangthai', '!=', 'Draft')
            ->select(
                'ct.*',
                'bm.tenbomon',
                'bm.mota as motabomon',
                'nd.hoten as truongbomon',
                DB::raw('(
                    (SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                    (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)
                ) as soluongdangky'),
                DB::raw('(SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi) as soluongdoi')
            )
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc thi'
            ], 404);
        }

        // ===== LẤY DANH SÁCH VÒNG THI =====
        $vongthi = DB::table('vongthi')
            ->where('macuocthi', $macuocthi)
            ->orderBy('thutu')
            ->get();

        // ===== LẤY KẾ HOẠCH CUỘC THI =====
        $kehoach = DB::table('kehoachcuocthi as kh')
            ->leftJoin('nguoidung as nd', 'kh.nguoiduyet', '=', 'nd.tendangnhap')
            ->where('kh.makehoach', $event->makehoach)
            ->select('kh.*', 'nd.hoten as tennguoiduyet')
            ->first();

        // ===== LẤY BAN TỔ CHỨC =====
        $bantochuc = DB::table('ban as b')
            ->leftJoin('phanconggiangvien as pc', 'b.maban', '=', 'pc.maban')
            ->where('b.macuocthi', $macuocthi)
            ->select(
                'b.tenban',
                'b.mota as motaban',
                DB::raw('COUNT(pc.maphanCong) as sothanhvien')
            )
            ->groupBy('b.maban', 'b.tenban', 'b.mota')
            ->get();

        // ===== LẤY HOẠT ĐỘNG HỖ TRỢ =====
        $hotro = DB::table('hoatdonghotro')
            ->where('macuocthi', $macuocthi)
            ->where('loaihoatdong', 'HoTroKyThuat')
            ->where('thoigianketthuc', '>', now())
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        // ===== LẤY HOẠT ĐỘNG CỔ VŨ =====
        $colvu = DB::table('hoatdonghotro')
            ->where('macuocthi', $macuocthi)
            ->where('loaihoatdong', 'CoVu')
            ->where('thoigianketthuc', '>', now())
            ->orderBy('thoigianbatdau', 'asc')
            ->get();

        // ===== CHUẨN BỊ DATA TRẢ VỀ =====
        $eventData = [
            'macuocthi' => $event->macuocthi,
            'tencuocthi' => $event->tencuocthi,
            'loaicuocthi' => $event->loaicuocthi,
            'mota' => $event->mota,
            'mucdich' => $event->mucdich,
            'doituongthamgia' => $event->doituongthamgia,
            'thoigianbatdau' => $event->thoigianbatdau,
            'thoigianketthuc' => $event->thoigianketthuc,
            'diadiem' => $event->diadiem,
            'soluongthanhvien' => $event->soluongthanhvien,
            'hinhthucthamgia' => $event->hinhthucthamgia,
            'trangthai' => $event->trangthai,
            'dutrukinhphi' => $event->dutrukinhphi,
            'tenbomon' => $event->tenbomon,
            'motabomon' => $event->motabomon,
            'truongbomon' => $event->truongbomon,
            'soluongdangky' => $event->soluongdangky,
            'soluongdoi' => $event->soluongdoi,

            'status_label' => $this->getStatusLabel($event),
            'status_color' => $this->getStatusColor($event),
            'slug' => $this->generateSlug($event->tencuocthi, $event->macuocthi),
            'days_remaining' => $this->getDaysRemaining($event),
            'prize_display' => $this->formatPrize($event->dutrukinhphi),
            'can_register' => $this->canRegister($event),

            'vongthi' => $vongthi,
            'kehoach' => $kehoach,
            'bantochuc' => $bantochuc,

            // 🔥 QUAN TRỌNG — THÊM 2 TRƯỜNG MỚI
            'hotro' => $hotro,
            'colvu' => $colvu,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết cuộc thi thành công',
            'data' => $eventData
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy chi tiết cuộc thi',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * API: Lấy danh sách loại cuộc thi
     * GET /api/events/categories
     */
    public function categories()
    {
        try {
            $categories = DB::table('cuocthi')
                ->select('loaicuocthi')
                ->distinct()
                ->whereNotNull('loaicuocthi')
                ->where('trangthai', '!=', 'Draft')
                ->pluck('loaicuocthi');

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách loại cuộc thi thành công',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Lấy thống kê tổng quan
     * GET /api/events/statistics
     */
    public function statistics()
    {
        try {
            // Tổng số cuộc thi (không bao gồm Draft)
            $totalEvents = DB::table('cuocthi')
                ->where('trangthai', '!=', 'Draft')
                ->count();

            // Tổng sinh viên tham gia
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
                $totalStudents = 0;
            }

            // Tổng giải thưởng
            $totalPrizes = DB::table('datgiai')
                ->join('cuocthi', 'datgiai.macuocthi', '=', 'cuocthi.macuocthi')
                ->where('cuocthi.trangthai', '!=', 'Draft')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Lấy thống kê thành công',
                'data' => [
                    'total_events' => $totalEvents,
                    'total_students' => $totalStudents,
                    'total_prizes' => $totalPrizes,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ===== HELPER METHODS =====

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

    private function getStatusColor($event)
    {
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);
        $end = Carbon::parse($event->thoigianketthuc);

        if ($now->lt($start)) {
            return 'yellow';
        } elseif ($now->between($start, $end)) {
            return 'green';
        } else {
            return 'gray';
        }
    }

    private function generateSlug($tencuocthi, $macuocthi)
    {
        $slug = $this->removeVietnameseTones($tencuocthi);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug . '-' . $macuocthi;
    }

    private function removeVietnameseTones($str)
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        return $str;
    }

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

    private function formatPrize($amount)
    {
        if (!$amount) return 'Chưa công bố';

        if ($amount >= 1000000) {
            return number_format($amount / 1000000, 0) . 'M';
        }

        return number_format($amount) . 'đ';
    }

    private function canRegister($event)
    {
        $now = Carbon::now();
        $start = Carbon::parse($event->thoigianbatdau);

        return $now->lt($start) &&
            in_array($event->trangthai, ['Approved', 'InProgress']) &&
            !empty($event->hinhthucthamgia);
    }

    /* ============================================================
|  API ĐĂNG KÝ DỰ THI (CÁ NHÂN / ĐỘI)
|  POST /api/events/register
============================================================ */
    public function register(Request $request)
    {
        $request->validate([
            'macuocthi' => 'required',
            'loaidangky' => 'required|in:CaNhan,DoiNhom',
            'masinhvien' => 'required_if:loaidangky,CaNhan',
            'madoithi' => 'required_if:loaidangky,DoiNhom',
        ]);

        $event = DB::table('cuocthi')
            ->where('macuocthi', $request->macuocthi)
            ->first();

        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy cuộc thi'], 404);
        }

        // Không cho đăng ký khi đã bắt đầu
        if (!now()->lt($event->thoigianbatdau)) {
            return response()->json(['success' => false, 'message' => 'Cuộc thi đã bắt đầu, không thể đăng ký'], 400);
        }

        // ====================== ĐĂNG KÝ CÁ NHÂN ======================
        if ($request->loaidangky === 'CaNhan') {

            // Kiểm tra trùng đăng ký
            $exists = DB::table('dangkycanhan')
                ->where('macuocthi', $request->macuocthi)
                ->where('masinhvien', $request->masinhvien)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký cuộc thi này'], 400);
            }

            // Tạo mã
            $madk = 'DKCN' . strtoupper(uniqid());

            DB::table('dangkycanhan')->insert([
                'madangkycanhan' => $madk,
                'macuocthi' => $request->macuocthi,
                'masinhvien' => $request->masinhvien,
                'ngaydangky' => now(),
                'trangthai' => 'Registered'
            ]);

            return response()->json(['success' => true, 'message' => 'Đăng ký dự thi thành công']);
        }

        // ====================== ĐĂNG KÝ ĐỘI ======================
        if ($request->loaidangky === 'DoiNhom') {

            $exists = DB::table('dangkydoithi')
                ->where('macuocthi', $request->macuocthi)
                ->where('madoithi', $request->madoithi)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Đội đã đăng ký cuộc thi này'], 400);
            }

            $madk = 'DKDT' . strtoupper(uniqid());

            DB::table('dangkydoithi')->insert([
                'madangkydoi' => $madk,
                'macuocthi' => $request->macuocthi,
                'madoithi' => $request->madoithi,
                'ngaydangky' => now(),
                'trangthai' => 'Registered'
            ]);

            return response()->json(['success' => true, 'message' => 'Đăng ký đội thi thành công']);
        }
    }

    /* ============================================================
|  API: ĐĂNG KÝ HỖ TRỢ
|  POST /api/events/support
============================================================ */
    public function support(Request $request)
    {
        $request->validate([
            'macuocthi' => 'required',
            'masinhvien' => 'required',
            'mahoatdong' => 'required'
        ]);

        // Kiểm tra hoạt động có thật không
        $hoatdong = DB::table('hoatdonghotro')
            ->where('mahoatdong', $request->mahoatdong)
            ->where('macuocthi', $request->macuocthi)
            ->first();

        if (!$hoatdong) {
            return response()->json(['success' => false, 'message' => 'Hoạt động không hợp lệ'], 400);
        }

        // Không cho đăng ký khi hết hạn
        if (now()->gt($hoatdong->thoigianketthuc)) {
            return response()->json(['success' => false, 'message' => 'Hoạt động đã kết thúc'], 400);
        }

        // Kiểm tra trùng đăng ký
        $exists = DB::table('dangkyhoatdong')
            ->where('mahoatdong', $request->mahoatdong)
            ->where('masinhvien', $request->masinhvien)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký hoạt động này'], 400);
        }

        $maDK = 'DKHD' . strtoupper(uniqid());

        DB::table('dangkyhoatdong')->insert([
            'madangkyhoatdong' => $maDK,
            'mahoatdong' => $request->mahoatdong,
            'masinhvien' => $request->masinhvien,
            'macuocthi' => $request->macuocthi,
            'ngaydangky' => now(),
            'trangthai' => 'Registered'
        ]);

        return response()->json(['success' => true, 'message' => 'Đăng ký hỗ trợ thành công']);
    }

    /* ============================================================
|  API: ĐĂNG KÝ CỔ VŨ
|  POST /api/events/cheer
============================================================ */
    public function cheer(Request $request)
    {
        $request->validate([
            'macuocthi' => 'required',
            'masinhvien' => 'required',
            'mahoatdong' => 'required'
        ]);

        // Lấy hoạt động cổ vũ
        $hoatdong = DB::table('hoatdonghotro')
            ->where('mahoatdong', $request->mahoatdong)
            ->where('loaihoatdong', 'CoVu')
            ->first();

        if (!$hoatdong) {
            return response()->json(['success' => false, 'message' => 'Hoạt động không hợp lệ'], 400);
        }

        // Kiểm tra hết hạn
        if (now()->gt($hoatdong->thoigianketthuc)) {
            return response()->json(['success' => false, 'message' => 'Hoạt động đã kết thúc'], 400);
        }

        // Check trùng đăng ký
        $exists = DB::table('dangkyhoatdong')
            ->where('mahoatdong', $request->mahoatdong)
            ->where('masinhvien', $request->masinhvien)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký cổ vũ rồi'], 400);
        }

        $ma = 'DKCV' . strtoupper(uniqid());

        DB::table('dangkyhoatdong')->insert([
            'madangkyhoatdong' => $ma,
            'mahoatdong' => $request->mahoatdong,
            'masinhvien' => $request->masinhvien,
            'macuocthi' => $request->macuocthi,
            'ngaydangky' => now(),
            'trangthai' => 'Registered'
        ]);

        return response()->json(['success' => true, 'message' => 'Đăng ký cổ vũ thành công']);
    }
}
