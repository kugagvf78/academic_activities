<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiPhi extends Model
{
    use HasFactory;

    protected $table = 'chiphi';
    protected $primaryKey = 'machiphi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'machiphi',
        'macuocthi',
        'tenkhoanchi',
        'dutruchiphi',
        'thuctechi',
        'ngaychi',
        'nguoiduyet',
        'trangthai',
        'chungtu',
        'ghichu',
    ];

    protected $casts = [
        'dutruchiphi' => 'decimal:2',
        'thuctechi' => 'decimal:2',
        'ngaychi' => 'date',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function nguoiduyet()
    {
        return $this->belongsTo(GiangVien::class, 'nguoiduyet', 'magiangvien');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('trangthai', 'Pending');
    }
}