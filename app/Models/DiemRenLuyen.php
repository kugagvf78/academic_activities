<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemRenLuyen extends Model
{
    use HasFactory;

    protected $table = 'diemrenluyen';
    protected $primaryKey = 'madiemrl';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madiemrl',
        'masinhvien',
        'macuocthi',
        'mahoatdong',
        'loaihoatdong',
        'diem',
        'mota',
        'ngaycong',
    ];

    protected $casts = [
        'diem' => 'decimal:2',
        'ngaycong' => 'datetime',
    ];

    // === Relationships ===
    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }

    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function hoatdong()
    {
        return $this->belongsTo(HoatDongHoTro::class, 'mahoatdong', 'mahoatdong');
    }

    // === Scopes ===
    public function scopeDuthi($query)
    {
        return $query->where('loaihoatdong', 'DuThi');
    }

    public function scopeHotro($query)
    {
        return $query->where('loaihoatdong', 'HoTro');
    }

    public function scopeDatgiai($query)
    {
        return $query->where('loaihoatdong', 'DatGiai');
    }
}