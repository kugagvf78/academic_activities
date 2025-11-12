<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class NguoiDung extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'nguoidung';
    protected $primaryKey = 'manguoidung';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    
    // Map tên cột timestamps
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = 'ngaycapnhat';

    protected $fillable = [
        'manguoidung',
        'tendangnhap',
        'matkhau',
        'hoten',
        'email',
        'sodienthoai',
        'vaitro',
        'trangthai',
    ];

    protected $hidden = [
        'matkhau',
    ];

    protected $casts = [
        'ngaytao' => 'datetime',
        'ngaycapnhat' => 'datetime',
    ];

    // Accessors & Mutators để dùng PascalCase trong code
    public function getMaNguoiDungAttribute()
    {
        return $this->attributes['manguoidung'];
    }

    public function setMaNguoiDungAttribute($value)
    {
        $this->attributes['manguoidung'] = $value;
    }

    public function getTenDangNhapAttribute()
    {
        return $this->attributes['tendangnhap'] ?? null;
    }

    public function setTenDangNhapAttribute($value)
    {
        $this->attributes['tendangnhap'] = $value;
    }

    public function getMatKhauAttribute()
    {
        return $this->attributes['matkhau'] ?? null;
    }

    public function setMatKhauAttribute($value)
    {
        $this->attributes['matkhau'] = $value;
    }

    public function getHoTenAttribute()
    {
        return $this->attributes['hoten'] ?? null;
    }

    public function setHoTenAttribute($value)
    {
        $this->attributes['hoten'] = $value;
    }

    public function getEmailAttribute()
    {
        return $this->attributes['email'] ?? null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value;
    }

    public function getSoDienThoaiAttribute()
    {
        return $this->attributes['sodienthoai'] ?? null;
    }

    public function setSoDienThoaiAttribute($value)
    {
        $this->attributes['sodienthoai'] = $value;
    }

    public function getVaiTroAttribute()
    {
        return $this->attributes['vaitro'] ?? null;
    }

    public function setVaiTroAttribute($value)
    {
        $this->attributes['vaitro'] = $value;
    }

    public function getTrangThaiAttribute()
    {
        return $this->attributes['trangthai'] ?? null;
    }

    public function setTrangThaiAttribute($value)
    {
        $this->attributes['trangthai'] = $value;
    }

    public function getNgayTaoAttribute()
    {
        return $this->attributes['ngaytao'] ?? null;
    }

    public function getNgayCapNhatAttribute()
    {
        return $this->attributes['ngaycapnhat'] ?? null;
    }

    // Laravel sử dụng đúng cột 'matkhau' để xác thực
    public function getAuthPassword()
    {
        return $this->attributes['matkhau'];
    }

    // JWT: xác định ID của người dùng
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Trả về manguoidung
    }

    // JWT: các thông tin bổ sung trong token (nếu cần)
    public function getJWTCustomClaims()
    {
        return [
            'vai_tro' => $this->VaiTro,
            'ho_ten' => $this->HoTen,
        ];
    }

    // Relationships
    public function giangVien()
    {
        return $this->hasOne(GiangVien::class, 'manguoidung', 'manguoidung');
    }

    public function sinhVien()
    {
        return $this->hasOne(SinhVien::class, 'manguoidung', 'manguoidung');
    }

    // Scopes
    public function scopeAdmin($query)
    {
        return $query->where('vaitro', 'Admin');
    }

    public function scopeGiangVien($query)
    {
        return $query->where('vaitro', 'GiangVien');
    }

    public function scopeSinhVien($query)
    {
        return $query->where('vaitro', 'SinhVien');
    }

    public function scopeActive($query)
    {
        return $query->where('trangthai', 'Active');
    }
}