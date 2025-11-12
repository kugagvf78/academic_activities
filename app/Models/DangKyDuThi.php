<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyDuThi extends Model
{
    use HasFactory;

    protected $table = 'dangkyduthi';
    protected $primaryKey = 'madangky';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madangky',
        'macuocthi',
        'masinhvien',
        'madoithi',
        'hinhthucdangky',
        'ngaydangky',
        'trangthai',
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

    public function doithi()
    {
        return $this->belongsTo(DoiThi::class, 'madoithi', 'madoithi');
    }

    public function baithis()
    {
        return $this->hasMany(BaiThi::class, 'madangky', 'madangky');
    }

    public function datgiais()
    {
        return $this->hasMany(DatGiai::class, 'madangky', 'madangky');
    }

    // Scopes
    public function scopeRegistered($query)
    {
        return $query->where('trangthai', 'Registered');
    }

    public function scopeCaNhan($query)
    {
        return $query->where('hinhthucdangky', 'CaNhan');
    }

    public function scopeDoiNhom($query)
    {
        return $query->where('hinhthucdangky', 'DoiNhom');
    }
}