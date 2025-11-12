<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyetToan extends Model
{
    use HasFactory;

    protected $table = 'quyettoan';
    protected $primaryKey = 'maquyettoan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'maquyettoan',
        'macuocthi',
        'tongdutru',
        'tongthucte',
        'chenhlech',
        'ngayquyettoan',
        'nguoilap',
        'nguoiduyet',
        'trangthai',
        'filequyettoan',
        'ghichu',
    ];

    protected $casts = [
        'tongdutru' => 'decimal:2',
        'tongthucte' => 'decimal:2',
        'chenhlech' => 'decimal:2',
        'ngayquyettoan' => 'date',
    ];

    // === Relationships ===
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function nguoilap()
    {
        return $this->belongsTo(GiangVien::class, 'nguoilap', 'magiangvien');
    }

    public function nguoiduyet()
    {
        return $this->belongsTo(GiangVien::class, 'nguoiduyet', 'magiangvien');
    }

    // === Scope ===
    public function scopeDraft($query)
    {
        return $query->where('trangthai', 'Draft');
    }
}