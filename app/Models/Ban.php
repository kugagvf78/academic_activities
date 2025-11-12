<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use HasFactory;

    protected $table = 'ban';
    protected $primaryKey = 'maban';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'maban',
        'tenban',
        'macuocthi',
        'mota',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function congviecs()
    {
        return $this->hasMany(CongViec::class, 'maban', 'maban');
    }

    public function phancongs()
    {
        return $this->hasMany(PhanCongGiangVien::class, 'maban', 'maban');
    }
}