<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = jwt_user();
        
        // Lấy thống kê cơ bản
        $stats = [
            'total_users' => DB::table('nguoidung')->count(),
            'total_students' => DB::table('sinhvien')->count(),
            'total_teachers' => DB::table('giangvien')->where('is_admin', false)->count(),
            'total_competitions' => DB::table('cuocthi')->count(),
            'total_news' => DB::table('tintuc')->count(),
            'active_users' => DB::table('nguoidung')->where('trangthai', 'Active')->count(),
            'published_news' => DB::table('tintuc')->where('trangthai', 'Published')->count(),
            'ongoing_competitions' => DB::table('cuocthi')
                ->where('trangthai', 'DangDienRa')
                ->count(),
        ];
        
        // Thống kê theo tháng
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyStats = [
            'new_users' => DB::table('nguoidung')
                ->whereMonth('ngaytao', $currentMonth)
                ->whereYear('ngaytao', $currentYear)
                ->count(),
            'new_news' => DB::table('tintuc')
                ->whereMonth('ngaydang', $currentMonth)
                ->whereYear('ngaydang', $currentYear)
                ->count(),
        ];
        
        // Hoạt động gần đây
        $recentActivities = $this->getRecentActivities();
        
        // Cuộc thi sắp diễn ra
        $upcomingCompetitions = DB::table('cuocthi')
            ->where('trangthai', 'ChuanBi')
            ->where('thoigianbatdau', '>', now())
            ->orderBy('thoigianbatdau', 'asc')
            ->limit(5)
            ->get();
        
        // Tin tức mới nhất
        $latestNews = DB::table('tintuc')
            ->where('trangthai', 'Published')
            ->orderBy('ngaydang', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'user', 
            'stats', 
            'monthlyStats',
            'recentActivities',
            'upcomingCompetitions',
            'latestNews'
        ));
    }
    
    /**
     * Lấy hoạt động gần đây
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Người dùng mới đăng ký (7 ngày gần đây)
        $newUsers = DB::table('nguoidung')
            ->where('ngaytao', '>=', now()->subDays(7))
            ->orderBy('ngaytao', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($newUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'icon' => 'fa-user-plus',
                'color' => 'blue',
                'title' => 'Người dùng mới đăng ký',
                'description' => $user->hoten . ' đã đăng ký tài khoản',
                'time' => Carbon::parse($user->ngaytao)->diffForHumans(),
                'timestamp' => $user->ngaytao,
            ];
        }
        
        // Cuộc thi mới
        $newCompetitions = DB::table('cuocthi')
            ->where('ngaytao', '>=', now()->subDays(7))
            ->orderBy('ngaytao', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($newCompetitions as $competition) {
            $activities[] = [
                'type' => 'competition',
                'icon' => 'fa-trophy',
                'color' => 'green',
                'title' => 'Cuộc thi mới',
                'description' => $competition->tencuocthi . ' được tạo',
                'time' => Carbon::parse($competition->ngaytao)->diffForHumans(),
                'timestamp' => $competition->ngaytao,
            ];
        }
        
        // Tin tức mới
        $newNews = DB::table('tintuc')
            ->where('ngaydang', '>=', now()->subDays(7))
            ->where('trangthai', 'Published')
            ->orderBy('ngaydang', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($newNews as $news) {
            $activities[] = [
                'type' => 'news',
                'icon' => 'fa-newspaper',
                'color' => 'purple',
                'title' => 'Tin tức mới',
                'description' => $news->tieude,
                'time' => Carbon::parse($news->ngaydang)->diffForHumans(),
                'timestamp' => $news->ngaydang,
            ];
        }
        
        // Sắp xếp theo thời gian
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($activities, 0, 10);
    }
    
    /**
     * Trang quản lý người dùng
     */
    public function users()
    {
        $user = jwt_user();
        
        return view('admin.users.index', compact('user'));
    }

    /**
     * Trang quản lý tin tức
     */
    public function news()
    {
        $user = jwt_user();
        
        return view('admin.news.index', compact('user'));
    }

    /**
     * Trang quản lý cuộc thi
     */
    public function competitions()
    {
        $user = jwt_user();
        
        return view('admin.competitions.index', compact('user'));
    }
}