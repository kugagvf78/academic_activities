<?php
// ==========================================
// MODEL MỚI: LichHoc.php
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichHoc extends Model
{
    use HasFactory;

    protected $table = 'lichhoc';
    protected $primaryKey = 'malichhoc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'malichhoc',
        'mamonhoc',
        'malop',
        'magiangvien',
        'thu',
        'tietbatdau',
        'tietketthuc',
        'phonghoc',
        'ngaybatdau',
        'ngayketthuc',
        'ghichu',
    ];

    protected $casts = [
        'tietbatdau' => 'integer',
        'tietketthuc' => 'integer',
        'ngaybatdau' => 'date',
        'ngayketthuc' => 'date',
    ];

    // Relationships
    public function monhoc()
    {
        return $this->belongsTo(MonHoc::class, 'mamonhoc', 'mamonhoc');
    }

    public function lop()
    {
        return $this->belongsTo(Lop::class, 'malop', 'malop');
    }

    public function giangvien()
    {
        return $this->belongsTo(GiangVien::class, 'magiangvien', 'magiangvien');
    }

    public function diemdanhs()
    {
        return $this->hasMany(DiemDanhLichHoc::class, 'malichhoc', 'malichhoc');
    }

    // Scopes
    public function scopeByThu($query, $thu)
    {
        return $query->where('thu', $thu);
    }

    public function scopeByGiangVien($query, $maGiangVien)
    {
        return $query->where('magiangvien', $maGiangVien);
    }

    public function scopeByLop($query, $maLop)
    {
        return $query->where('malop', $maLop);
    }
}