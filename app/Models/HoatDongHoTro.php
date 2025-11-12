<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoatDongHoTro extends Model
{
    use HasFactory;

    // SỬA LỖI: "hoatdonghot ro" → "hoatdonghotro"
    protected $table = 'hoatdonghotro';
    protected $primaryKey = 'mahoatdong';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mahoatdong',
        'tenhoatdong',
        'macuocthi',
        'loaihoatdong',
        'diemrenluyen',
        'thoigianbatdau',
        'thoigianketthuc',
        'diadiem',
        'mota',
    ];

    protected $casts = [
        'diemrenluyen' => 'decimal:2',
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
    ];

    // === Relationships ===
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function dangkyhoatdongs()
    {
        return $this->hasMany(DangKyHoatDong::class, 'mahoatdong', 'mahoatdong');
    }

    public function diemrenluyens()
    {
        return $this->hasMany(DiemRenLuyen::class, 'mahoatdong', 'mahoatdong');
    }

    public function diemdanhqrs()
    {
        return $this->hasMany(DiemDanhQR::class, 'mahoatdong', 'mahoatdong');
    }

    // === Scopes ===
    public function scopeCovu($query)
    {
        return $query->where('loaihoatdong', 'CoVu');
    }

    public function scopeTochuc($query)
    {
        return $query->where('loaihoatdong', 'ToChuc');
    }

    public function scopeHotrokythuat($query)
    {
        return $query->where('loaihoatdong', 'HoTroKyThuat');
    }
}