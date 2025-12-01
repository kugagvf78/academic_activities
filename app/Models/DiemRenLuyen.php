<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemRenLuyen extends Model
{
    use HasFactory;

    protected $table = 'diemrenluyen';
    protected $primaryKey = 'madiemrl';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'madiemrl',
        'masinhvien',
        'macuocthi',
        'mahoatdong',
        'loaihoatdong',
        'diem',
        'mota',
        'ngaycong',
        'magangiai',        // Liên kết với giải thưởng (mới thêm)
    ];

    protected $casts = [
        'diem' => 'decimal:2',
        'ngaycong' => 'datetime',
    ];

    // === Relationships ===
    
    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'masinhvien', 'masinhvien');
    }

    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    public function hoatdong()
    {
        return $this->belongsTo(HoatDongHoTro::class, 'mahoatdong', 'mahoatdong');
    }

    // Liên kết với giải thưởng đã gán (mới)
    public function gangiaithuong()
    {
        return $this->belongsTo(GanGiaiThuong::class, 'magangiai', 'magangiai');
    }

    // === Scopes ===
    
    public function scopeDuthi($query)
    {
        return $query->where('loaihoatdong', 'DuThi');
    }

    public function scopeHotro($query)
    {
        return $query->where('loaihoatdong', 'HoTro');
    }

    public function scopeDatgiai($query)
    {
        return $query->where('loaihoatdong', 'DatGiai');
    }

    // Scope điểm từ giải thưởng (mới)
    public function scopeTuGiaiThuong($query)
    {
        return $query->whereNotNull('magangiai');
    }

    // Scope theo giải thưởng cụ thể (mới)
    public function scopeByGanGiai($query, $maGanGiai)
    {
        return $query->where('magangiai', $maGanGiai);
    }

    // Scope theo sinh viên (mới)
    public function scopeBySinhVien($query, $maSinhVien)
    {
        return $query->where('masinhvien', $maSinhVien);
    }

    // Scope theo cuộc thi (mới)
    public function scopeByCuocThi($query, $maCuocThi)
    {
        return $query->where('macuocthi', $maCuocThi);
    }

    // === Accessors ===
    
    // Kiểm tra điểm có từ giải thưởng không (mới)
    public function getLaTuGiaiThuongAttribute()
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
            'xep_hang' => $this->gangiaithuong->xephangthucte,
            'la_dong_hang' => $this->gangiaithuong->ladongkang,
            'trang_thai' => $this->gangiaithuong->trangthai,
        ];
    }

    // Tạo mô tả tự động từ giải thưởng (helper method - mới)
    public function taoMotaTuGiaiThuong()
    {
        if (!$this->gangiaithuong || !$this->gangiaithuong->cocaugiaithuong) {
            return null;
        }

        $cocau = $this->gangiaithuong->cocaugiaithuong;
        $xephang = $this->gangiaithuong->xephangthucte;
        $dongHang = $this->gangiaithuong->ladongkang ? ' (Đồng hạng)' : '';

        return "Đạt {$cocau->tengiai}{$dongHang} - Xếp hạng {$xephang}";
    }
}