<?php

// ==========================================
// MODEL CẬP NHẬT: DatGiai.php
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatGiai extends Model
{
    use HasFactory;

    protected $table = 'datgiai';
    protected $primaryKey = 'madatgiai';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madatgiai',
        'macuocthi',
        'madangkycanhan',
        'madangkydoi',
        'loaidangky',
        'tengiai',
        'giaithuong',
        'diemrenluyen',
        'ngaytrao',
    ];

    protected $casts = [
        'diemrenluyen' => 'decimal:2',
        'ngaytrao' => 'datetime',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
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

    // Scopes
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