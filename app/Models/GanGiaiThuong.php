<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GanGiaiThuong extends Model
{
    use HasFactory;

    protected $table = 'gangiaithuong';
    protected $primaryKey = 'magangiai';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'magangiai',
        'macocau',
        'madangkycanhan',
        'madangkydoi',
        'loaidangky',
        'ladongkang',
        'xephangthucte',
        'nguoigan',        // ✅ Người gán giải
        'ngaygan',         // ✅ Ngày gán
        'trangthai',
        'nguoiduyet',
        'ngayduyet',
        'ghichu',
    ];

    protected $casts = [
        'ladongkang' => 'boolean',
        'xephangthucte' => 'integer',
        'ngaygan' => 'datetime',
        'ngayduyet' => 'datetime',
    ];

    // === Relationships ===
    
    // Cơ cấu giải thưởng
    public function cocaugiaithuong()
    {
        return $this->belongsTo(CoCauGiaiThuong::class, 'macocau', 'macocau');
    }

    // Đăng ký cá nhân
    public function dangkycanhan()
    {
        return $this->belongsTo(DangKyCaNhan::class, 'madangkycanhan', 'madangkycanhan');
    }

    // Đăng ký đội
    public function dangkydoi()
    {
        return $this->belongsTo(DangKyDoiThi::class, 'madangkydoi', 'madangkydoi');
    }

    // ✅ Người gán giải
    public function nguoigan()
    {
        return $this->belongsTo(GiangVien::class, 'nguoigan', 'magiangvien');
    }

    // Người duyệt
    public function nguoiduyet()
    {
        return $this->belongsTo(GiangVien::class, 'nguoiduyet', 'magiangvien');
    }

    // Điểm rèn luyện liên quan
    public function diemrenluyen()
    {
        return $this->hasMany(DiemRenLuyen::class, 'magangiai', 'magangiai');
    }

    // Chi phí liên quan (tiền thưởng)
    public function chiphi()
    {
        return $this->hasMany(ChiPhi::class, 'magangiai', 'magangiai');
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

    public function scopeCaNhan($query)
    {
        return $query->where('loaidangky', 'CaNhan');
    }

    public function scopeDoiNhom($query)
    {
        return $query->where('loaidangky', 'DoiNhom');
    }

    public function scopeDongHang($query)
    {
        return $query->where('ladongkang', true);
    }

    public function scopeByCoCau($query, $maCoCau)
    {
        return $query->where('macocau', $maCoCau);
    }

    public function scopeByApprover($query, $maGiangVien)
    {
        return $query->where('nguoiduyet', $maGiangVien);
    }

    // ✅ Scope theo người gán
    public function scopeByAssigner($query, $maGiangVien)
    {
        return $query->where('nguoigan', $maGiangVien);
    }

    // === Accessors ===
    
    // Lấy thông tin người nhận giải
    public function getNguoiNhanGiaiAttribute()
    {
        if ($this->loaidangky === 'CaNhan' && $this->dangkycanhan) {
            return $this->dangkycanhan->sinhvien;
        }
        
        if ($this->loaidangky === 'DoiNhom' && $this->dangkydoi) {
            return $this->dangkydoi->thanhvien;
        }
        
        return null;
    }

    // Lấy tên người/đội nhận giải
    public function getTenNguoiNhanAttribute()
    {
        if ($this->loaidangky === 'CaNhan' && $this->dangkycanhan) {
            return $this->dangkycanhan->sinhvien->hoten ?? 'N/A';
        }
        
        if ($this->loaidangky === 'DoiNhom' && $this->dangkydoi) {
            return $this->dangkydoi->tendoi ?? 'N/A';
        }
        
        return 'N/A';
    }

    // Lấy thông tin giải thưởng
    public function getThongTinGiaiAttribute()
    {
        if (!$this->cocaugiaithuong) {
            return null;
        }

        return [
            'ten_giai' => $this->cocaugiaithuong->tengiai,
            'tien_thuong' => $this->cocaugiaithuong->tienthuong,
            'giay_khen' => $this->cocaugiaithuong->giaykhen,
            'la_dong_hang' => $this->ladongkang,
            'xep_hang' => $this->xephangthucte,
        ];
    }
}