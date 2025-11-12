<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongViec extends Model
{
    use HasFactory;

    protected $table = 'congviec';
    protected $primaryKey = 'macongviec';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'macongviec',
        'tencongviec',
        'maban',
        'macuocthi',
        'mota',
        'thoigianbatdau',
        'thoigianketthuc',
        'trangthai',
    ];

    protected $casts = [
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
    ];

    // Relationships
    public function ban()
    {
        return $this->belongsTo(Ban::class, 'maban', 'maban');
    }

    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function phancongs()
    {
        return $this->hasMany(PhanCongGiangVien::class, 'macongviec', 'macongviec');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('trangthai', 'Pending');
    }
}