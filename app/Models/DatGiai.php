<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatGiai extends Model
{
    use HasFactory;

    protected $table = 'datgiai';
    protected $primaryKey = 'madatgiai';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madatgiai',
        'macuocthi',
        'madangky',
        'tengiai',
        'giaithuong',
        'diemrenluyen',
        'ngaytrao',
    ];

    protected $casts = [
        'diemrenluyen' => 'decimal:2',
        'ngaytrao' => 'datetime',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function dangky()
    {
        return $this->belongsTo(DangKyDuThi::class, 'madangky', 'madangky');
    }
}