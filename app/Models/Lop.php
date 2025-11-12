<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    use HasFactory;

    protected $table = 'lop';
    protected $primaryKey = 'malop';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'malop',
        'tenlop',
        'nienkhoa',
        'soluongsinhvien',
        'magiangvienchunhiem',
    ];

    protected $casts = [
        'soluongsinhvien' => 'integer',
    ];

    // Relationships
    public function giangvienchunhiem()
    {
        return $this->belongsTo(GiangVien::class, 'magiangvienchunhiem', 'magiangvien');
    }

    public function sinhviens()
    {
        return $this->hasMany(SinhVien::class, 'malop', 'malop');
    }
}