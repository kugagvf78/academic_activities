<?php

// ==========================================
// MODEL CẬP NHẬT: BaiThi.php
// ==========================================

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
        'madangkycanhan',
        'madangkydoi',
        'loaidangky',
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

    // CẬP NHẬT: Thay đổi relationship
    public function dangkycanhan()
    {
        return $this->belongsTo(DangKyCaNhan::class, 'madangkycanhan', 'madangkycanhan');
    }

    public function dangkydoi()
    {
        return $this->belongsTo(DangKyDoiThi::class, 'madangkydoi', 'madangkydoi');
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

    public function scopeCaNhan($query)
    {
        return $query->where('loaidangky', 'CaNhan');
    }

    public function scopeDoiNhom($query)
    {
        return $query->where('loaidangky', 'DoiNhom');
    }

    // Helper methods
    public function getDangKy()
    {
        if ($this->loaidangky === 'CaNhan') {
            return $this->dangkycanhan;
        }
        return $this->dangkydoi;
    }

    public function getSinhViens()
    {
        if ($this->loaidangky === 'CaNhan') {
            return collect([$this->dangkycanhan->sinhvien]);
        }
        return $this->dangkydoi->doithi->thanhviens->pluck('sinhvien');
    }
}
