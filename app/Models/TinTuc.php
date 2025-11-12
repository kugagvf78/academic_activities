<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TinTuc extends Model
{
    use HasFactory;

    protected $table = 'tintuc';
    protected $primaryKey = 'matintuc';
    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'ngaydang';
    const UPDATED_AT = 'ngaycapnhat';

    protected $fillable = [
        'matintuc',
        'tieude',
        'noidung',
        'macuocthi',
        'loaitin',
        'hinhanh',
        'tacgia',
        'luotxem',
        'trangthai',
    ];

    protected $casts = [
        'luotxem' => 'integer',
        'ngaydang' => 'datetime',
        'ngaycapnhat' => 'datetime',
    ];

    // === Relationships ===
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function tacgia()
    {
        return $this->belongsTo(GiangVien::class, 'tacgia', 'magiangvien');
    }

    // === Scopes ===
    public function scopePublished($query)
    {
        return $query->where('trangthai', 'Published');
    }

    public function scopeThongbao($query)
    {
        return $query->where('loaitin', 'ThongBao');
    }

    public function scopeTintuc($query)
    {
        return $query->where('loaitin', 'TinTuc');
    }

    public function scopeSukien($query)
    {
        return $query->where('loaitin', 'SuKien');
    }

    // === Method ===
    public function incrementViews()
    {
        $this->increment('luotxem');
    }
}