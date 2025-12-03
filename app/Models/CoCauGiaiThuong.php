<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoCauGiaiThuong extends Model
{
    use HasFactory;

    protected $table = 'cocaugiaithuong';
    protected $primaryKey = 'macocau';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'macocau',
        'macuocthi',
        'tengiai',
        'soluong',
        'tienthuong',
        'giaykhen',
        'chophepdonghang',    // Sửa từ chophepdongkang
        'ghichudonghang',     // Sửa từ ghichudongkang
        'ghichu',
        'trangthai',
        'ngaytao',
    ];

    protected $casts = [
        'soluong' => 'integer',
        'tienthuong' => 'decimal:2',
        'giaykhen' => 'boolean',
        'chophepdonghang' => 'boolean',  // Sửa từ chophepdongkang
        'ngaytao' => 'datetime',
    ];

    // === Relationships ===
    
    // Cuộc thi
    public function cuocthi()
    {
        return $this->belongsTo(CuocThi::class, 'macuocthi', 'macuocthi');
    }

    // Các giải thưởng đã gán
    public function gangiaithuong()
    {
        return $this->hasMany(GanGiaiThuong::class, 'macocau', 'macocau');
    }

    // === Scopes ===
    
    public function scopeActive($query)
    {
        return $query->where('trangthai', 'Active');
    }

    public function scopeInactive($query)
    {
        return $query->where('trangthai', 'Inactive');
    }

    public function scopeByCuocThi($query, $maCuocThi)
    {
        return $query->where('macuocthi', $maCuocThi);
    }

    public function scopeCoTienThuong($query)
    {
        return $query->where('tienthuong', '>', 0);
    }

    public function scopeChoDongHang($query)
    {
        return $query->where('chophepdonghang', true);  // Sửa từ chophepdongkang
    }

    // === Accessors & Mutators ===
    
    // Kiểm tra còn slot giải không
    public function getConSlotAttribute()
    {
        $daSuDung = $this->gangiaithuong()
            ->whereIn('trangthai', ['Pending', 'Approved'])
            ->count();
        
        return $this->chophepdonghang ? true : ($daSuDung < $this->soluong);  // Sửa từ chophepdongkang
    }

    // Số slot đã sử dụng
    public function getSoDaGanAttribute()
    {
        return $this->gangiaithuong()
            ->whereIn('trangthai', ['Pending', 'Approved'])
            ->count();
    }

    // Số slot còn lại
    public function getSoConLaiAttribute()
    {
        if ($this->chophepdonghang) {  // Sửa từ chophepdongkang
            return null; // Không giới hạn
        }
        return max(0, $this->soluong - $this->so_da_gan);
    }
}