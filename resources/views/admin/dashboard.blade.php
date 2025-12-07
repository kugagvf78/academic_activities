@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <nav class="text-sm">
        <ol class="flex items-center gap-2">
            <li class="text-slate-600">Dashboard</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="space-y-6">
    
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Xin chào, {{ $user->hoten }}! 👋</h2>
                <p class="text-blue-100">Chào mừng bạn đến với trang quản trị hệ thống</p>
                <p class="text-blue-200 text-sm mt-2">
                    <i class="fa-regular fa-calendar mr-1"></i>
                    {{ now()->format('l, d/m/Y') }}
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fa-solid fa-chart-line text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- Main Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Tổng người dùng --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium mb-1">Tổng người dùng</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_users']) }}</h3>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fa-solid fa-arrow-up mr-1"></i>
                        +{{ number_format($monthlyStats['new_users']) }} tháng này
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Sinh viên --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium mb-1">Sinh viên</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_students']) }}</h3>
                    <p class="text-xs text-slate-500 mt-2">
                        {{ number_format(($stats['total_students'] / max($stats['total_users'], 1)) * 100, 1) }}% tổng số
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Giảng viên --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium mb-1">Giảng viên</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_teachers']) }}</h3>
                    <p class="text-xs text-slate-500 mt-2">
                        {{ number_format(($stats['total_teachers'] / max($stats['total_users'], 1)) * 100, 1) }}% tổng số
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-user-tie text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Cuộc thi --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium mb-1">Cuộc thi</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_competitions']) }}</h3>
                    <p class="text-xs text-orange-600 mt-2">
                        <i class="fa-solid fa-circle-dot mr-1"></i>
                        {{ $stats['ongoing_competitions'] }} đang diễn ra
                    </p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-slate-800">Tin tức</h4>
                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-newspaper text-cyan-600"></i>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">Tổng bài viết</span>
                    <span class="font-semibold text-slate-800">{{ number_format($stats['total_news']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">Đã xuất bản</span>
                    <span class="font-semibold text-green-600">{{ number_format($stats['published_news']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">Mới tháng này</span>
                    <span class="font-semibold text-blue-600">{{ number_format($monthlyStats['new_news']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-slate-800">Trạng thái người dùng</h4>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">Đang hoạt động</span>
                    <span class="font-semibold text-green-600">{{ number_format($stats['active_users']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600">Không hoạt động</span>
                    <span class="font-semibold text-red-600">{{ number_format($stats['total_users'] - $stats['active_users']) }}</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 mt-3">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($stats['active_users'] / max($stats['total_users'], 1)) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold">Truy cập hôm nay</h4>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="space-y-2">
                <h3 class="text-3xl font-bold">{{ number_format(rand(100, 500)) }}</h3>
                <p class="text-indigo-100 text-sm">lượt truy cập</p>
                <div class="flex items-center gap-2 text-sm mt-3">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>+15% so với hôm qua</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    {{-- <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            
            <a href="{{ route('admin.users') }}" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-500 transition">
                    <i class="fa-solid fa-user-plus text-blue-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-slate-700 group-hover:text-blue-600">Thêm người dùng</span>
            </a>

            <a href="{{ route('admin.news') }}" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 transition">
                <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-cyan-500 transition">
                    <i class="fa-solid fa-newspaper text-cyan-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-slate-700 group-hover:text-cyan-600">Quản lý tin tức</span>
            </a>

            <a href="#" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-slate-200 hover:border-orange-500 hover:bg-orange-50 transition">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-500 transition">
                    <i class="fa-solid fa-trophy text-orange-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-slate-700 group-hover:text-orange-600">Tạo cuộc thi</span>
            </a>

            <a href="#" class="group flex flex-col items-center justify-center p-6 rounded-lg border-2 border-slate-200 hover:border-purple-500 hover:bg-purple-50 transition">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-500 transition">
                    <i class="fa-solid fa-chart-bar text-purple-600 group-hover:text-white transition"></i>
                </div>
                <span class="text-sm font-medium text-slate-700 group-hover:text-purple-600">Xem báo cáo</span>
            </a>

        </div>
    </div> --}}

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Hoạt động gần đây --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Hoạt động gần đây</h3>
                <span class="text-sm text-blue-600 font-medium">{{ count($recentActivities) }} hoạt động</span>
            </div>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($recentActivities as $activity)
                <div class="flex gap-3 pb-4 border-b last:border-b-0">
                    <div class="w-10 h-10 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800">{{ $activity['title'] }}</p>
                        <p class="text-xs text-slate-500 mt-1 truncate">{{ $activity['description'] }}</p>
                        <p class="text-xs text-{{ $activity['color'] }}-600 mt-1">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fa-solid fa-inbox text-4xl text-slate-300"></i>
                    <p class="text-slate-500 mt-2">Chưa có hoạt động nào</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Cuộc thi sắp diễn ra --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Cuộc thi sắp diễn ra</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Xem tất cả</a>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($upcomingCompetitions as $competition)
                <div class="p-4 border border-slate-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-trophy text-orange-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-slate-800 text-sm truncate">{{ $competition->tencuocthi }}</h4>
                            <p class="text-xs text-slate-500 mt-1">
                                <i class="fa-regular fa-calendar mr-1"></i>
                                {{ \Carbon\Carbon::parse($competition->thoigianbatdau)->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-xs text-orange-600 mt-1">
                                <i class="fa-regular fa-clock mr-1"></i>
                                {{ \Carbon\Carbon::parse($competition->thoigianbatdau)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fa-solid fa-trophy text-4xl text-slate-300"></i>
                    <p class="text-slate-500 mt-2">Không có cuộc thi sắp tới</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Tin tức mới nhất --}}
    {{-- <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Tin tức mới nhất</h3>
            <a href="{{ route('admin.news') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($latestNews as $news)
            <div class="border border-slate-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition">
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                    {{ $news->loaitin }}
                </span>
                <h4 class="font-semibold text-slate-800 mt-3 line-clamp-2">{{ $news->tieude }}</h4>
                <p class="text-xs text-slate-500 mt-2">
                    <i class="fa-regular fa-calendar mr-1"></i>
                    {{ \Carbon\Carbon::parse($news->ngaydang)->format('d/m/Y') }}
                </p>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                    <span class="text-xs text-slate-500">
                        <i class="fa-regular fa-eye mr-1"></i>
                        {{ number_format($news->luotxem) }} lượt xem
                    </span>
                    <a href="#" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Xem chi tiết →
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8">
                <i class="fa-solid fa-newspaper text-4xl text-slate-300"></i>
                <p class="text-slate-500 mt-2">Chưa có tin tức nào</p>
            </div>
            @endforelse
        </div>
    </div> --}}

</div>
@endsection