<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoanNguoiDung extends Authenticatable
{
    use HasFactory, Notifiable;

    // ⚙️ Tên bảng trong PostgreSQL (có chữ hoa nên phải đúng như vậy)
    protected $table = 'TaiKhoanNguoiDung';

    // ⚙️ Khóa chính của bảng
    protected $primaryKey = 'MaTaiKhoan';

    // ⚙️ Tắt timestamps tự động (vì bạn không dùng created_at, updated_at)
    public $timestamps = false;

    protected $fillable = [
        'TenDangNhap',
        'MatKhau',
        'Email',
        'SoDienThoai',
        'TrangThaiHoatDong',
        'LanDangNhapCuoi',
        'NguoiTao',
        'NgayTao',
        'NguoiCapNhat',
        'NgayCapNhat',
    ];

    protected $hidden = ['MatKhau'];

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}
