<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemDanhQR extends Model
{
    use HasFactory;

    protected $table = 'diemdanhqr';
    protected $primaryKey = 'madiemdanh';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madiemdanh',
        'mahoatdong',
        'macuocthi',
        'masinhvien',
        'maqr',
        'thoigiandiemdanh',
        'vitri',
    ];

    protected $casts = [
        'thoigiandiemdanh' => 'datetime',
    ];

    // === Relationships ===
    public function hoatdong()
    {
        return $this->belongsTo(HoatDongHoTro::class, 'mahoatdong', 'mahoatdong');
    }

    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }
}