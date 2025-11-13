<?php

namespace App\Http\Controllers\Web\Client;

use App\Models\CuocThi;
use App\Models\SinhVien;
use App\Models\DoiThi;
use App\Models\DangKyDuThi;
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
        // Parse slug để lấy macuocthi
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        
        $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
        
        // Kiểm tra trạng thái cuộc thi
        if ($cuocthi->trangthai !== 'Approved' && $cuocthi->trangthai !== 'InProgress') {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi không trong thời gian đăng ký');
        }
        
        // Kiểm tra thời gian đăng ký
        $now = now();
        if ($now->lt($cuocthi->thoigianbatdau) || $now->gt($cuocthi->thoigianketthuc)) {
            return redirect()->route('client.events.show', $slug)
                ->with('error', 'Cuộc thi không trong thời gian đăng ký');
        }
        
        return view('client.events.register', compact('cuocthi', 'slug'));
    }

    /**
     * Xử lý đăng ký cuộc thi
     */
    public function register(Request $request, $slug)
    {
        // Parse slug để lấy macuocthi
        $macuocthi = $this->getMaCuocThiFromSlug($slug);
        
        // Validate dữ liệu
        $validated = $request->validate([
            'type' => 'required|in:individual,team',
            'main_name' => 'required|string|max:255',
            'main_student_code' => 'required|string|max:50',
            'main_email' => 'required|email|max:255',
            'main_phone' => 'required|string|max:20',
            'team_name' => 'required_if:type,team|string|max:255|nullable',
            'members' => 'required_if:type,team|array|nullable',
            'members.*.name' => 'required_with:members|string|max:255',
            'members.*.student_code' => 'required_with:members|string|max:50',
            'members.*.email' => 'required_with:members|email|max:255',
            'note' => 'nullable|string|max:1000',
        ], [
            'main_name.required' => 'Vui lòng nhập họ và tên',
            'main_student_code.required' => 'Vui lòng nhập mã sinh viên',
            'main_email.required' => 'Vui lòng nhập email',
            'main_phone.required' => 'Vui lòng nhập số điện thoại',
            'team_name.required_if' => 'Vui lòng nhập tên đội',
            'members.required_if' => 'Vui lòng thêm thành viên nhóm',
        ]);

        DB::beginTransaction();
        
        try {
            $cuocthi = CuocThi::where('macuocthi', $macuocthi)->firstOrFail();
            
            // Kiểm tra sinh viên chính có tồn tại không
            $sinhvienChinh = SinhVien::where('masinhvien', $validated['main_student_code'])->first();
            
            if (!$sinhvienChinh) {
                return back()->withErrors(['main_student_code' => 'Mã sinh viên không tồn tại trong hệ thống'])
                            ->withInput();
            }

            // Kiểm tra đã đăng ký chưa
            $daDangKy = DangKyDuThi::where('macuocthi', $macuocthi)
                                   ->where('masinhvien', $sinhvienChinh->masinhvien)
                                   ->exists();
            
            if ($daDangKy) {
                return back()->with('error', 'Bạn đã đăng ký cuộc thi này rồi!')
                            ->withInput();
            }

            $madoithi = null;
            
            // Xử lý đăng ký theo nhóm
            if ($validated['type'] === 'team') {
                // Kiểm tra số lượng thành viên
                if (empty($validated['members']) || count($validated['members']) < 1) {
                    return back()->withErrors(['members' => 'Đội thi phải có ít nhất 1 thành viên ngoài trưởng đội'])
                                ->withInput();
                }
                
                // Tạo đội thi
                $madoithi = 'DT' . Str::upper(Str::random(8));
                
                $doithi = DoiThi::create([
                    'madoithi' => $madoithi,
                    'tendoithi' => $validated['team_name'],
                    'macuocthi' => $macuocthi,
                    'matruongdoi' => $sinhvienChinh->masinhvien,
                    'sothanhvien' => count($validated['members']) + 1, // +1 cho trưởng đội
                    'ngaydangky' => now(),
                    'trangthai' => 'Active',
                ]);

                // Thêm thành viên nhóm
                if (!empty($validated['members'])) {
                    foreach ($validated['members'] as $member) {
                        // Kiểm tra mã sinh viên thành viên
                        $sinhvienThanhVien = SinhVien::where('masinhvien', $member['student_code'])->first();
                        
                        if (!$sinhvienThanhVien) {
                            DB::rollBack();
                            return back()->withErrors(['members' => "Mã sinh viên {$member['student_code']} không tồn tại"])
                                        ->withInput();
                        }

                        // Kiểm tra thành viên đã tham gia cuộc thi này chưa
                        $daThamGiaCuocThi = DangKyDuThi::where('macuocthi', $macuocthi)
                                                       ->where('masinhvien', $sinhvienThanhVien->masinhvien)
                                                       ->exists();
                        
                        if ($daThamGiaCuocThi) {
                            DB::rollBack();
                            return back()->withErrors(['members' => "Sinh viên {$member['name']} đã đăng ký cuộc thi này rồi"])
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
                            'vaitro' => 'Member',
                            'ngaythamgia' => now(),
                            'trangthai' => 'Active',
                        ]);
                        
                        // Tạo đăng ký dự thi cho thành viên
                        $madangkyThanhVien = 'DK' . Str::upper(Str::random(8));
                        DangKyDuThi::create([
                            'madangky' => $madangkyThanhVien,
                            'macuocthi' => $macuocthi,
                            'masinhvien' => $sinhvienThanhVien->masinhvien,
                            'madoithi' => $madoithi,
                            'hinhthucdangky' => 'DoiNhom',
                            'ngaydangky' => now(),
                            'trangthai' => 'Registered',
                        ]);
                    }
                }
            }

            // Tạo đăng ký dự thi cho trưởng đội/cá nhân
            $madangky = 'DK' . Str::upper(Str::random(8));
            
            DangKyDuThi::create([
                'madangky' => $madangky,
                'macuocthi' => $macuocthi,
                'masinhvien' => $sinhvienChinh->masinhvien,
                'madoithi' => $madoithi,
                'hinhthucdangky' => $validated['type'] === 'individual' ? 'CaNhan' : 'DoiNhom',
                'ngaydangky' => now(),
                'trangthai' => 'Registered',
            ]);

            DB::commit();
            
            return redirect()->route('client.events.show', $slug)
                           ->with('success', 'Đăng ký cuộc thi thành công! Chúc bạn thi tốt! 🎉');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Lỗi đăng ký cuộc thi: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                        ->withInput();
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