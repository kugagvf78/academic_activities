<?php
// ==========================================
// MODEL MỚI: DangKyDoiThi.php
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DangKyDoiThi extends Model
{
    use HasFactory;

    protected $table = 'dangkydoithi';
    protected $primaryKey = 'madangkydoi';
    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'ngaydangky';
    const UPDATED_AT = null;

    protected $fillable = [
        'madangkydoi',
        'macuocthi',
        'madoithi',
        'trangthai',
        'ghichu',
    ];

    protected $casts = [
        'ngaydangky' => 'datetime',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function doithi()
    {
        return $this->belongsTo(DoiThi::class, 'madoithi', 'madoithi');
    }

    public function baithis()
    {
        return $this->hasMany(BaiThi::class, 'madangkydoi', 'madangkydoi');
    }

    public function datgiais()
    {
        return $this->hasMany(DatGiai::class, 'madangkydoi', 'madangkydoi');
    }

    // Scopes
    public function scopeRegistered($query)
    {
        return $query->where('trangthai', 'Registered');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('trangthai', 'Confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('trangthai', 'Cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('trangthai', 'Completed');
    }

    public function scopeByCuocThi($query, $maCuocThi)
    {
        return $query->where('macuocthi', $maCuocThi);
    }

    public function scopeByDoiThi($query, $maDoiThi)
    {
        return $query->where('madoithi', $maDoiThi);
    }
}