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
        'mabomon',
        'tencuocthi',
        'loaicuocthi',
        'namhoc',
        'hocky',
        'mota',
        'mucdich',
        'doituongthamgia',
        'thoigianbatdau',
        'thoigianketthuc',
        'diadiem',
        'soluongthanhvien',
        'hinhthucthamgia',
        'dutrukinhphi',
        'trangthaiduyet',
        'ngaynopkehoach',
        'ngayduyet',
        'nguoinop',
        'nguoiduyet',
        'ghichu',
    ];

    protected $casts = [
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
        'ngaynopkehoach' => 'datetime',
        'ngayduyet' => 'datetime',
        'soluongthanhvien' => 'integer',
        'dutrukinhphi' => 'decimal:2',
    ];

    // Relationships
    public function bomon()
    {
        return $this->belongsTo(BoMon::class, 'mabomon', 'mabomon');
    }

    public function cuocthi()
    {
        return $this->hasOne(CuocThi::class, 'makehoach', 'makehoach');
    }

    public function nguoinop()
    {
        return $this->belongsTo(GiangVien::class, 'nguoinop', 'magiangvien');
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

    public function scopeByNamHoc($query, $namhoc)
    {
        return $query->where('namhoc', $namhoc);
    }

    public function scopeByHocKy($query, $hocky)
    {
        return $query->where('hocky', $hocky);
    }

    // Helper methods
    public function isPending()
    {
        return $this->trangthaiduyet === 'Pending';
    }

    public function isApproved()
    {
        return $this->trangthaiduyet === 'Approved';
    }

    public function isRejected()
    {
        return $this->trangthaiduyet === 'Rejected';
    }

    public function hasCuocThi()
    {
        return $this->cuocthi()->exists();
    }

    public function canEdit()
    {
        return in_array($this->trangthaiduyet, ['Pending', 'Rejected']) && !$this->hasCuocThi();
    }

    public function canDelete()
    {
        return $this->trangthaiduyet !== 'Approved' && !$this->hasCuocThi();
    }

    public function canCreateCuocThi()
    {
        return $this->trangthaiduyet === 'Approved' && !$this->hasCuocThi();
    }
}