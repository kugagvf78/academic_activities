<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoanNguoiDung extends Authenticatable
{
    use Notifiable;

    protected $table = 'TaiKhoanNguoiDung';
    protected $primaryKey = 'MaTaiKhoan';
    public $timestamps = false; // vì bảng không có created_at, updated_at

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
        'NgayCapNhat'
    ];

    protected $hidden = ['MatKhau'];

    // Laravel mặc định dùng 'password' => ta override lại
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}
