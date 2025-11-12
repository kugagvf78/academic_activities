<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiThi extends Model
{
    use HasFactory;

    protected $table = 'baithi';
    protected $primaryKey = 'mabaithi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mabaithi',
        'madethi',
        'madangky',
        'filebaithi',
        'thoigiannop',
        'trangthai',
    ];

    protected $casts = [
        'thoigiannop' => 'datetime',
    ];

    // Relationships
    public function dethi()
    {
        return $this->belongsTo(DeThi::class, 'madethi', 'madethi');
    }

    public function dangky()
    {
        return $this->belongsTo(DangKyDuThi::class, 'madangky', 'madangky');
    }

    public function ketqua()
    {
        return $this->hasOne(KetQuaThi::class, 'mabaithi', 'mabaithi');
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->where('trangthai', 'Submitted');
    }
}