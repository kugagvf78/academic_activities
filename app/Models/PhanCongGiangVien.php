<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanCongGiangVien extends Model
{
    use HasFactory;

    protected $table = 'phanconggiangvien';
    protected $primaryKey = 'maphancong';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'maphancong',
        'magiangvien',
        'macongviec',
        'maban',
        'vaitro',
        'ngayphancong',
    ];

    protected $casts = [
        'ngayphancong' => 'datetime',
    ];

    // Relationships
    public function giangvien()
    {
        return $this->belongsTo(GiangVien::class, 'magiangvien', 'magiangvien');
    }

    public function congviec()
    {
        return $this->belongsTo(CongViec::class, 'macongviec', 'macongviec');
    }

    public function ban()
    {
        return $this->belongsTo(Ban::class, 'maban', 'maban');
    }
}