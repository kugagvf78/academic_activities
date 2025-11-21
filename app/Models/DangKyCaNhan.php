<?php

// ==========================================
// MODEL MỚI: DangKyCaNhan.php
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyCaNhan extends Model
{
    use HasFactory;

    protected $table = 'dangkycanhan';
    protected $primaryKey = 'madangkycanhan';
    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'ngaydangky';
    const UPDATED_AT = null;

    protected $fillable = [
        'madangkycanhan',
        'macuocthi',
        'masinhvien',
        'trangthai',
        'ghichu',
    ];

    protected $casts = [
        'ngaydangky' => 'datetime',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }

    public function baithis()
    {
        return $this->hasMany(BaiThi::class, 'madangkycanhan', 'madangkycanhan');
    }

    public function datgiais()
    {
        return $this->hasMany(DatGiai::class, 'madangkycanhan', 'madangkycanhan');
    }

    // Scopes
    public function scopeRegistered($query)
    {
        return $query->where('trangthai', 'Registered');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('trangthai', 'Confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('trangthai', 'Cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('trangthai', 'Completed');
    }

    public function scopeByCuocThi($query, $maCuocThi)
    {
        return $query->where('macuocthi', $maCuocThi);
    }

    public function scopeBySinhVien($query, $maSinhVien)
    {
        return $query->where('masinhvien', $maSinhVien);
    }
}
