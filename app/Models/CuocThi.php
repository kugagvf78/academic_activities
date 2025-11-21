<?php

// ==========================================
// MODEL CẬP NHẬT: CuocThi.php
// ==========================================

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

    public function kehoachs()
    {
        return $this->hasMany(KeHoachCuocThi::class, 'macuocthi', 'macuocthi');
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

    // CẬP NHẬT: Thay đổi từ dangkyduthis sang 2 relationship mới
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
    public function scopeDraft($query)
    {
        return $query->where('trangthai', 'Draft');
    }

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

    // CẬP NHẬT: Thêm scope cho hình thức tham gia
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
}
