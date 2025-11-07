<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class TaiKhoanNguoiDung extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'TaiKhoanNguoiDung';
    protected $primaryKey = 'MaTaiKhoan';
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

    // 🧩 Laravel cần biết cột mật khẩu tên gì
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    // 🟢 Bắt buộc cho JWT (để có thể sinh token)
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Trả về khóa chính của user
    }

    public function getJWTCustomClaims()
    {
        return []; // Có thể thêm thông tin khác vào token nếu cần
    }
}
