<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaThi extends Model
{
    use HasFactory;

    protected $table = 'ketquathi';
    protected $primaryKey = 'maketqua';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'maketqua',
        'mabaithi',
        'diem',
        'xephang',
        'giaithuong',
        'nhanxet',
        'ngaychamdiem',
        'nguoichamdiem',
    ];

    protected $casts = [
        'diem' => 'decimal:2',
        'xephang' => 'integer',
        'ngaychamdiem' => 'datetime',
    ];

    // Relationships
    public function baithi()
    {
        return $this->belongsTo(BaiThi::class, 'mabaithi', 'mabaithi');
    }

    public function nguoichamdiem()
    {
        return $this->belongsTo(GiangVien::class, 'nguoichamdiem', 'magiangvien');
    }
}