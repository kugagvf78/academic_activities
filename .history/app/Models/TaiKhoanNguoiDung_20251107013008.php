<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TaiKhoanNguoiDung extends Authenticatable
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

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}
