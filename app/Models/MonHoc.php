<?php

// ==========================================
// MODEL MỚI: MonHoc.php
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    use HasFactory;

    protected $table = 'monhoc';
    protected $primaryKey = 'mamonhoc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mamonhoc',
        'tenmonhoc',
        'sotinchi',
        'mabomon',
        'mota',
    ];

    protected $casts = [
        'sotinchi' => 'integer',
    ];

    // Relationships
    public function bomon()
    {
        return $this->belongsTo(BoMon::class, 'mabomon', 'mabomon');
    }

    public function lichhocs()
    {
        return $this->hasMany(LichHoc::class, 'mamonhoc', 'mamonhoc');
    }
}