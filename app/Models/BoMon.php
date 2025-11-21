<?php

// ==========================================
// MODEL CẬP NHẬT: BoMon.php (thêm relationship MonHoc)
// ==========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoMon extends Model
{
    use HasFactory;

    protected $table = 'bomon';
    protected $primaryKey = 'mabomon';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mabomon',
        'tenbomon',
        'matruongbomon',
        'mota',
    ];

    // Relationships
    public function truongbomon()
    {
        return $this->belongsTo(GiangVien::class, 'matruongbomon', 'magiangvien');
    }

    public function giangviens()
    {
        return $this->hasMany(GiangVien::class, 'mabomon', 'mabomon');
    }

    public function cuocthis()
    {
        return $this->hasMany(CuocThi::class, 'mabomon', 'mabomon');
    }

    // CẬP NHẬT: Thêm relationship mới
    public function monhocs()
    {
        return $this->hasMany(MonHoc::class, 'mabomon', 'mabomon');
    }
}