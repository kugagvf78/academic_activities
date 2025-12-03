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
        'magangiai',        // Liên kết với giải thưởng (mới thêm)
    ];

    protected $casts = [
        'dutruchiphi' => 'decimal:2',
        'thuctechi' => 'decimal:2',
        'ngaychi' => 'date',
        'ngayyeucau' => 'date',
        'ngayduyet' => 'date',
    ];

    // === Relationships ===
    
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

    // Liên kết với giải thưởng đã gán (mới)
    public function gangiaithuong()
    {
        return $this->belongsTo(GanGiaiThuong::class, 'magangiai', 'magangiai');
    }

    // === Scopes ===
    
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

    // Scope chi phí liên quan đến giải thưởng (mới)
    public function scopeTienThuong($query)
    {
        return $query->whereNotNull('magangiai');
    }

    // Scope chi phí không phải tiền thưởng (mới)
    public function scopeKhongPhaiTienThuong($query)
    {
        return $query->whereNull('magangiai');
    }

    // Scope theo giải thưởng cụ thể (mới)
    public function scopeByGanGiai($query, $maGanGiai)
    {
        return $query->where('magangiai', $maGanGiai);
    }

    // === Accessors ===
    
    // Kiểm tra có phải chi phí tiền thưởng không (mới)
    public function getLaTienThuongAttribute()
    {
        return !is_null($this->magangiai);
    }

    // Lấy thông tin giải thưởng nếu có (mới)
    public function getThongTinGiaiThuongAttribute()
    {
        if (!$this->gangiaithuong) {
            return null;
        }

        return [
            'ten_giai' => $this->gangiaithuong->cocaugiaithuong->tengiai ?? 'N/A',
            'nguoi_nhan' => $this->gangiaithuong->ten_nguoi_nhan,
            'loai_dang_ky' => $this->gangiaithuong->loaidangky,
            'tien_thuong' => $this->gangiaithuong->cocaugiaithuong->tienthuong ?? 0,
        ];
    }
}