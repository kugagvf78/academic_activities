<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiangVien extends Model
{
    use HasFactory;

    protected $table = 'giangvien';
    protected $primaryKey = 'magiangvien';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'magiangvien',
        'manguoidung',
        'mabomon',
        'chucvu',
        'hocvi',
        'chuyenmon',
        'is_admin',
    ];

    // Accessors & Mutators để dùng PascalCase trong code
    public function getMaGiangVienAttribute()
    {
        return $this->attributes['magiangvien'];
    }

    public function setMaGiangVienAttribute($value)
    {
        $this->attributes['magiangvien'] = $value;
    }

    public function getMaNguoiDungAttribute()
    {
        return $this->attributes['manguoidung'];
    }

    public function setMaNguoiDungAttribute($value)
    {
        $this->attributes['manguoidung'] = $value;
    }

    public function getMaBoMonAttribute()
    {
        return $this->attributes['mabomon'] ?? null;
    }

    public function setMaBoMonAttribute($value)
    {
        $this->attributes['mabomon'] = $value;
    }

    public function getChucVuAttribute()
    {
        return $this->attributes['chucvu'] ?? null;
    }

    public function setChucVuAttribute($value)
    {
        $this->attributes['chucvu'] = $value;
    }

    public function getHocViAttribute()
    {
        return $this->attributes['hocvi'] ?? null;
    }

    public function setHocViAttribute($value)
    {
        $this->attributes['hocvi'] = $value;
    }

    public function getChuyenMonAttribute()
    {
        return $this->attributes['chuyenmon'] ?? null;
    }

    public function setChuyenMonAttribute($value)
    {
        $this->attributes['chuyenmon'] = $value;
    }

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'manguoidung', 'manguoidung');
    }

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'mabomon', 'mabomon');
    }

    public function boMonTruong()
    {
        return $this->hasMany(BoMon::class, 'matruongbomon', 'magiangvien');
    }

    public function lopChuNhiem()
    {
        return $this->hasMany(Lop::class, 'magiangvienchunhiem', 'magiangvien');
    }

    public function phanCongs()
    {
        return $this->hasMany(PhanCongGiangVien::class, 'magiangvien', 'magiangvien');
    }

    public function keHoachDuyet()
    {
        return $this->hasMany(KeHoachCuocThi::class, 'nguoiduyet', 'magiangvien');
    }

    public function deThiTao()
    {
        return $this->hasMany(DeThi::class, 'nguoitao', 'magiangvien');
    }

    public function ketQuaChamDiem()
    {
        return $this->hasMany(KetQuaThi::class, 'nguoichamdiem', 'magiangvien');
    }

    public function chiPhiDuyet()
    {
        return $this->hasMany(ChiPhi::class, 'nguoiduyet', 'magiangvien');
    }

    public function quyetToanLap()
    {
        return $this->hasMany(QuyetToan::class, 'nguoilap', 'magiangvien');
    }

    public function quyetToanDuyet()
    {
        return $this->hasMany(QuyetToan::class, 'nguoiduyet', 'magiangvien');
    }

    public function tinTucs()
    {
        return $this->hasMany(TinTuc::class, 'tacgia', 'magiangvien');
    }
}