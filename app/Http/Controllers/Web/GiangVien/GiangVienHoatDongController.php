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
        
        // ✅ LOG: Thông tin hoạt động
        Log::info('Import điểm danh cho hoạt động', [
            'mahoatdong' => $hoatdong->mahoatdong,
            'tenhoatdong' => $hoatdong->tenhoatdong,
            'diemrenluyen' => $hoatdong->diemrenluyen,
            'loaihoatdong' => $hoatdong->loaihoatdong,
        ]);
        
        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $headerRow = 0;
            $masinhvienCol = null;
            $timestampCol = null;

            // Tìm cột mã sinh viên và timestamp
            foreach ($rows as $index => $row) {
                foreach ($row as $colIndex => $cell) {
                    $cell = mb_strtolower(trim($cell ?? ''));
                    
                    if (str_contains($cell, 'mã sinh viên') || 
                        str_contains($cell, 'masinhvien') || 
                        str_contains($cell, 'student')) {
                        $headerRow = $index;
                        $masinhvienCol = $colIndex;
                    }
                    
                    if (str_contains($cell, 'timestamp') || 
                        str_contains($cell, 'thời gian') || 
                        str_contains($cell, 'time')) {
                        $timestampCol = $colIndex;
                    }
                }
                
                if ($masinhvienCol !== null) break;
            }

            if ($masinhvienCol === null) {
                return back()->with('error', 'Không tìm thấy cột "Mã sinh viên" trong file!');
            }

            Log::info('Tìm thấy cột', [
                'masinhvienCol' => $masinhvienCol,
                'timestampCol' => $timestampCol,
                'headerRow' => $headerRow,
            ]);

            DB::beginTransaction();
            $success = 0;
            $errors = [];
            $diemRenLuyenCount = 0;

            // Xử lý từng dòng dữ liệu
            for ($i = $headerRow + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;

                $masinhvien = trim($row[$masinhvienCol] ?? '');
                if (empty($masinhvien)) continue;

                $thoigian = now();
                if ($timestampCol !== null && !empty($row[$timestampCol])) {
                    try {
                        $thoigian = Carbon::parse($row[$timestampCol]);
                    } catch (\Exception $e) {
                        Log::warning('Không parse được timestamp', [
                            'masinhvien' => $masinhvien,
                            'timestamp' => $row[$timestampCol],
                        ]);
                    }
                }

                // Kiểm tra sinh viên đã đăng ký chưa
                $dangky = DangKyHoatDong::where('mahoatdong', $id)
                    ->where('masinhvien', $masinhvien)
                    ->first();

                if (!$dangky) {
                    $errors[] = "SV {$masinhvien}: Chưa đăng ký";
                    Log::warning('Sinh viên chưa đăng ký', ['masinhvien' => $masinhvien]);
                    continue;
                }

                // Bỏ qua nếu đã điểm danh
                if ($dangky->diemdanhqr) {
                    Log::info('Sinh viên đã điểm danh trước đó', ['masinhvien' => $masinhvien]);
                    continue;
                }

                // Cập nhật trạng thái điểm danh
                $dangky->update([
                    'diemdanhqr' => true,
                    'thoigiandiemdanh' => $thoigian,
                ]);

                // Tạo bản ghi điểm danh QR
                DiemDanhQR::create([
                    'madiemdanh' => 'DD' . Str::upper(Str::random(8)),
                    'mahoatdong' => $hoatdong->mahoatdong,
                    'macuocthi' => $hoatdong->macuocthi,
                    'masinhvien' => $masinhvien,
                    'maqr' => 'GOOGLE-FORM',
                    'thoigiandiemdanh' => $thoigian,
                    'vitri' => 'Import từ Google Form',
                ]);

                Log::info('Điểm danh thành công', ['masinhvien' => $masinhvien]);

                // ✅ CỘNG ĐIỂM RÈN LUYỆN
                if ($hoatdong->diemrenluyen > 0) {
                    Log::info('Bắt đầu cộng điểm rèn luyện', [
                        'masinhvien' => $masinhvien,
                        'diem' => $hoatdong->diemrenluyen,
                    ]);

                    // Kiểm tra xem đã cộng điểm cho hoạt động này chưa
                    $daCongDiem = DiemRenLuyen::where('masinhvien', $masinhvien)
                        ->where('mahoatdong', $hoatdong->mahoatdong)
                        ->exists();
                    
                    Log::info('Kiểm tra trùng lặp', [
                        'masinhvien' => $masinhvien,
                        'daCongDiem' => $daCongDiem,
                    ]);

                    if (!$daCongDiem) {
                        try {
                            // Tạo mã điểm rèn luyện duy nhất
                            $maDiemRL = 'DRL' . time() . rand(1000, 9999);
                            
                            // ✅ XÁC ĐỊNH LOẠI HOẠT ĐỘNG - CHỈ CÓ 3 GIÁ TRỊ HỢP LỆ: DuThi, HoTro, DatGiai
                            // Tất cả các loại hoạt động hỗ trợ đều map sang 'HoTro'
                            $loaiDiemRL = 'HoTro';
                            
                            Log::info('Chuẩn bị insert điểm rèn luyện', [
                                'madiemrl' => $maDiemRL,
                                'masinhvien' => $masinhvien,
                                'macuocthi' => $hoatdong->macuocthi,
                                'mahoatdong' => $hoatdong->mahoatdong,
                                'loaihoatdong' => $loaiDiemRL,
                                'diem' => $hoatdong->diemrenluyen,
                            ]);
                            
                            // Tạo bản ghi điểm rèn luyện
                            DiemRenLuyen::create([
                                'madiemrl' => $maDiemRL,
                                'masinhvien' => $masinhvien,
                                'macuocthi' => $hoatdong->macuocthi,
                                'mahoatdong' => $hoatdong->mahoatdong,
                                'loaihoatdong' => $loaiDiemRL,
                                'diem' => $hoatdong->diemrenluyen,
                                'mota' => 'Điểm danh hoạt động: ' . $hoatdong->tenhoatdong,
                                'ngaycong' => now(),
                            ]);

                            Log::info('Insert điểm rèn luyện thành công', [
                                'masinhvien' => $masinhvien,
                                'madiemrl' => $maDiemRL,
                            ]);
                            
                            // Cập nhật tổng điểm rèn luyện của sinh viên
                            $sinhVien = SinhVien::where('masinhvien', $masinhvien)->first();
                            if ($sinhVien) {
                                $diemCu = $sinhVien->diemrenluyen ?? 0;
                                $sinhVien->diemrenluyen = $diemCu + $hoatdong->diemrenluyen;
                                $sinhVien->save();

                                Log::info('Cập nhật tổng điểm sinh viên thành công', [
                                    'masinhvien' => $masinhvien,
                                    'diem_cu' => $diemCu,
                                    'diem_cong' => $hoatdong->diemrenluyen,
                                    'diem_moi' => $sinhVien->diemrenluyen,
                                ]);

                                $diemRenLuyenCount++;
                            } else {
                                Log::warning('Không tìm thấy sinh viên để cập nhật điểm', [
                                    'masinhvien' => $masinhvien,
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Lỗi cộng điểm rèn luyện', [
                                'masinhvien' => $masinhvien,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            // Không throw exception, tiếp tục xử lý sinh viên khác
                        }
                    } else {
                        Log::warning('Đã cộng điểm trước đó', ['masinhvien' => $masinhvien]);
                    }
                } else {
                    Log::warning('Hoạt động không có điểm rèn luyện', [
                        'mahoatdong' => $hoatdong->mahoatdong,
                    ]);
                }

                $success++;
            }

            DB::commit();

            // ✅ LOG: Tổng kết
            Log::info('Hoàn thành import', [
                'success' => $success,
                'diemRenLuyenCount' => $diemRenLuyenCount,
                'errors' => count($errors),
            ]);

            $message = "Import thành công {$success} sinh viên điểm danh!";
            // if ($diemRenLuyenCount > 0) {
            //     $message .= " ✅ Đã cộng điểm rèn luyện cho <strong>{$diemRenLuyenCount}</strong> sinh viên.";
            // }
            if (count($errors) > 0) {
                $message .= " ⚠️ Có <strong>" . count($errors) . "</strong> lỗi.";
            }

            return redirect()->route('giangvien.hoatdong.show', $id)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi import điểm danh', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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