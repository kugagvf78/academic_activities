<?php

namespace App\Http\Controllers\Web\Client;

use App\Models\CuocThi;
use App\Models\SinhVien;
use App\Models\DoiThi;
use App\Models\DangKyCaNhan;
use App\Models\DangKyDoiThi;
use App\Models\ThanhVienDoiThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ContestRegistrationController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function showRegistrationForm($slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        
        $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
        
        // Kiểm tra trạng thái cuộc thi
        if (!in_array($cuocthi->trangthai, ['Approved', 'InProgress'])) {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi không mở đăng ký');
        }
        
        // Kiểm tra thời gian - CHỈ CHO ĐĂNG KÝ KHI CHƯA BẮT ĐẦU
        $now = now();
        $start = $cuocthi->thoigianbatdau;
        
        if ($now->gte($start)) {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi đã bắt đầu, không thể đăng ký thêm');
        }
        
        // Kiểm tra hình thức tham gia có hợp lệ không
        if (empty($cuocthi->hinhthucthamgia)) {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi chưa xác định hình thức tham gia');
        }
        
        return view('client.events.register', compact('cuocthi', 'slug'));
    }

    /**
     * Xử lý đăng ký cuộc thi
     */
    public function register(Request $request, $slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        
        $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
        
        // KIỂM TRA HÌNH THỨC THAM GIA
        $requestType = $request->input('type');
        
        // Validate theo hình thức của cuộc thi
        if ($cuocthi->hinhthucthamgia === 'CaNhan' && $requestType !== 'individual') {
            return back()->with('error', 'Cuộc thi này chỉ cho phép đăng ký cá nhân!')
                        ->withInput();
        }
        
        if ($cuocthi->hinhthucthamgia === 'DoiNhom' && $requestType !== 'team') {
            return back()->with('error', 'Cuộc thi này chỉ cho phép đăng ký theo đội/nhóm!')
                        ->withInput();
        }
        
        if ($cuocthi->hinhthucthamgia === 'CaHai' && !in_array($requestType, ['individual', 'team'])) {
            return back()->with('error', 'Hình thức đăng ký không hợp lệ!')
                        ->withInput();
        }
        
        // Validate dữ liệu - CẬP NHẬT RULES
        $rules = [
            'type' => 'required|in:individual,team',
            'main_name' => 'required|string|max:255',
            'main_student_code' => 'required|string|max:50',
            'main_email' => 'required|email|max:255',
            'main_phone' => 'required|string|max:20',
            'team_name' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ];
        
        // Thêm validation cho members chỉ khi đăng ký theo đội
        if ($requestType === 'team') {
            $rules['members'] = 'required|array|min:1';
            $rules['members.*.name'] = 'required|string|max:255';
            $rules['members.*.student_code'] = 'required|string|max:50';
            $rules['members.*.email'] = 'required|email|max:255';
        }
        
        $validated = $request->validate($rules, [
            'main_name.required' => 'Vui lòng nhập họ và tên',
            'main_student_code.required' => 'Vui lòng nhập mã sinh viên',
            'main_email.required' => 'Vui lòng nhập email',
            'main_phone.required' => 'Vui lòng nhập số điện thoại',
            'team_name.required' => 'Vui lòng nhập tên đội',
            'members.required' => 'Vui lòng thêm thành viên nhóm',
            'members.min' => 'Đội thi phải có ít nhất 1 thành viên ngoài trưởng đội',
        ]);

        DB::beginTransaction();
        
        try {
            // Kiểm tra sinh viên chính có tồn tại không
            $sinhvienChinh = SinhVien::where('masinhvien', $validated['main_student_code'])->first();
            
            if (!$sinhvienChinh) {
                return back()->withErrors(['main_student_code' => 'Mã sinh viên không tồn tại trong hệ thống'])
                            ->withInput();
            }

            // ===== XỬ LÝ ĐĂNG KÝ CÁ NHÂN =====
            if ($validated['type'] === 'individual') {
                // Kiểm tra đã đăng ký cá nhân chưa
                $daDangKyCaNhan = DangKyCaNhan::where('macuocthi', $macuocthi)
                                            ->where('masinhvien', $sinhvienChinh->masinhvien)
                                            ->exists();
                
                if ($daDangKyCaNhan) {
                    return back()->with('error', 'Bạn đã đăng ký cá nhân cuộc thi này rồi!')
                                ->withInput();
                }

                // Kiểm tra đã tham gia đội nào chưa
                $daThamGiaDoi = ThanhVienDoiThi::join('doithi', 'thanhviendoithi.madoithi', '=', 'doithi.madoithi')
                                            ->where('doithi.macuocthi', $macuocthi)
                                            ->where('thanhviendoithi.masinhvien', $sinhvienChinh->masinhvien)
                                            ->exists();
                
                if ($daThamGiaDoi) {
                    return back()->with('error', 'Bạn đã tham gia đội thi trong cuộc thi này rồi!')
                                ->withInput();
                }

                // Tạo đăng ký cá nhân
                $madangkycanhan = 'DKCN' . Str::upper(Str::random(8));
                
                DangKyCaNhan::create([
                    'madangkycanhan' => $madangkycanhan,
                    'macuocthi' => $macuocthi,
                    'masinhvien' => $sinhvienChinh->masinhvien,
                    'ngaydangky' => now(),
                    'trangthai' => 'Registered',
                    'ghichu' => $validated['note'] ?? null,
                ]);

                DB::commit();
                
                return back()->with('success', 'Đăng ký cá nhân thành công! Chúc bạn thi tốt! 🎉');
            }

            // ===== XỬ LÝ ĐĂNG KÝ THEO ĐỘI =====
            if ($validated['type'] === 'team') {
                // Kiểm tra số lượng thành viên
                if (empty($validated['members']) || count($validated['members']) < 1) {
                    DB::rollBack();
                    return back()->withErrors(['members' => 'Đội thi phải có ít nhất 1 thành viên ngoài trưởng đội'])
                                ->withInput();
                }

                // Kiểm tra trưởng đội đã đăng ký cá nhân chưa
                $daDangKyCaNhan = DangKyCaNhan::where('macuocthi', $macuocthi)
                                            ->where('masinhvien', $sinhvienChinh->masinhvien)
                                            ->exists();
                
                if ($daDangKyCaNhan) {
                    return back()->with('error', 'Bạn đã đăng ký cá nhân cuộc thi này rồi, không thể đăng ký đội!')
                                ->withInput();
                }

                // Kiểm tra trưởng đội đã tham gia đội khác chưa
                $daThamGiaDoiKhac = ThanhVienDoiThi::join('doithi', 'thanhviendoithi.madoithi', '=', 'doithi.madoithi')
                                                ->where('doithi.macuocthi', $macuocthi)
                                                ->where('thanhviendoithi.masinhvien', $sinhvienChinh->masinhvien)
                                                ->exists();
                
                if ($daThamGiaDoiKhac) {
                    return back()->with('error', 'Bạn đã tham gia đội thi khác trong cuộc thi này rồi!')
                                ->withInput();
                }

                // Tạo đội thi
                $madoithi = 'DT' . Str::upper(Str::random(8));
                
                // Số thành viên = 1 (trưởng đội) + số thành viên thêm vào
                $sothanhvien = 1 + count($validated['members']);
                
                $doithi = DoiThi::create([
                    'madoithi' => $madoithi,
                    'tendoithi' => $validated['team_name'],
                    'macuocthi' => $macuocthi,
                    'matruongdoi' => $sinhvienChinh->masinhvien,
                    'sothanhvien' => $sothanhvien,
                    'ngaydangky' => now(),
                    'trangthai' => 'Active',
                ]);

                // Lưu trưởng đội vào bảng ThanhVienDoiThi
                $mathanhvienTruongDoi = 'TV' . Str::upper(Str::random(8));
                
                ThanhVienDoiThi::create([
                    'mathanhvien' => $mathanhvienTruongDoi,
                    'madoithi' => $madoithi,
                    'masinhvien' => $sinhvienChinh->masinhvien,
                    'vaitro' => 'TruongDoi',
                    'ngaythamgia' => now(),
                ]);

                // Thêm thành viên nhóm
                foreach ($validated['members'] as $member) {
                    // Kiểm tra mã sinh viên thành viên
                    $sinhvienThanhVien = SinhVien::where('masinhvien', $member['student_code'])->first();
                    
                    if (!$sinhvienThanhVien) {
                        DB::rollBack();
                        return back()->withErrors(['members' => "Mã sinh viên {$member['student_code']} không tồn tại"])
                                    ->withInput();
                    }

                    // Kiểm tra thành viên đã đăng ký cá nhân chưa
                    $thanhVienDaDangKyCaNhan = DangKyCaNhan::where('macuocthi', $macuocthi)
                                                        ->where('masinhvien', $sinhvienThanhVien->masinhvien)
                                                        ->exists();
                    
                    if ($thanhVienDaDangKyCaNhan) {
                        DB::rollBack();
                        return back()->withErrors(['members' => "Sinh viên {$member['name']} đã đăng ký cá nhân cuộc thi này"])
                                    ->withInput();
                    }

                    // Kiểm tra thành viên đã trong đội khác chưa
                    $daTrongDoiKhac = ThanhVienDoiThi::join('doithi', 'thanhviendoithi.madoithi', '=', 'doithi.madoithi')
                                                    ->where('doithi.macuocthi', $macuocthi)
                                                    ->where('thanhviendoithi.masinhvien', $sinhvienThanhVien->masinhvien)
                                                    ->exists();
                    
                    if ($daTrongDoiKhac) {
                        DB::rollBack();
                        return back()->withErrors(['members' => "Sinh viên {$member['name']} đã tham gia đội khác trong cuộc thi này"])
                                    ->withInput();
                    }

                    $mathanhvien = 'TV' . Str::upper(Str::random(8));
                    
                    ThanhVienDoiThi::create([
                        'mathanhvien' => $mathanhvien,
                        'madoithi' => $madoithi,
                        'masinhvien' => $sinhvienThanhVien->masinhvien,
                        'vaitro' => 'ThanhVien',
                        'ngaythamgia' => now(),
                    ]);
                }

                // Tạo đăng ký đội thi
                $madangkydoi = 'DKDT' . Str::upper(Str::random(8));
                
                DangKyDoiThi::create([
                    'madangkydoi' => $madangkydoi,
                    'macuocthi' => $macuocthi,
                    'madoithi' => $madoithi,
                    'ngaydangky' => now(),
                    'trangthai' => 'Registered',
                    'ghichu' => $validated['note'] ?? null,
                ]);

                DB::commit();
                
                return back()->with('success', 'Đăng ký đội thi thành công! Chúc đội bạn thi tốt! 🎉');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Lỗi đăng ký cuộc thi: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Huỷ đăng ký cá nhân
     */
    public function cancelIndividualRegistration(Request $request, $slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        $masinhvien = $request->input('masinhvien');
        
        DB::beginTransaction();
        
        try {
            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
            
            // Kiểm tra cuộc thi chưa bắt đầu thì mới cho huỷ
            if (now()->gte($cuocthi->thoigianbatdau)) {
                return back()->with('error', 'Không thể huỷ đăng ký sau khi cuộc thi đã bắt đầu!');
            }
            
            $dangky = DangKyCaNhan::where('macuocthi', $macuocthi)
                                  ->where('masinhvien', $masinhvien)
                                  ->firstOrFail();
            
            // Cập nhật trạng thái thành Cancelled thay vì xoá
            $dangky->update([
                'trangthai' => 'Cancelled',
                'ngayhuy' => now()
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Đã huỷ đăng ký cá nhân thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi huỷ đăng ký cá nhân: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra khi huỷ đăng ký: ' . $e->getMessage());
        }
    }
    
    /**
     * Huỷ đăng ký đội thi
     */
    public function cancelTeamRegistration(Request $request, $slug)
    {
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        $madoithi = $request->input('madoithi');
        $masinhvien = $request->input('masinhvien'); // Mã sinh viên người yêu cầu huỷ
        
        DB::beginTransaction();
        
        try {
            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
            
            // Kiểm tra cuộc thi chưa bắt đầu thì mới cho huỷ
            if (now()->gte($cuocthi->thoigianbatdau)) {
                return back()->with('error', 'Không thể huỷ đăng ký sau khi cuộc thi đã bắt đầu!');
            }
            
            $doithi = DoiThi::where('madoithi', $madoithi)
                            ->where('macuocthi', $macuocthi)
                            ->firstOrFail();
            
            // Chỉ trưởng đội mới được huỷ đăng ký
            if ($doithi->matruongdoi !== $masinhvien) {
                return back()->with('error', 'Chỉ trưởng đội mới có quyền huỷ đăng ký!');
            }
            
            // Cập nhật trạng thái đội thi
            $doithi->update([
                'trangthai' => 'Cancelled'
            ]);
            
            // Cập nhật trạng thái đăng ký đội thi
            DangKyDoiThi::where('madoithi', $madoithi)
                        ->where('macuocthi', $macuocthi)
                        ->update([
                            'trangthai' => 'Cancelled',
                            'ngayhuy' => now()
                        ]);
            
            DB::commit();
            
            return back()->with('success', 'Đã huỷ đăng ký đội thi thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi huỷ đăng ký đội thi: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra khi huỷ đăng ký: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra mã sinh viên có tồn tại không (API)
     */
    public function checkStudentCode(Request $request)
    {
        $masinhvien = $request->input('student_code');
        $exists = SinhVien::where('masinhvien', $masinhvien)->exists();
        
        return response()->json(['exists' => $exists]);
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
}