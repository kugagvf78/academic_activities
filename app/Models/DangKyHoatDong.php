<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyHoatDong extends Model
{
    use HasFactory;

    protected $table = 'dangkyhoatdong';
    protected $primaryKey = 'madangkyhoatdong';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madangkyhoatdong',
        'mahoatdong',
        'masinhvien',
        'ngaydangky',
        'trangthai',
        'diemdanhqr',
        'thoigiandiemdanh',
    ];

    protected $casts = [
        'ngaydangky' => 'datetime',
        'diemdanhqr' => 'boolean',
        'thoigiandiemdanh' => 'datetime',
    ];

    // Relationships
    public function hoatdong()
    {
        return $this->belongsTo(HoatDongHoTro::class, 'mahoatdong', 'mahoatdong');
    }

    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }

    // Scopes
    public function scopeRegistered($query)
    {
        return $query->where('trangthai', 'Registered');
    }

    public function scopeDiemDanh($query)
    {
        return $query->where('diemdanhqr', true);
    }
}