<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\NguoiDung;
use App\Models\GiangVien;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Tạo mã người dùng
            $maNguoiDung = 'ND' . str_pad(NguoiDung::count() + 1, 6, '0', STR_PAD_LEFT);
            
            // Tạo người dùng admin
            $admin = NguoiDung::create([
                'manguoidung' => $maNguoiDung,
                'tendangnhap' => 'admin', // Tên đăng nhập là "admin"
                'matkhau' => Hash::make('123456'), // Mật khẩu mặc định
                'hoten' => 'Administrator',
                'email' => 'admin@huit.edu.vn',
                'sodienthoai' => '0123456789',
                'vaitro' => 'GiangVien', // Vai trò là GiangVien
                'trangthai' => 'Active',
            ]);

            // Tạo bản ghi giảng viên với is_admin = true
            GiangVien::create([
                'magiangvien' => 'ADMIN',
                'manguoidung' => $maNguoiDung,
                'mabomon' => null, // Có thể để null hoặc gán bộ môn
                'is_admin' => true, // Đánh dấu là admin
            ]);

            DB::commit();
            
            $this->command->info('✅ Tạo tài khoản Admin thành công!');
            $this->command->info('📧 Email: admin@huit.edu.vn');
            $this->command->info('🔑 Username: admin');
            $this->command->info('🔒 Password: 123456');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Lỗi: ' . $e->getMessage());
        }
    }
}