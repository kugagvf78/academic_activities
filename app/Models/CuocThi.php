<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuocThi extends Model
{
    use HasFactory;

    protected $table = 'cuocthi';
    protected $primaryKey = 'macuocthi';
    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = 'ngaycapnhat';

    protected $fillable = [
        'macuocthi',
        'makehoach',
        'tencuocthi',
        'loaicuocthi',
        'mota',
        'mucdich',
        'doituongthamgia',
        'thoigianbatdau',
        'thoigianketthuc',
        'diadiem',
        'soluongthanhvien',
        'hinhthucthamgia',
        'trangthai',
        'dutrukinhphi',
        'chiphithucte',
        'mabomon',
    ];

    protected $casts = [
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
        'soluongthanhvien' => 'integer',
        'dutrukinhphi' => 'decimal:2',
        'chiphithucte' => 'decimal:2',
        'ngaytao' => 'datetime',
        'ngaycapnhat' => 'datetime',
    ];

    // Relationships
    public function bomon()
    {
        return $this->belongsTo(BoMon::class, 'mabomon', 'mabomon');
    }

    public function kehoach()
    {
        return $this->belongsTo(KeHoachCuocThi::class, 'makehoach', 'makehoach');
    }

    public function bans()
    {
        return $this->hasMany(Ban::class, 'macuocthi', 'macuocthi');
    }

    public function congviecs()
    {
        return $this->hasMany(CongViec::class, 'macuocthi', 'macuocthi');
    }

    public function doithis()
    {
        return $this->hasMany(DoiThi::class, 'macuocthi', 'macuocthi');
    }

    public function dangkycanhans()
    {
        return $this->hasMany(DangKyCaNhan::class, 'macuocthi', 'macuocthi');
    }

    public function dangkydoithis()
    {
        return $this->hasMany(DangKyDoiThi::class, 'macuocthi', 'macuocthi');
    }

    public function hoatdonghotros()
    {
        return $this->hasMany(HoatDongHoTro::class, 'macuocthi', 'macuocthi');
    }

    public function dethis()
    {
        return $this->hasMany(DeThi::class, 'macuocthi', 'macuocthi');
    }

    public function datgiais()
    {
        return $this->hasMany(DatGiai::class, 'macuocthi', 'macuocthi');
    }

    public function diemrenluyens()
    {
        return $this->hasMany(DiemRenLuyen::class, 'macuocthi', 'macuocthi');
    }

    public function chiphis()
    {
        return $this->hasMany(ChiPhi::class, 'macuocthi', 'macuocthi');
    }

    public function quyettoans()
    {
        return $this->hasMany(QuyetToan::class, 'macuocthi', 'macuocthi');
    }

    public function tintucs()
    {
        return $this->hasMany(TinTuc::class, 'macuocthi', 'macuocthi');
    }

    public function vongthis()
    {
        return $this->hasMany(VongThi::class, 'macuocthi', 'macuocthi');
    }

    public function diemdanhqrs()
    {
        return $this->hasMany(DiemDanhQR::class, 'macuocthi', 'macuocthi');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('trangthai', 'Approved');
    }

    public function scopeInProgress($query)
    {
        return $query->where('trangthai', 'InProgress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('trangthai', 'Completed');
    }

    public function scopeCaNhan($query)
    {
        return $query->where('hinhthucthamgia', 'CaNhan');
    }

    public function scopeDoiNhom($query)
    {
        return $query->where('hinhthucthamgia', 'DoiNhom');
    }

    public function scopeCaHai($query)
    {
        return $query->where('hinhthucthamgia', 'CaHai');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereRaw('thoigianbatdau > NOW()');
    }

    public function scopeOngoing($query)
    {
        return $query->whereRaw('thoigianbatdau <= NOW() AND thoigianketthuc >= NOW()');
    }

    public function scopePast($query)
    {
        return $query->whereRaw('thoigianketthuc < NOW()');
    }

    // Helper methods
    public function getTotalDangKy()
    {
        return $this->dangkycanhans()->count() + $this->dangkydoithis()->count();
    }

    public function getDangKyCaNhanCount()
    {
        return $this->dangkycanhans()->count();
    }

    public function getDangKyDoiCount()
    {
        return $this->dangkydoithis()->count();
    }

    public function isUpcoming()
    {
        return $this->thoigianbatdau > now();
    }

    public function isOngoing()
    {
        return $this->thoigianbatdau <= now() && $this->thoigianketthuc >= now();
    }

    public function isPast()
    {
        return $this->thoigianketthuc < now();
    }

    public function hasRegistrations()
    {
        return $this->getTotalDangKy() > 0;
    }

    public function canEdit()
    {
        return !$this->hasRegistrations() && !$this->isPast();
    }

    public function canDelete()
    {
        return !$this->hasRegistrations() && $this->isUpcoming();
    }
    
    public function cocaugiaithuong()
    {
        return $this->hasMany(\App\Models\CoCauGiaiThuong::class, 'macuocthi', 'macuocthi');
    }
}