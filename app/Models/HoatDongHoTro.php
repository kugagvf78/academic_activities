<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoatDongHoTro extends Model
{
    use HasFactory;

    // Tên bảng đúng (snake_case)
    protected $table = 'hoatdonghotro';

    // Khóa chính
    protected $primaryKey = 'mahoatdong';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // Các cột có thể fill
    protected $fillable = [
        'mahoatdong',
        'tenhoatdong',
        'macuocthi',
        'loaihoatdong',
        'diemrenluyen',
        'thoigianbatdau',
        'thoigianketthuc',
        'diadiem',
        'mota',
        'soluong', // ← THÊM MỚI
    ];

    // Cast kiểu dữ liệu
    protected $casts = [
        'diemrenluyen' => 'decimal:2',
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
        'soluong' => 'integer', // ← Đảm bảo kiểu int
    ];

    // === Relationships ===
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function dangkyhoatdongs()
    {
        return $this->hasMany(DangKyHoatDong::class, 'mahoatdong', 'mahoatdong');
    }

    public function diemrenluyens()
    {
        return $this->hasMany(DiemRenLuyen::class, 'mahoatdong', 'mahoatdong');
    }

    public function diemdanhqrs()
    {
        return $this->hasMany(DiemDanhQR::class, 'mahoatdong', 'mahoatdong');
    }

    // === Scopes ===
    public function scopeCovu($query)
    {
        return $query->where('loaihoatdong', 'CoVu');
    }

    public function scopeTochuc($query)
    {
        return $query->where('loaihoatdong', 'ToChuc');
    }

    public function scopeHotrokythuat($query)
    {
        return $query->where('loaihoatdong', 'HoTroKyThuat');
    }

    // === Accessors ===
    
    /**
     * Lấy số lượng đã đăng ký
     */
    public function getSoLuongDangKyAttribute()
    {
        return $this->dangkyhoatdongs()->count();
    }

    /**
     * Kiểm tra còn chỗ không
     */
    public function getConChoAttribute()
    {
        return $this->soluong > $this->soLuongDangKy;
    }

    /**
     * Lấy số chỗ còn lại
     */
    public function getChoConLaiAttribute()
    {
        return max(0, $this->soluong - $this->soLuongDangKy);
    }

    /**
     * Lấy phần trăm đã đăng ký
     */
    public function getPhanTramDangKyAttribute()
    {
        if ($this->soluong == 0) return 0;
        return round(($this->soLuongDangKy / $this->soluong) * 100, 1);
    }
}