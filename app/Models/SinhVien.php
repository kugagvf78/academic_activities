<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinhVien extends Model
{
    use HasFactory;

    protected $table = 'sinhvien';
    protected $primaryKey = 'masinhvien';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'masinhvien',
        'manguoidung',
        'malop',
        'namnhaphoc',
        'diemrenluyen',
        'trangthai',
    ];

    protected $casts = [
        'namnhaphoc' => 'integer',
        'diemrenluyen' => 'decimal:2',
    ];

    // Accessors & Mutators để dùng PascalCase trong code
    public function getMaSinhVienAttribute()
    {
        return $this->attributes['masinhvien'];
    }

    public function setMaSinhVienAttribute($value)
    {
        $this->attributes['masinhvien'] = $value;
    }

    public function getMaNguoiDungAttribute()
    {
        return $this->attributes['manguoidung'];
    }

    public function setMaNguoiDungAttribute($value)
    {
        $this->attributes['manguoidung'] = $value;
    }

    public function getMaLopAttribute()
    {
        return $this->attributes['malop'] ?? null;
    }

    public function setMaLopAttribute($value)
    {
        $this->attributes['malop'] = $value;
    }

    public function getNamNhapHocAttribute()
    {
        return $this->attributes['namnhaphoc'] ?? null;
    }

    public function setNamNhapHocAttribute($value)
    {
        $this->attributes['namnhaphoc'] = $value;
    }

    public function getDiemRenLuyenAttribute()
    {
        return $this->attributes['diemrenluyen'] ?? null;
    }

    public function setDiemRenLuyenAttribute($value)
    {
        $this->attributes['diemrenluyen'] = $value;
    }

    public function getTrangThaiAttribute()
    {
        return $this->attributes['trangthai'] ?? null;
    }

    public function setTrangThaiAttribute($value)
    {
        $this->attributes['trangthai'] = $value;
    }

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'manguoidung', 'manguoidung');
    }

    public function lop()
    {
        return $this->belongsTo(Lop::class, 'malop', 'malop');
    }

    public function doiThiTruong()
    {
        return $this->hasMany(DoiThi::class, 'matruongdoi', 'masinhvien');
    }

    public function dangKyDuThis()
    {
        return $this->hasMany(DangKyDuThi::class, 'masinhvien', 'masinhvien');
    }

    public function thanhVienDoiThis()
    {
        return $this->hasMany(ThanhVienDoiThi::class, 'masinhvien', 'masinhvien');
    }

    public function dangKyHoatDongs()
    {
        return $this->hasMany(DangKyHoatDong::class, 'masinhvien', 'masinhvien');
    }

    public function diemRenLuyens()
    {
        return $this->hasMany(DiemRenLuyen::class, 'masinhvien', 'masinhvien');
    }

    public function diemDanhQRs()
    {
        return $this->hasMany(DiemDanhQR::class, 'masinhvien', 'masinhvien');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('trangthai', 'Active');
    }
}