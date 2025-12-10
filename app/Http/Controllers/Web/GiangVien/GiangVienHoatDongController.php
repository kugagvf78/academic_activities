<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\HoatDongHoTro;
use App\Models\DangKyHoatDong;
use App\Models\DiemDanhQR;
use App\Models\CuocThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\DiemRenLuyen;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;

class GiangVienHoatDongController extends Controller
{
    /**
     * Danh sách hoạt động hỗ trợ
     */
    public function index(Request $request)
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $query = HoatDongHoTro::select('hoatdonghotro.*')
            ->selectRaw('(SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) as soluong_dangky')
            ->join('cuocthi', 'hoatdonghotro.macuocthi', '=', 'cuocthi.macuocthi')
            ->where('cuocthi.mabomon', $giangvien->mabomon);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('hoatdonghotro.tenhoatdong', 'ILIKE', "%{$search}%");
        }

        if ($request->filled('loai')) {
            $query->where('hoatdonghotro.loaihoatdong', $request->loai);
        }

        if ($request->filled('cuocthi')) {
            $query->where('hoatdonghotro.macuocthi', $request->cuocthi);
        }

        if ($request->filled('status')) {
            $now = now();
            if ($request->status == 'upcoming') {
                $query->where('hoatdonghotro.thoigianbatdau', '>', $now);
            } elseif ($request->status == 'ongoing') {
                $query->where('hoatdonghotro.thoigianbatdau', '<=', $now)
                      ->where('hoatdonghotro.thoigianketthuc', '>=', $now);
            } elseif ($request->status == 'completed') {
                $query->where('hoatdonghotro.thoigianketthuc', '<', $now);
            }
        }

        $hoatdongs = $query->with('cuocthi')
            ->orderBy('hoatdonghotro.thoigianbatdau', 'desc')
            ->paginate(10);

        $hoatdongs->getCollection()->transform(function ($hd) {
            $hd->status_label = $this->getStatusLabel($hd);
            $hd->status_color = $this->getStatusColor($hd);
            return $hd;
        });

        $cuocthis = CuocThi::where('mabomon', $giangvien->mabomon)
            ->orderBy('thoigianbatdau', 'desc')
            ->get();

        return view('giangvien.hoatdong.index', compact('hoatdongs', 'cuocthis', 'giangvien'));
    }

    public function create()
    {
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $cuocthis = CuocThi::where('mabomon', $giangvien->mabomon)
            ->whereIn('trangthai', ['Approved', 'InProgress'])
            ->orderBy('thoigianbatdau', 'desc')
            ->get();
        
        return view('giangvien.hoatdong.create', compact('giangvien', 'cuocthis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenhoatdong' => 'required|string|max:255',
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'loaihoatdong' => 'required|in:CoVu,HoTroKyThuat',
            'diemrenluyen' => 'nullable|numeric|min:0',
            'thoigianbatdau' => 'required|date',
            'thoigianketthuc' => 'required|date|after:thoigianbatdau',
            'diadiem' => 'nullable|string',
            'mota' => 'nullable|string',
            'soluong' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $lastHoatDong = HoatDongHoTro::where('mahoatdong', 'LIKE', 'HD%')
                ->orderByRaw('CAST(SUBSTRING(mahoatdong FROM 3) AS INTEGER) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastHoatDong && preg_match('/HD(\d+)/', $lastHoatDong->mahoatdong, $matches)) {
                $newNumber = intval($matches[1]) + 1;
            } else {
                $newNumber = 1;
            }
            
            $validated['mahoatdong'] = 'HD' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            HoatDongHoTro::create($validated);
            
            DB::commit();
            return redirect()->route('giangvien.hoatdong.index')
                ->with('success', 'Tạo hoạt động hỗ trợ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $hoatdong = HoatDongHoTro::with('cuocthi')
            ->selectRaw('hoatdonghotro.*, (SELECT COUNT(*) FROM dangkyhoatdong WHERE dangkyhoatdong.mahoatdong = hoatdonghotro.mahoatdong) as soluong_dangky')
            ->where('mahoatdong', $id)
            ->firstOrFail();

        $dangkys = DangKyHoatDong::where('mahoatdong', $id)
            ->with(['sinhvien.nguoidung', 'sinhvien.lop'])
            ->orderBy('ngaydangky', 'desc')
            ->get();

        $stats = [
            'total' => $dangkys->count(),
            'checked_in' => $dangkys->where('diemdanhqr', true)->count(),
            'not_checked_in' => $dangkys->where('diemdanhqr', false)->count(),
        ];

        return view('giangvien.hoatdong.show', compact('hoatdong', 'dangkys', 'stats'));
    }

    public function edit($id)
    {
        $hoatdong = HoatDongHoTro::findOrFail($id);
        
        $user = jwt_user();
        $giangvien = DB::table('giangvien')->where('manguoidung', $user->manguoidung)->first();
        
        $cuocthis = CuocThi::where('mabomon', $giangvien->mabomon)
            ->whereIn('trangthai', ['Approved', 'InProgress'])
            ->orderBy('thoigianbatdau', 'desc')
            ->get();
        
        return view('giangvien.hoatdong.edit', compact('hoatdong', 'cuocthis'));
    }

    public function update(Request $request, $id)
    {
        $hoatdong = HoatDongHoTro::findOrFail($id);

        $validated = $request->validate([
            'tenhoatdong' => 'required|string|max:255',
            'macuocthi' => 'required|exists:cuocthi,macuocthi',
            'loaihoatdong' => 'required|in:CoVu,HoTroKyThuat',
            'diemrenluyen' => 'nullable|numeric|min:0',
            'thoigianbatdau' => 'required|date',
            'thoigianketthuc' => 'required|date|after:thoigianbatdau',
            'diadiem' => 'nullable|string',
            'mota' => 'nullable|string',
            'soluong' => 'required|integer|min:1',
        ]);

        $hoatdong->update($validated);

        return redirect()->route('giangvien.hoatdong.show', $id)
            ->with('success', 'Cập nhật hoạt động thành công!');
    }

    public function destroy($id)
    {
        $hoatdong = HoatDongHoTro::findOrFail($id);
        $hasDangKy = DangKyHoatDong::where('mahoatdong', $id)->exists();

        if ($hasDangKy) {
            return back()->with('error', 'Không thể xóa hoạt động đã có sinh viên đăng ký!');
        }

        $hoatdong->delete();
        return redirect()->route('giangvien.hoatdong.index')
            ->with('success', 'Xóa hoạt động thành công!');
    }

    public function generateQR($id)
    {
        $hoatdong = HoatDongHoTro::with('cuocthi')->findOrFail($id);

        $stats = [
            'total' => DangKyHoatDong::where('mahoatdong', $id)->count(),
            'checked_in' => DangKyHoatDong::where('mahoatdong', $id)->where('diemdanhqr', true)->count(),
        ];

        return view('giangvien.hoatdong.qr-google-form', compact('hoatdong', 'stats'));
    }

    public function importFromGoogleForm(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        $hoatdong = HoatDongHoTro::findOrFail($id);
        
        Log::info('=== BẮT ĐẦU IMPORT ===', [
            'mahoatdong' => $hoatdong->mahoatdong,
            'tenhoatdong' => $hoatdong->tenhoatdong,
        ]);
        
        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            Log::info('File đã load', [
                'total_rows' => count($rows),
                'first_3_rows' => array_slice($rows, 0, 3)
            ]);

            // ✅ PHÁT HIỆN CẤU TRÚC FILE
            $hasHeader = false;
            $headerRow = -1;
            $masinhvienCol = 1; // Mặc định cột B (index 1)
            $timestampCol = 0;  // Mặc định cột A (index 0)

            // Kiểm tra dòng đầu có phải header không
            if (!empty($rows[0])) {
                $firstCell = mb_strtolower(trim($rows[0][0] ?? ''));
                $secondCell = mb_strtolower(trim($rows[0][1] ?? ''));
                
                // Nếu chứa từ khóa header
                if (preg_match('/(timestamp|thời|time)/ui', $firstCell) || 
                    preg_match('/(mã.*sinh|student|mssv)/ui', $secondCell)) {
                    $hasHeader = true;
                    $headerRow = 0;
                    
                    // Tìm chính xác cột nào
                    foreach ($rows[0] as $colIndex => $cell) {
                        $cellLower = mb_strtolower(trim($cell ?? ''));
                        if (preg_match('/(mã.*sinh.*viên|student.*id|masinhvien|mssv)/ui', $cellLower)) {
                            $masinhvienCol = $colIndex;
                        }
                        if (preg_match('/(timestamp|thời.*gian|time)/ui', $cellLower)) {
                            $timestampCol = $colIndex;
                        }
                    }
                }
            }

            Log::info('Cấu trúc file', [
                'hasHeader' => $hasHeader,
                'masinhvienCol' => $masinhvienCol,
                'timestampCol' => $timestampCol,
                'startRow' => $headerRow + 1,
            ]);

            // ✅ KIỂM TRA DANH SÁCH ĐĂNG KÝ TRƯỚC
            $allDangKy = DangKyHoatDong::where('mahoatdong', $id)
                ->pluck('diemdanhqr', 'masinhvien')
                ->toArray();
            
            Log::info('Danh sách đăng ký hiện có', [
                'total' => count($allDangKy),
                'sample' => array_slice($allDangKy, 0, 5, true)
            ]);

            DB::beginTransaction();
            $success = 0;
            $errors = [];
            $skipped = 0;
            $diemRenLuyenCount = 0;

            $startRow = $headerRow + 1;
            
            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Bỏ qua dòng trống
                if (empty(array_filter($row))) {
                    Log::info("Dòng [{$i}] trống, bỏ qua");
                    continue;
                }

                // ✅ LẤY DỮ LIỆU
                $rawMaSV = $row[$masinhvienCol] ?? '';
                $rawTimestamp = $row[$timestampCol] ?? '';
                
                // Chuẩn hóa mã sinh viên
                $masinhvien = trim($rawMaSV);
                $masinhvien = preg_replace('/[^\d]/', '', $masinhvien); // Chỉ giữ số
                
                Log::info("═══ DÒNG [{$i}] ═══", [
                    'raw_masinhvien' => $rawMaSV,
                    'cleaned_masinhvien' => $masinhvien,
                    'raw_timestamp' => $rawTimestamp,
                    'full_row' => $row
                ]);

                // Validate mã sinh viên
                if (empty($masinhvien) || !preg_match('/^\d{10}$/', $masinhvien)) {
                    $errors[] = "Dòng {$i}: Mã SV không hợp lệ ('{$rawMaSV}')";
                    Log::warning("Mã SV không hợp lệ", [
                        'row' => $i,
                        'raw' => $rawMaSV,
                        'cleaned' => $masinhvien
                    ]);
                    continue;
                }

                // Parse timestamp
                $thoigian = now();
                if (!empty($rawTimestamp)) {
                    try {
                        $thoigian = Carbon::parse($rawTimestamp);
                        Log::info("Parse timestamp OK", ['parsed' => $thoigian->toDateTimeString()]);
                    } catch (\Exception $e) {
                        Log::warning('Parse timestamp thất bại', [
                            'raw' => $rawTimestamp,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // ✅ TÌM ĐĂNG KÝ - DEBUG CHI TIẾT
                Log::info("Tìm kiếm đăng ký", [
                    'mahoatdong' => $id,
                    'masinhvien' => $masinhvien,
                    'query' => "SELECT * FROM dangkyhoatdong WHERE mahoatdong = '{$id}' AND masinhvien = '{$masinhvien}'"
                ]);

                $dangky = DangKyHoatDong::where('mahoatdong', $id)
                    ->where('masinhvien', $masinhvien)
                    ->first();

                if (!$dangky) {
                    $errors[] = "SV {$masinhvien}: Chưa đăng ký hoặc không tồn tại";
                    Log::error("❌ KHÔNG TÌM THẤY ĐĂNG KÝ", [
                        'masinhvien' => $masinhvien,
                        'mahoatdong' => $id,
                        'exists_in_list' => isset($allDangKy[$masinhvien]),
                        'similar_masv' => array_filter(array_keys($allDangKy), function($k) use ($masinhvien) {
                            return strpos($k, substr($masinhvien, 0, 5)) !== false;
                        })
                    ]);
                    continue;
                }

                Log::info("✅ Tìm thấy đăng ký", [
                    'madangky' => $dangky->madangky,
                    'masinhvien' => $dangky->masinhvien,
                    'diemdanhqr' => $dangky->diemdanhqr,
                    'thoigiandiemdanh' => $dangky->thoigiandiemdanh
                ]);

                // Kiểm tra đã điểm danh chưa
                if ($dangky->diemdanhqr) {
                    $skipped++;
                    Log::info("⏭️ Đã điểm danh", [
                        'masinhvien' => $masinhvien,
                        'thoigian_cu' => $dangky->thoigiandiemdanh
                    ]);
                    continue;
                }

                // ✅ CẬP NHẬT ĐIỂM DANH
                try {
                    $dangky->diemdanhqr = true;
                    $dangky->thoigiandiemdanh = $thoigian;
                    $dangky->save();
                    
                    Log::info("✅ Cập nhật điểm danh thành công", [
                        'madangky' => $dangky->madangky,
                        'masinhvien' => $masinhvien,
                        'thoigian' => $thoigian->toDateTimeString()
                    ]);
                } catch (\Exception $e) {
                    Log::error("❌ Lỗi cập nhật điểm danh", [
                        'masinhvien' => $masinhvien,
                        'error' => $e->getMessage()
                    ]);
                    $errors[] = "SV {$masinhvien}: Lỗi cập nhật - {$e->getMessage()}";
                    continue;
                }

                // ✅ TẠO BẢNG GHI ĐIỂM DANH QR
                try {
                    DiemDanhQR::create([
                        'madiemdanh' => 'DD' . strtoupper(Str::random(8)),
                        'mahoatdong' => $hoatdong->mahoatdong,
                        'macuocthi' => $hoatdong->macuocthi,
                        'masinhvien' => $masinhvien,
                        'maqr' => 'GOOGLE-FORM-IMPORT',
                        'thoigiandiemdanh' => $thoigian,
                        'vitri' => 'Import từ Google Form',
                    ]);
                    
                    Log::info("✅ Tạo DiemDanhQR thành công");
                } catch (\Exception $e) {
                    Log::error("❌ Lỗi tạo DiemDanhQR", [
                        'masinhvien' => $masinhvien,
                        'error' => $e->getMessage()
                    ]);
                }

                // ✅ CỘNG ĐIỂM RÈN LUYỆN
                if ($hoatdong->diemrenluyen > 0) {
                    Log::info("Kiểm tra điểm rèn luyện", [
                        'masinhvien' => $masinhvien,
                        'diem' => $hoatdong->diemrenluyen
                    ]);

                    $daCongDiem = DiemRenLuyen::where('masinhvien', $masinhvien)
                        ->where('mahoatdong', $hoatdong->mahoatdong)
                        ->exists();

                    if (!$daCongDiem) {
                        try {
                            $maDiemRL = 'DRL' . time() . rand(1000, 9999);
                            
                            DiemRenLuyen::create([
                                'madiemrl' => $maDiemRL,
                                'masinhvien' => $masinhvien,
                                'macuocthi' => $hoatdong->macuocthi,
                                'mahoatdong' => $hoatdong->mahoatdong,
                                'loaihoatdong' => 'HoTro',
                                'diem' => $hoatdong->diemrenluyen,
                                'mota' => 'Điểm danh: ' . $hoatdong->tenhoatdong,
                                'ngaycong' => now(),
                            ]);

                            $sinhVien = SinhVien::where('masinhvien', $masinhvien)->first();
                            if ($sinhVien) {
                                $diemCu = $sinhVien->diemrenluyen ?? 0;
                                $sinhVien->diemrenluyen = $diemCu + $hoatdong->diemrenluyen;
                                $sinhVien->save();
                                
                                $diemRenLuyenCount++;
                                
                                Log::info("✅ Cộng điểm thành công", [
                                    'masinhvien' => $masinhvien,
                                    'diem_cu' => $diemCu,
                                    'diem_cong' => $hoatdong->diemrenluyen,
                                    'diem_moi' => $sinhVien->diemrenluyen
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error("❌ Lỗi cộng điểm", [
                                'masinhvien' => $masinhvien,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    } else {
                        Log::info("Đã cộng điểm trước đó", ['masinhvien' => $masinhvien]);
                    }
                }

                $success++;
            }

            DB::commit();

            Log::info('=== KẾT THÚC IMPORT ===', [
                'success' => $success,
                'skipped' => $skipped,
                'errors' => count($errors),
                'diemRenLuyenCount' => $diemRenLuyenCount
            ]);

            $message = "✅ Điểm danh thành công: {$success} sinh viên";
            if ($skipped > 0) $message .= " | ⏭️ Đã điểm danh trước: {$skipped}";
            if ($diemRenLuyenCount > 0) $message .= " | 🎯 Cộng điểm: {$diemRenLuyenCount} SV";
            if (count($errors) > 0) {
                $message .= " | ⚠️ Lỗi: " . count($errors);
                Log::warning('Chi tiết lỗi', ['errors' => $errors]);
            }

            return redirect()
                ->route('giangvien.hoatdong.show', $id)
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ LỖI NGHIÊM TRỌNG', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', '❌ Lỗi: ' . $e->getMessage());
        }
    }

    public function exportAttendance($id)
    {
        $hoatdong = HoatDongHoTro::with('cuocthi')->findOrFail($id);
        $dangkys = DangKyHoatDong::where('mahoatdong', $id)
            ->with(['sinhvien.nguoidung', 'sinhvien.lop'])
            ->orderBy('ngaydangky', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'DANH SÁCH ĐIỂM DANH - ' . $hoatdong->tenhoatdong);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', 'Cuộc thi: ' . $hoatdong->cuocthi->tencuocthi);
        $sheet->setCellValue('A3', 'Thời gian: ' . $hoatdong->thoigianbatdau->format('d/m/Y H:i') . ' - ' . $hoatdong->thoigianketthuc->format('d/m/Y H:i'));
        $sheet->setCellValue('A4', 'Địa điểm: ' . ($hoatdong->diadiem ?? 'Chưa xác định'));

        $row = 6;
        $headers = ['STT', 'Mã SV', 'Họ tên', 'Lớp', 'Trạng thái', 'Thời gian điểm danh', 'Ghi chú'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, $row, $header);
        }
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:G{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $row++;
        $stt = 1;
        foreach ($dangkys as $dk) {
            $sheet->setCellValue("A{$row}", $stt++);
            $sheet->setCellValue("B{$row}", $dk->sinhvien->masinhvien);
            $sheet->setCellValue("C{$row}", $dk->sinhvien->nguoidung->hoten);
            $sheet->setCellValue("D{$row}", $dk->sinhvien->malop ?? 'N/A');
            $sheet->setCellValue("E{$row}", $dk->diemdanhqr ? 'Đã điểm danh' : 'Chưa điểm danh');
            $sheet->setCellValue("F{$row}", $dk->thoigiandiemdanh ? $dk->thoigiandiemdanh->format('d/m/Y H:i:s') : '');
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'diem-danh-' . Str::slug($hoatdong->tenhoatdong) . '-' . date('YmdHis') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function getStatusLabel($hoatdong)
    {
        $now = Carbon::now();
        $start = Carbon::parse($hoatdong->thoigianbatdau);
        $end = Carbon::parse($hoatdong->thoigianketthuc);

        if ($now->lt($start)) return 'Sắp diễn ra';
        elseif ($now->between($start, $end)) return 'Đang diễn ra';
        else return 'Đã kết thúc';
    }

    private function getStatusColor($hoatdong)
    {
        $now = Carbon::now();
        $start = Carbon::parse($hoatdong->thoigianbatdau);
        $end = Carbon::parse($hoatdong->thoigianketthuc);

        if ($now->lt($start)) return 'yellow';
        elseif ($now->between($start, $end)) return 'green';
        else return 'gray';
    }
}