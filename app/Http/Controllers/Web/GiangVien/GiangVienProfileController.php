<?php

namespace App\Http\Controllers\Web\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\GiangVien;
use App\Models\NguoiDung;
use App\Models\KeHoachCuocThi;
use App\Models\PhanCongGiangVien;
use App\Models\DeThi;
use App\Models\KetQuaThi;
use App\Models\ChiPhi;
use App\Models\QuyetToan;
use App\Models\TinTuc;
use Illuminate\Support\Facades\DB;

class GiangVienProfileController extends Controller
{
    public function index()
    {
        $user = jwt_user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }
        
        $giangvien = GiangVien::with(['nguoiDung', 'boMon'])
            ->where('manguoidung', $user->manguoidung)
            ->first();
        
        if (!$giangvien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giảng viên!');
        }
        
        $stats = [
            'tong_cuoc_thi' => PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)->distinct('macongviec')->count(),
            'tong_de_thi' => DeThi::where('nguoitao', $giangvien->magiangvien)->count(),
            'tong_bai_can_cham' => KetQuaThi::where(function($q) use ($giangvien) {
                $q->whereNull('nguoichamdiem')->orWhere('nguoichamdiem', $giangvien->magiangvien);
            })->whereNull('diem')->count(),
            'tong_phan_cong' => PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)->count(),
        ];
        
        $phanCongGanDay = PhanCongGiangVien::with(['ban'])
            ->where('magiangvien', $giangvien->magiangvien)
            ->orderBy('ngayphancong', 'desc')
            ->take(5)
            ->get();
        
        return view('giangvien.profile.index', compact('giangvien', 'stats', 'phanCongGanDay'));
    }
    
    public function updateInfo(Request $request)
    {
        $user = jwt_user();
        
        $validated = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|max:255',
            'SoDienThoai' => 'nullable|string|max:15',
            'ChucVu' => 'nullable|string|max:100',
            'HocVi' => 'nullable|string|max:100',
            'ChuyenMon' => 'nullable|string',
        ]);
        
        NguoiDung::where('manguoidung', $user->manguoidung)->update([
            'hoten' => $validated['HoTen'],
            'email' => $validated['Email'],
            'sodienthoai' => $validated['SoDienThoai'] ?? null,
        ]);
        
        GiangVien::where('manguoidung', $user->manguoidung)->update([
            'chucvu' => $validated['ChucVu'] ?? null,
            'hocvi' => $validated['HocVi'] ?? null,
            'chuyenmon' => $validated['ChuyenMon'] ?? null,
        ]);
        
        return redirect()->route('giangvien.profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }
    
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'Avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = jwt_user();
        $nguoidung = NguoiDung::where('manguoidung', $user->manguoidung)->first();
        
        if ($nguoidung->avatar && Storage::disk('public')->exists($nguoidung->avatar)) {
            Storage::disk('public')->delete($nguoidung->avatar);
        }
        
        $path = $request->file('Avatar')->store('avatars', 'public');
        $nguoidung->update(['avatar' => $path]);
        
        return back()->with('success', 'Cập nhật avatar thành công!');
    }
    
    public function danhSachKeHoach()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $kehoachList = KeHoachCuocThi::with(['cuocthi', 'nguoiduyet'])
            ->whereHas('cuocthi', function($q) use ($giangvien) {
                $q->where('mabomon', $giangvien->mabomon);
            })
            ->orderBy('ngaynopkehoach', 'desc')
            ->paginate(10);
        
        return view('giangvien.profile.kehoach.index', compact('kehoachList', 'giangvien'));
    }
    
    public function danhSachDeThi()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $dethiList = DeThi::with(['cuocthi'])
            ->where('nguoitao', $giangvien->magiangvien)
            ->orderBy('ngaytao', 'desc')
            ->paginate(10);
        
        return view('giangvien.profile.dethi.index', compact('dethiList', 'giangvien'));
    }
    
    public function danhSachBaiCanCham()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        // Lấy danh sách Ban mà GV tham gia
        $danhSachBan = PhanCongGiangVien::where('magiangvien', $giangvien->magiangvien)
            ->pluck('maban')
            ->unique()
            ->toArray();
        
        // Lấy TOÀN BỘ cuộc thi của các Ban đó
        $danhSachCuocThi = DB::table('congviec as cv')
            ->whereIn('cv.maban', $danhSachBan)
            ->pluck('cv.macuocthi')
            ->unique()
            ->toArray();
        
        // Query danh sách bài thi cần chấm (chưa có điểm)
        $ketquaList = DB::table('ketquathi as kq')
            ->join('baithi as bt', 'kq.mabaithi', '=', 'bt.mabaithi')
            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
            ->leftJoin('dangkycanhan as dkcn', 'bt.madangkycanhan', '=', 'dkcn.madangkycanhan')
            ->leftJoin('dangkydoithi as dkdt', 'bt.madangkydoi', '=', 'dkdt.madangkydoi')
            ->leftJoin('sinhvien as sv', 'dkcn.masinhvien', '=', 'sv.masinhvien')
            ->leftJoin('doithi as d', 'dkdt.madoithi', '=', 'd.madoithi')
            ->leftJoin('nguoidung as nd', 'sv.manguoidung', '=', 'nd.manguoidung')
            ->leftJoin('cuocthi as ct', 'dt.macuocthi', '=', 'ct.macuocthi')
            ->whereIn('dt.macuocthi', $danhSachCuocThi)
            ->whereNull('kq.diem') // Chỉ lấy bài chưa có điểm
            ->select(
                'kq.maketqua',
                'kq.mabaithi',
                'kq.nguoichamdiem',
                'kq.ngaychamdiem',
                'bt.loaidangky',
                'bt.thoigiannop',
                'dt.tendethi',
                'ct.macuocthi',
                'ct.tencuocthi',
                'sv.masinhvien',
                'nd.hoten as ten_sinhvien',
                'd.madoithi',
                'd.tendoithi'
            )
            ->orderBy('bt.thoigiannop', 'asc') // Ưu tiên bài nộp sớm
            ->paginate(10);
        
        return view('giangvien.profile.chamdiem.index', compact('ketquaList', 'giangvien'));
    }
    
    public function danhSachPhanCong()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $phanCongList = PhanCongGiangVien::with(['ban'])
            ->where('magiangvien', $giangvien->magiangvien)
            ->orderBy('ngayphancong', 'desc')
            ->paginate(10);
        
        return view('giangvien.profile.phancong.index', compact('phanCongList', 'giangvien'));
    }
    
    public function danhSachChiPhi()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $chiphiList = ChiPhi::with(['cuocthi'])
            ->where(function($q) use ($giangvien) {
                $q->where('nguoiduyet', $giangvien->magiangvien)->orWhereNull('nguoiduyet');
            })
            ->orderBy('ngaychi', 'desc')
            ->paginate(10);
        
        return view('giangvien.profile.chiphi.index', compact('chiphiList', 'giangvien'));
    }
    
    public function danhSachQuyetToan()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $quyettoanList = QuyetToan::with(['cuocthi', 'nguoilap', 'nguoiduyet'])
            ->where(function($q) use ($giangvien) {
                $q->where('nguoilap', $giangvien->magiangvien)->orWhere('nguoiduyet', $giangvien->magiangvien);
            })
            ->orderBy('ngayquyettoan', 'desc')
            ->paginate(10);
        
        return view('giangvien.profile.quyettoan.index', compact('quyettoanList', 'giangvien'));
    }
    
    public function danhSachTinTuc()
    {
        $user = jwt_user();
        $giangvien = GiangVien::where('manguoidung', $user->manguoidung)->firstOrFail();
        
        $tintucList = TinTuc::with(['cuocthi'])
            ->where('tacgia', $giangvien->magiangvien)
            ->orderBy('ngaydang', 'desc')
            ->paginate(10);
        
        return view('giangvien.profile.tintuc.index', compact('tintucList', 'giangvien'));
    }
}