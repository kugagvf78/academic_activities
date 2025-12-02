<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResultApiController extends Controller
{
    /**
     * API: Danh sách kết quả cuộc thi
     */
    public function index(Request $request)
    {
        $query = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('ct.thoigianketthuc', '<', now())
            ->select(
                'ct.macuocthi',
                'ct.tencuocthi',
                'ct.thoigianketthuc',
                'ct.loaicuocthi',
                'ct.hinhthucthamgia',
                'bm.tenbomon',
                DB::raw('((SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                          (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)) as soluongthamgia'),
                DB::raw('(SELECT COUNT(*) FROM datgiai WHERE macuocthi = ct.macuocthi) as soluonggiai'),
                DB::raw("(SELECT 
                            CASE 
                                WHEN dg.loaidangky = 'DoiNhom' THEN dt.tendoithi
                                WHEN dg.loaidangky = 'CaNhan' THEN nd.hoten
                                ELSE 'Chưa công bố'
                            END
                          FROM datgiai dg 
                          LEFT JOIN dangkydoithi dkd ON dg.madangkydoi = dkd.madangkydoi AND dg.loaidangky = 'DoiNhom'
                          LEFT JOIN doithi dt ON dkd.madoithi = dt.madoithi
                          LEFT JOIN dangkycanhan dkc ON dg.madangkydoi = dkc.madangkycanhan AND dg.loaidangky = 'CaNhan'
                          LEFT JOIN sinhvien sv ON dkc.masinhvien = sv.masinhvien
                          LEFT JOIN nguoidung nd ON sv.manguoidung = nd.manguoidung
                          WHERE dg.macuocthi = ct.macuocthi 
                          AND dg.tengiai ILIKE '%nhất%' 
                          LIMIT 1) as nguoithang")
            );

        // Tìm theo tên
        if ($request->filled('search')) {
            $query->where('ct.tencuocthi', 'ILIKE', "%{$request->search}%");
        }

        // Lọc theo năm
        if ($request->filled('year')) {
            $query->whereYear('ct.thoigianketthuc', $request->year);
        }

        // Lọc theo loại tham gia
        if ($request->filled('type')) {
            if ($request->type === 'individual') {
                $query->where('ct.hinhthucthamgia', 'CaNhan');
            } elseif ($request->type === 'team') {
                $query->where('ct.hinhthucthamgia', 'DoiNhom');
            }
        }

        $query->orderBy('ct.thoigianketthuc', 'desc');

        $results = $query->paginate(6);

        // Format lại ngày và thêm winner
        $results->getCollection()->transform(function ($item) {
            $item->date = Carbon::parse($item->thoigianketthuc)->format('d/m/Y');
            $item->year = Carbon::parse($item->thoigianketthuc)->format('Y');
            $item->winner = $item->nguoithang ?? 'Chưa công bố';
            return $item;
        });

        // Lấy danh sách năm
        $years = DB::table('cuocthi')
            ->where('thoigianketthuc', '<', now())
            ->selectRaw('DISTINCT EXTRACT(YEAR FROM thoigianketthuc) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'results' => $results,
            'years' => $years
        ]);
    }

    /**
     * API: Chi tiết kết quả cuộc thi
     */
    public function show($id)
    {
        $result = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('ct.macuocthi', $id)
            ->select(
                'ct.*',
                'bm.tenbomon',
                DB::raw('((SELECT COUNT(*) FROM dangkycanhan WHERE macuocthi = ct.macuocthi) + 
                          (SELECT COUNT(*) FROM dangkydoithi WHERE macuocthi = ct.macuocthi)) as soluongthamgia'),
                DB::raw('(SELECT COUNT(DISTINCT madoithi) FROM dangkydoithi WHERE macuocthi = ct.macuocthi) as soluongdoi')
            )
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc thi.'
            ], 404);
        }

        $result->date = Carbon::parse($result->thoigianketthuc)->format('d/m/Y');

        // Danh sách vòng thi
        $rounds = DB::table('vongthi as vt')
            ->where('vt.macuocthi', $id)
            ->select(
                'vt.tenvongthi',
                'vt.thoigianbatdau',
                'vt.thoigianketthuc',
                DB::raw("(SELECT 
                            CASE 
                                WHEN dg.loaidangky = 'DoiNhom' THEN dt.tendoithi
                                WHEN dg.loaidangky = 'CaNhan' THEN nd.hoten
                                ELSE 'Chưa xác định'
                            END
                          FROM datgiai dg 
                          LEFT JOIN dangkydoithi dkd ON dg.madangkydoi = dkd.madangkydoi AND dg.loaidangky = 'DoiNhom'
                          LEFT JOIN doithi dt ON dkd.madoithi = dt.madoithi
                          LEFT JOIN dangkycanhan dkc ON dg.madangkydoi = dkc.madangkycanhan AND dg.loaidangky = 'CaNhan'
                          LEFT JOIN sinhvien sv ON dkc.masinhvien = sv.masinhvien
                          LEFT JOIN nguoidung nd ON sv.manguoidung = nd.manguoidung
                          WHERE dg.macuocthi = vt.macuocthi
                          AND dg.tengiai ILIKE CONCAT('%', vt.tenvongthi, '%')
                          LIMIT 1) as winner")
            )
            ->orderBy('vt.thutu')
            ->get();

        // Top 3
        $top3 = DB::table('datgiai as dg')
            ->leftJoin('dangkydoithi as dkd', fn($join) =>
                $join->on('dg.madangkydoi', '=', 'dkd.madangkydoi')
                    ->where('dg.loaidangky', '=', 'DoiNhom'))
            ->leftJoin('doithi as dt', 'dkd.madoithi', '=', 'dt.madoithi')
            ->leftJoin('dangkycanhan as dkc', fn($join) =>
                $join->on('dg.madangkydoi', '=', 'dkc.madangkycanhan')
                    ->where('dg.loaidangky', '=', 'CaNhan'))
            ->leftJoin('sinhvien as sv', 'dkc.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dg.macuocthi', $id)
            ->select(
                'dg.tengiai',
                'dg.giaithuong',
                'dg.diemrenluyen',
                'dg.ngaytrao',
                DB::raw("CASE 
                    WHEN dg.loaidangky = 'DoiNhom' THEN dt.tendoithi
                    WHEN dg.loaidangky = 'CaNhan' THEN nd.hoten
                    ELSE 'Chưa xác định'
                END as name"),
                DB::raw("CASE 
                    WHEN dg.tengiai ILIKE '%nhất%' THEN 1
                    WHEN dg.tengiai ILIKE '%nhì%' THEN 2
                    WHEN dg.tengiai ILIKE '%ba%' THEN 3
                    ELSE 4
                END as rank_order")
            )
            ->orderBy('rank_order')
            ->limit(3)
            ->get();

        if ($top3->count() === 0) {
            $top3 = collect([
                ['name' => 'Chưa công bố', 'rank' => 'Giải Nhất', 'prize' => null],
                ['name' => 'Chưa công bố', 'rank' => 'Giải Nhì', 'prize' => null],
                ['name' => 'Chưa công bố', 'rank' => 'Giải Ba', 'prize' => null],
            ]);
        }

        // Tất cả giải thưởng
        $allAwards = DB::table('datgiai as dg')
            ->leftJoin('dangkydoithi as dkd', fn($join) =>
                $join->on('dg.madangkydoi', '=', 'dkd.madangkydoi')
                    ->where('dg.loaidangky', '=', 'DoiNhom'))
            ->leftJoin('doithi as dt', 'dkd.madoithi', '=', 'dt.madoithi')
            ->leftJoin('dangkycanhan as dkc', fn($join) =>
                $join->on('dg.madangkydoi', '=', 'dkc.madangkycanhan')
                    ->where('dg.loaidangky', '=', 'CaNhan'))
            ->leftJoin('sinhvien as sv', 'dkc.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dg.macuocthi', $id)
            ->select(
                'dg.tengiai',
                'dg.giaithuong',
                'dg.diemrenluyen',
                'dg.ngaytrao',
                DB::raw("CASE 
                    WHEN dg.loaidangky = 'DoiNhom' THEN dt.tendoithi
                    WHEN dg.loaidangky = 'CaNhan' THEN nd.hoten
                    ELSE 'Chưa xác định'
                END as name")
            )
            ->orderBy('dg.ngaytrao', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'result' => $result,
            'rounds' => $rounds,
            'top3' => $top3,
            'all_awards' => $allAwards
        ]);
    }

    /**
     * Placeholder: Xuất PDF
     */
    public function exportPDF($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Chức năng xuất PDF đang được phát triển.'
        ]);
    }
}
