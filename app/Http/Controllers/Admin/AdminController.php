<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = jwt_user();
        
        // Lấy thống kê cơ bản
        $stats = [
            'total_users' => \App\Models\NguoiDung::count(),
            'total_students' => \App\Models\SinhVien::count(),
            'total_teachers' => \App\Models\GiangVien::where('is_admin', false)->count(),
            'total_competitions' => \App\Models\CuocThi::count() ?? 0,
        ];
        
        return view('admin.dashboard', compact('user', 'stats'));
    }
}