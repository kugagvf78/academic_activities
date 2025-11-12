<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoiThi extends Model
{
    use HasFactory;

    protected $table = 'doithi';
    protected $primaryKey = 'madoithi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madoithi',
        'tendoithi',
        'macuocthi',
        'matruongdoi',
        'sothanhvien',
        'ngaydangky',
        'trangthai',
    ];

    protected $casts = [
        'sothanhvien' => 'integer',
        'ngaydangky' => 'datetime',
    ];

    // === Relationships ===
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function truongdoi()
    {
        return $this->belongsTo(SinhVien::class, 'matruongdoi', 'masinhvien');
    }

    public function dangkyduthis()
    {
        return $this->hasMany(DangKyDuThi::class, 'madoithi', 'madoithi');
    }

    public function thanhviens()
    {
        return $this->hasMany(ThanhVienDoiThi::class, 'madoithi', 'madoithi');
    }

    // === Scope ===
    public function scopeActive($query)
    {
        return $query->where('trangthai', 'Active');
    }
}