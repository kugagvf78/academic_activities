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

    // 🧩 Cực kỳ quan trọng
    protected $authPasswordName = 'MatKhau';

    // ✅ Laravel dùng đúng cột MatKhau để xác thực
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    // ✅ JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
