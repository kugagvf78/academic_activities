<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeThi extends Model
{
    use HasFactory;

    protected $table = 'dethi';
    protected $primaryKey = 'madethi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madethi',
        'tendethi',
        'macuocthi',
        'loaidethi',
        'filedethi',
        'thoigianlambai',
        'diemtoida',
        'ngaytao',
        'nguoitao',
        'trangthai',
    ];

    protected $casts = [
        'thoigianlambai' => 'integer',
        'diemtoida' => 'decimal:2',
        'ngaytao' => 'datetime',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function nguoitao()
    {
        return $this->belongsTo(GiangVien::class, 'nguoitao', 'magiangvien');
    }

    public function baithis()
    {
        return $this->hasMany(BaiThi::class, 'madethi', 'madethi');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('trangthai', 'Draft');
    }
}