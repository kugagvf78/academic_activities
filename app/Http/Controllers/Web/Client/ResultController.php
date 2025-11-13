<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResultController extends Controller
{
    /**
     * Hiển thị danh sách kết quả cuộc thi
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
                'bm.tenbomon',
                DB::raw('(SELECT COUNT(*) FROM dangkyduthi WHERE macuocthi = ct.macuocthi) as soluongthamgia'),
                DB::raw('(SELECT COUNT(*) FROM datgiai WHERE macuocthi = ct.macuocthi) as soluonggiai'),
                DB::raw("(SELECT COALESCE(dt.tendoithi, nd.hoten, 'Chưa công bố') 
                          FROM datgiai dg 
                          LEFT JOIN dangkyduthi dk ON dg.madangky = dk.madangky 
                          LEFT JOIN doithi dt ON dk.madoithi = dt.madoithi
                          LEFT JOIN sinhvien sv ON dk.masinhvien = sv.masinhvien
                          LEFT JOIN nguoidung nd ON sv.manguoidung = nd.manguoidung
                          WHERE dg.macuocthi = ct.macuocthi 
                          AND dg.tengiai ILIKE '%nhất%' 
                          LIMIT 1) as nguoithang")
            );

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ct.tencuocthi', 'ILIKE', "%{$search}%");
        }

        // Lọc theo năm
        if ($request->filled('year')) {
            $year = $request->year;
            $query->whereYear('ct.thoigianketthuc', $year);
        }

        // Lọc theo hình thức (cá nhân/đội)
        if ($request->filled('type')) {
            $type = $request->type;
            if ($type === 'individual') {
                $query->where('ct.hinhthucthamgia', 'CaNhan');
            } elseif ($type === 'team') {
                $query->where('ct.hinhthucthamgia', 'DoiNhom');
            }
        }

        // Sắp xếp theo thời gian kết thúc giảm dần
        $query->orderBy('ct.thoigianketthuc', 'desc');

        // Phân trang
        $results = $query->paginate(6)->appends($request->query());

        // Transform data
        $results->getCollection()->transform(function ($result) {
            $result->date = Carbon::parse($result->thoigianketthuc)->format('d/m/Y');
            $result->year = Carbon::parse($result->thoigianketthuc)->format('Y');
            $result->winner = $result->nguoithang ?? 'Chưa công bố';
            return $result;
        });

        // Lấy danh sách năm có kết quả
        $years = DB::table('cuocthi')
            ->where('thoigianketthuc', '<', now())
            ->selectRaw('DISTINCT EXTRACT(YEAR FROM thoigianketthuc) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('client.results.index', compact('results', 'years'));
    }

    /**
     * Hiển thị chi tiết kết quả cuộc thi
     */
    public function show($id)
    {
        // Lấy thông tin cuộc thi
        $result = DB::table('cuocthi as ct')
            ->leftJoin('bomon as bm', 'ct.mabomon', '=', 'bm.mabomon')
            ->where('ct.macuocthi', $id)
            ->select(
                'ct.*',
                'bm.tenbomon',
                DB::raw('(SELECT COUNT(*) FROM dangkyduthi WHERE macuocthi = ct.macuocthi) as soluongthamgia'),
                DB::raw('(SELECT COUNT(DISTINCT madoithi) FROM dangkyduthi WHERE macuocthi = ct.macuocthi AND madoithi IS NOT NULL) as soluongdoi')
            )
            ->first();

        if (!$result) {
            abort(404, 'Không tìm thấy cuộc thi');
        }

        // Format date
        $result->date = Carbon::parse($result->thoigianketthuc)->format('d/m/Y');
        $result->title = $result->tencuocthi;

        // Lấy danh sách vòng thi với người thắng
        $rounds = DB::table('vongthi as vt')
            ->where('vt.macuocthi', $id)
            ->select(
                'vt.mavongthi',
                'vt.tenvongthi',
                'vt.thutu',
                'vt.thoigianbatdau',
                'vt.thoigianketthuc',
                DB::raw("(SELECT COALESCE(dt.tendoithi, nd.hoten, 'Chưa xác định') 
                          FROM datgiai dg 
                          LEFT JOIN dangkyduthi dk ON dg.madangky = dk.madangky 
                          LEFT JOIN doithi dt ON dk.madoithi = dt.madoithi
                          LEFT JOIN sinhvien sv ON dk.masinhvien = sv.masinhvien
                          LEFT JOIN nguoidung nd ON sv.manguoidung = nd.manguoidung
                          WHERE dg.macuocthi = vt.macuocthi
                          AND dg.tengiai ILIKE CONCAT('%', vt.tenvongthi, '%')
                          LIMIT 1) as winner")
            )
            ->orderBy('vt.thutu')
            ->get()
            ->map(function($round) {
                return [
                    'name' => $round->tenvongthi,
                    'winner' => $round->winner ?? 'Chưa có kết quả',
                    'start' => $round->thoigianbatdau,
                    'end' => $round->thoigianketthuc,
                ];
            });

        // Lấy top 3 giải thưởng
        $top3 = DB::table('datgiai as dg')
            ->join('dangkyduthi as dk', 'dg.madangky', '=', 'dk.madangky')
            ->leftJoin('doithi as dt', 'dk.madoithi', '=', 'dt.madoithi')
            ->leftJoin('sinhvien as sv', 'dk.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dg.macuocthi', $id)
            ->select(
                'dg.madatgiai',
                'dg.tengiai',
                'dg.giaithuong',
                'dg.diemrenluyen',
                'dg.ngaytrao',
                DB::raw('COALESCE(dt.tendoithi, nd.hoten, \'Chưa xác định\') as name'),
                DB::raw('CASE 
                    WHEN dg.tengiai ILIKE \'%nhất%\' OR dg.tengiai ILIKE \'%1%\' THEN 1
                    WHEN dg.tengiai ILIKE \'%nhì%\' OR dg.tengiai ILIKE \'%hai%\' OR dg.tengiai ILIKE \'%2%\' THEN 2
                    WHEN dg.tengiai ILIKE \'%ba%\' OR dg.tengiai ILIKE \'%3%\' THEN 3
                    ELSE 4
                END as rank_order')
            )
            ->orderBy('rank_order')
            ->limit(3)
            ->get()
            ->map(function($item, $index) {
                return [
                    'name' => $item->name,
                    'rank' => $item->tengiai,
                    'prize' => $item->giaithuong ?? 'Giấy khen',
                    'score' => $item->diemrenluyen ?? 0,
                    'date' => $item->ngaytrao ? Carbon::parse($item->ngaytrao)->format('d/m/Y') : null,
                ];
            });

        // Nếu không có top 3 từ database, tạo placeholder
        if ($top3->count() === 0) {
            $top3 = collect([
                ['name' => 'Chưa công bố', 'rank' => 'Giải Nhất', 'prize' => '1.000.000đ + Giấy khen', 'score' => 0],
                ['name' => 'Chưa công bố', 'rank' => 'Giải Nhì', 'prize' => '700.000đ + Giấy khen', 'score' => 0],
                ['name' => 'Chưa công bố', 'rank' => 'Giải Ba', 'prize' => '500.000đ + Giấy khen', 'score' => 0],
            ]);
        }

        // Lấy danh sách tất cả giải thưởng
        $allAwards = DB::table('datgiai as dg')
            ->join('dangkyduthi as dk', 'dg.madangky', '=', 'dk.madangky')
            ->leftJoin('doithi as dt', 'dk.madoithi', '=', 'dt.madoithi')
            ->leftJoin('sinhvien as sv', 'dk.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->where('dg.macuocthi', $id)
            ->select(
                'dg.tengiai',
                'dg.giaithuong',
                'dg.diemrenluyen',
                'dg.ngaytrao',
                DB::raw('COALESCE(dt.tendoithi, nd.hoten, \'Chưa xác định\') as name')
            )
            ->orderBy('dg.ngaytrao', 'desc')
            ->get();

        // Chuyển đổi để tương thích với view
        $result->rounds = $rounds;
        $result->top3 = $top3;
        $result->allAwards = $allAwards;

        return view('client.results.show', compact('result'));
    }

    /**
     * Xuất báo cáo kết quả PDF
     */
    public function exportPDF($id)
    {
        // TODO: Implement PDF export
        return back()->with('info', 'Chức năng xuất PDF đang được phát triển');
    }
}