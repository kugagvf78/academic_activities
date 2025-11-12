<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeHoachCuocThi extends Model
{
    use HasFactory;

    protected $table = 'kehoachcuocthi';
    protected $primaryKey = 'makehoach';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'makehoach',
        'macuocthi',
        'namhoc',
        'hocky',
        'trangthaiduyet',
        'ngaynopkehoach',
        'ngayduyet',
        'nguoiduyet',
        'ghichu',
    ];

    protected $casts = [
        'ngaynopkehoach' => 'datetime',
        'ngayduyet' => 'datetime',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function nguoiduyet()
    {
        return $this->belongsTo(GiangVien::class, 'nguoiduyet', 'magiangvien');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('trangthaiduyet', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('trangthaiduyet', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('trangthaiduyet', 'Rejected');
    }
}