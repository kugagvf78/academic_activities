<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VongThi extends Model
{
    use HasFactory;

    protected $table = 'vongthi';
    protected $primaryKey = 'mavongthi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mavongthi',
        'tenvongthi',
        'macuocthi',
        'thutu',
        'thoigianbatdau',
        'thoigianketthuc',
        'diadiem',
        'mota',
        'trangthai',
    ];

    protected $casts = [
        'thutu' => 'integer',
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
    ];

    // === Relationship ===
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    // === Scopes ===
    public function scopeUpcoming($query)
    {
        return $query->where('trangthai', 'Upcoming');
    }

    public function scopeOrderByThutu($query)
    {
        return $query->orderBy('thutu', 'asc');
    }
}