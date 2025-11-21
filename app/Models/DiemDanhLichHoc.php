<?php
// ==========================================
// MODEL MỚI: DiemDanhLichHoc.php
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemDanhLichHoc extends Model
{
    use HasFactory;

    protected $table = 'diemdanhlichhoc';
    protected $primaryKey = 'madiemdanh';
    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'thoigiandiemdanh';
    const UPDATED_AT = null;

    protected $fillable = [
        'madiemdanh',
        'malichhoc',
        'masinhvien',
        'ngayhoc',
        'trangthai',
        'ghichu',
    ];

    protected $casts = [
        'ngayhoc' => 'date',
        'thoigiandiemdanh' => 'datetime',
    ];

    // Relationships
    public function lichhoc()
    {
        return $this->belongsTo(LichHoc::class, 'malichhoc', 'malichhoc');
    }

    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }

    // Scopes
    public function scopeCoMat($query)
    {
        return $query->where('trangthai', 'CoMat');
    }

    public function scopeVangMat($query)
    {
        return $query->where('trangthai', 'VangMat');
    }

    public function scopeVangCoPhep($query)
    {
        return $query->where('trangthai', 'VangCoPhep');
    }

    public function scopeDiTre($query)
    {
        return $query->where('trangthai', 'DiTre');
    }

    public function scopeByNgay($query, $ngay)
    {
        return $query->whereDate('ngayhoc', $ngay);
    }
}
