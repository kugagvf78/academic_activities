@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('breadcrumb', 'Trang chủ / Dashboard')

@section('content')
<div class="space-y-6">
    
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Xin chào, {{ $user->hoten }}! 👋</h2>
                <p class="text-blue-100">Chào mừng bạn đến với trang quản trị hệ thống</p>
            </div>
            <div class="hidden md:block">
                <i class="fa-solid fa-user-shield text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Tổng người dùng --}}
        <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Tổng người dùng</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['total_users']) }}</h3>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Sinh viên --}}
        <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Sinh viên</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['total_students']) }}</h3>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Giảng viên --}}
        <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Giảng viên</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['total_teachers']) }}</h3>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user-tie text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Cuộc thi --}}
        <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Cuộc thi</p>
                    <h3 class="text-3xl font-bold">{{ number_format($stats['total_competitions']) }}</h3>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            
            <a href="#" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-500 transition">
                    <i class="fa-solid fa-user-plus text-blue-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600">Thêm người dùng</span>
            </a>

            <a href="#" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-green-500 transition">
                    <i class="fa-solid fa-building text-green-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-green-600">Quản lý bộ môn</span>
            </a>

            <a href="#" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-500 transition">
                    <i class="fa-solid fa-trophy text-purple-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600">Tạo cuộc thi</span>
            </a>

            <a href="#" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-gray-200 hover:border-orange-500 hover:bg-orange-50 transition">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-500 transition">
                    <i class="fa-solid fa-chart-bar text-orange-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-orange-600">Xem báo cáo</span>
            </a>

        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Hoạt động gần đây --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Hoạt động gần đây</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Xem tất cả</a>
            </div>
            <div class="space-y-4">
                <div class="flex gap-3 pb-4 border-b">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-plus text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Người dùng mới đăng ký</p>
                        <p class="text-xs text-gray-500 mt-1">Nguyễn Văn A đã đăng ký tài khoản</p>
                        <p class="text-xs text-blue-600 mt-1">5 phút trước</p>
                    </div>
                </div>

                <div class="flex gap-3 pb-4 border-b">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-trophy text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Cuộc thi mới</p>
                        <p class="text-xs text-gray-500 mt-1">Olympic Tin học 2024 được tạo</p>
                        <p class="text-xs text-blue-600 mt-1">1 giờ trước</p>
                    </div>
                </div>

                <div class="flex gap-3 pb-4">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-clipboard-check text-purple-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Kế hoạch được duyệt</p>
                        <p class="text-xs text-gray-500 mt-1">Kế hoạch cuộc thi ICPC đã được phê duyệt</p>
                        <p class="text-xs text-blue-600 mt-1">2 giờ trước</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông báo hệ thống --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Thông báo hệ thống</h3>
                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">3 mới</span>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-exclamation-triangle text-red-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-red-900">3 kế hoạch cần duyệt</p>
                            <p class="text-xs text-red-700 mt-1">Có các kế hoạch cuộc thi đang chờ phê duyệt</p>
                            <a href="#" class="text-xs text-red-600 hover:text-red-700 font-medium mt-2 inline-block">Xem ngay →</a>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-clock text-yellow-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-yellow-900">Cuộc thi sắp diễn ra</p>
                            <p class="text-xs text-yellow-700 mt-1">Olympic Tin học sẽ bắt đầu trong 2 ngày</p>
                            <a href="#" class="text-xs text-yellow-600 hover:text-yellow-700 font-medium mt-2 inline-block">Xem chi tiết →</a>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-info-circle text-blue-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900">Cập nhật hệ thống</p>
                            <p class="text-xs text-blue-700 mt-1">Hệ thống sẽ bảo trì vào 02:00 AM ngày mai</p>
                            <a href="#" class="text-xs text-blue-600 hover:text-blue-700 font-medium mt-2 inline-block">Tìm hiểu thêm →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection