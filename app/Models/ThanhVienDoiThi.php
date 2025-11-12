<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVienDoiThi extends Model
{
    use HasFactory;

    protected $table = 'thanhviendoithi';
    protected $primaryKey = 'mathanhvien';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mathanhvien',
        'madoithi',
        'masinhvien',
        'vaitro',
        'ngaythamgia',
    ];

    protected $casts = [
        'ngaythamgia' => 'datetime',
    ];

    // === Relationships ===
    public function doithi()
    {
        return $this->belongsTo(DoiThi::class, 'madoithi', 'madoithi');
    }

    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }

    // === Scopes ===
    public function scopeTruongdoi($query)
    {
        return $query->where('vaitro', 'TruongDoi');
    }

    public function scopeThanhvien($query)
    {
        return $query->where('vaitro', 'ThanhVien');
    }
}