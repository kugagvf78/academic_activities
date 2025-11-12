<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class NguoiDung extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'id';
    public $timestamps = false; // vì bạn dùng ngay_tao / ngay_cap_nhat thay vì created_at / updated_at

    protected $fillable = [
        'ten_dang_nhap',
        'mat_khau',
        'ho_ten',
        'email',
        'so_dien_thoai',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
    ];

    protected $hidden = ['mat_khau'];

    // Laravel dùng đúng cột 'mat_khau' để xác thực
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    // Tự động cập nhật cột 'ngay_cap_nhat' khi sửa dữ liệu
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->ngay_cap_nhat = now();
        });
    }

    // JWT: xác định ID của người dùng
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // JWT: các thông tin bổ sung trong token (nếu cần)
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getTenDangNhapAttribute($value)
    {
        return $value ?? $this->attributes['ten_dang_nhap'] ?? 'User';
    }
}
