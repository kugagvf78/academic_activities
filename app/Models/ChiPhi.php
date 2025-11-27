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
        'nguoiyeucau',      // Người yêu cầu chi phí
        'ngayyeucau',       // Ngày yêu cầu
        'nguoiduyet',       // Người duyệt
        'ngayduyet',        // Ngày duyệt
        'trangthai',
        'chungtu',
        'ghichu',
    ];

    protected $casts = [
        'dutruchiphi' => 'decimal:2',
        'thuctechi' => 'decimal:2',
        'ngaychi' => 'date',
        'ngayyeucau' => 'date',
        'ngayduyet' => 'date',
    ];

    // Relationships
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    // Người yêu cầu chi phí (giảng viên tạo yêu cầu)
    public function nguoiyeucau()
    {
        return $this->belongsTo(GiangVien::class, 'nguoiyeucau', 'magiangvien');
    }

    // Người duyệt chi phí (trưởng bộ môn)
    public function nguoiduyet()
    {
        return $this->belongsTo(GiangVien::class, 'nguoiduyet', 'magiangvien');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('trangthai', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('trangthai', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('trangthai', 'Rejected');
    }

    // Scope lọc theo người yêu cầu
    public function scopeByRequester($query, $maGiangVien)
    {
        return $query->where('nguoiyeucau', $maGiangVien);
    }

    // Scope lọc theo người duyệt
    public function scopeByApprover($query, $maGiangVien)
    {
        return $query->where('nguoiduyet', $maGiangVien);
    }
}