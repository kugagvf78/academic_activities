@extends('layouts.client')
@section('title', 'Hồ sơ cá nhân')

@section('content')

{{-- 📋 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-6">
    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('info') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-4 gap-8 relative" x-data="{ 
            tab: new URLSearchParams(window.location.search).get('tab') || 'info' 
        }">

        {{-- 👤 LEFT SIDEBAR --}}
        <aside class="lg:col-span-1 sticky top-28 self-start">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

                {{-- Profile Header --}}
                <div class="px-6 pb-3 pt-6 text-center border-b border-gray-300 mx-8">
                    <div class="relative inline-block mb-4">
                        @if($user->anhdaidien)
                            <img src="{{ Storage::url($user->anhdaidien) }}"
                                class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover mx-auto"
                                alt="Avatar">
                        @else
                            <img src="{{asset('images/users/avt.jpg')}}"
                                class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover mx-auto"
                                alt="Avatar">
                        @endif
                        <button onclick="document.getElementById('avatar-input').click()"
                            class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white shadow-lg transition">
                            <i class="fas fa-camera text-xs"></i>
                        </button>
                        
                        {{-- Hidden file input --}}
                        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                            @csrf
                            <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                        </form>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 mb-1">{{ $user->hoten ?? $user->tendangnhap }}</h2>
                    
                    @if($user->vaitro === 'SinhVien' && $profile)
                        <p class="text-sm text-gray-500 mb-1">MSSV: {{ $profile->masinhvien }}</p>
                        @if($profile->lop)
                            <p class="text-sm text-gray-600 font-medium">{{ $profile->lop->tenlop }}</p>
                        @endif
                    @elseif($user->vaitro === 'GiangVien' && $profile)
                        <p class="text-sm text-gray-500 mb-1">MSGV: {{ $profile->magiangvien }}</p>
                        @if($profile->chucvu)
                            <p class="text-sm text-gray-600 font-medium">{{ $profile->chucvu }}</p>
                        @endif
                    @endif
                </div>

                {{-- Navigation Menu --}}
                <nav class="flex flex-col text-left mt-3">
                    <button @click="tab='info'; window.history.pushState({}, '', '?tab=info')"
                        :class="tab==='info' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition">
                        <i class="fas fa-user w-5"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

                    @if($user->vaitro === 'SinhVien')
                        <button @click="tab='activities'; window.history.pushState({}, '', '?tab=activities')"
                            :class="tab==='activities' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                            <i class="fas fa-trophy w-5"></i>
                            <span>Hoạt động học thuật</span>
                        </button>

                        <button @click="tab='points'; window.history.pushState({}, '', '?tab=points')"
                            :class="tab==='points' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Điểm rèn luyện</span>
                        </button>

                        <button 
                            @click="tab = 'competition'; window.history.pushState({}, '', '?tab=competition')"
                            :class="tab === 'competition' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100 cursor-pointer">
                            <i class="fas fa-trophy w-5" :class="tab === 'competition' ? 'text-blue-600' : 'text-gray-500'"></i>
                            <span>Đăng ký dự thi</span>
                        </button>

                        <button
                            @click="tab = 'activity'; window.history.pushState({}, '', '?tab=activity')"
                            :class="tab === 'activity' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100 cursor-pointer">
                            <i class="fas fa-hands-clapping w-5" :class="tab === 'activity' ? 'text-blue-600' : 'text-gray-500'"></i>
                            <span>Đăng ký cổ vũ - hỗ trợ</span>
                        </button>

                        <button @click="tab='certs'; window.history.pushState({}, '', '?tab=certs')"
                            :class="tab==='certs' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                            <i class="fas fa-certificate w-5"></i>
                            <span>Chứng nhận</span>
                        </button>
                    @endif

                    <button @click="tab='settings'; window.history.pushState({}, '', '?tab=settings')"
                        :class="tab==='settings' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                        <i class="fas fa-cog w-5"></i>
                        <span>Cài đặt</span>
                    </button>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100 text-red-600 hover:bg-red-50">
                            <i class="fas fa-right-from-bracket w-5"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- 📄 RIGHT CONTENT AREA --}}
        <main class="lg:col-span-3">

            {{-- 🧑 THÔNG TIN CÁ NHÂN --}}
            <div x-show="tab==='info'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Thông tin cá nhân</h3>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Thông tin chung --}}
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            {{-- Họ tên --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="hoten" 
                                    value="{{ old('hoten', $user->hoten) }}"
                                    class="w-full px-4 py-3 border border-gray-300 bg-gray-50 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Nhập họ tên đầy đủ" readonly>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" 
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="example@gmail.com" required>
                            </div>

                            {{-- Số điện thoại --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại</label>
                                <input type="text" name="sodienthoai" 
                                    value="{{ old('sodienthoai', $user->sodienthoai) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="0123456789">
                            </div>

                            {{-- Vai trò (read-only) --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Vai trò</label>
                                <input type="text" value="{{ $user->vaitro === 'SinhVien' ? 'Sinh viên' : 'Giảng viên' }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                    readonly>
                            </div>
                        </div>

                        {{-- Thông tin riêng cho Sinh viên --}}
                        @if($user->vaitro === 'SinhVien' && $profile)
                            <div class="pt-6 border-t border-gray-100">
                                <h4 class="font-bold text-lg text-gray-800 mb-4">Thông tin sinh viên</h4>
                                <div class="grid md:grid-cols-2 gap-6">
                                    {{-- MSSV (read-only) --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mã sinh viên</label>
                                        <input type="text" value="{{ $profile->masinhvien }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                            readonly>
                                    </div>

                                    {{-- Lớp --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lớp</label>
                                        <input type="text" 
                                            value="{{ $profile->lop->malop ?? 'Chưa có' }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                            readonly>
                                    </div>

                                    {{-- Năm nhập học --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Năm nhập học</label>
                                        <input type="number" name="namnhaphoc" 
                                            value="{{ old('namnhaphoc', $profile->namnhaphoc) }}"
                                            class="w-full px-4 py-3 border border-gray-300 bg-gray-50 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="2023" min="2000" max="{{ date('Y') + 1 }}" readonly>
                                    </div>

                                    {{-- Điểm rèn luyện (read-only) --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Điểm rèn luyện</label>
                                        <input type="text" value="{{ number_format($profile->diemrenluyen ?? 0, 2) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                            readonly>
                                    </div>

                                    {{-- Trạng thái (read-only) --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
                                        <input type="text" value="{{ $profile->trangthai ?? 'Active' }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Thông tin riêng cho Giảng viên --}}
                        @if($user->vaitro === 'GiangVien' && $profile)
                            <div class="pt-6 border-t border-gray-100">
                                <h4 class="font-bold text-lg text-gray-800 mb-4">Thông tin giảng viên</h4>
                                <div class="grid md:grid-cols-2 gap-6">
                                    {{-- MSGV (read-only) --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mã giảng viên</label>
                                        <input type="text" value="{{ $profile->magiangvien }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                            readonly>
                                    </div>

                                    {{-- Bộ môn (read-only) --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bộ môn</label>
                                        <input type="text" value="{{ $profile->boMon->tenbomon ?? 'Chưa có' }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                            readonly>
                                    </div>

                                    {{-- Chức vụ --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chức vụ</label>
                                        <input type="text" name="chucvu" 
                                            value="{{ old('chucvu', $profile->chucvu) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="Giảng viên chính">
                                    </div>

                                    {{-- Học vị --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Học vị</label>
                                        <input type="text" name="hocvi" 
                                            value="{{ old('hocvi', $profile->hocvi) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="Thạc sĩ">
                                    </div>

                                    {{-- Chuyên môn --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chuyên môn</label>
                                        <textarea name="chuyenmon" rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            placeholder="Mô tả về chuyên môn...">{{ old('chuyenmon', $profile->chuyenmon) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                            <button type="reset"
                                class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition">
                                Hủy
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                                <i class="fas fa-save mr-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 🏆 HOẠT ĐỘNG HỌC THUẬT (Chỉ cho sinh viên) --}}
            @if($user->vaitro === 'SinhVien')
            <div x-show="tab==='activities'" 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-2" 
                x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    
                    {{-- Header & Toolbar --}}
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                                <i class="fas fa-graduation-cap text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">Hoạt động Học thuật</h3>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Hồ sơ điểm rèn luyện</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('client.events.index') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 hover:border-slate-400 hover:text-indigo-600 transition shadow-sm gap-2">
                            <i class="fas fa-plus-circle"></i> Đăng ký hoạt động
                        </a>
                    </div>

                    {{-- Content Body --}}
                    <div class="p-6 bg-slate-50 min-h-[300px]">
                        @if($activities->count() > 0)
                            <div class="space-y-3">
                                @foreach($activities as $activity)
                                    {{-- Logic màu sắc trạng thái --}}
                                    @php
                                        $statusConfig = match($activity['status']) {
                                            'Active', 'Approved', 'Completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'dot' => 'bg-emerald-500'],
                                            'Registered', 'Pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'dot' => 'bg-amber-500'],
                                            'Cancelled', 'Rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-100', 'dot' => 'bg-rose-500'],
                                            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-100', 'dot' => 'bg-slate-500']
                                        };
                                        
                                        $dateObj = \Carbon\Carbon::parse($activity['date']);
                                    @endphp

                                    {{-- Item Card --}}
                                    <div class="group bg-white rounded-lg border border-slate-200 p-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                                        <div class="flex items-start gap-4">
                                            
                                            {{-- Date Block (Style Lịch) --}}
                                            <div class="flex-shrink-0 w-14 h-14 bg-slate-50 rounded-lg border border-slate-200 flex flex-col items-center justify-center text-center overflow-hidden">
                                                <span class="text-[10px] uppercase font-bold text-slate-500 bg-slate-100 w-full py-0.5 block border-b border-slate-200">
                                                    Tháng {{ $dateObj->format('m') }}
                                                </span>
                                                <span class="text-xl font-extrabold text-slate-700 leading-none mt-1">
                                                    {{ $dateObj->format('d') }}
                                                </span>
                                            </div>

                                            {{-- Main Info --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2">
                                                    <div>
                                                        <h4 class="text-base font-bold text-slate-800 group-hover:text-indigo-700 transition line-clamp-1" title="{{ $activity['title'] }}">
                                                            {{ $activity['title'] }}
                                                        </h4>
                                                        @if(isset($activity['subtitle']))
                                                            <p class="text-sm text-slate-500 mt-0.5 line-clamp-1">{{ $activity['subtitle'] }}</p>
                                                        @endif
                                                        
                                                        {{-- Tags Row --}}
                                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                                            {{-- Role Badge --}}
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                                <i class="fas fa-user-tag mr-1.5 opacity-70"></i> {{ $activity['role'] }}
                                                            </span>
                                                            
                                                            {{-- Attendance Badge --}}
                                                            @if(isset($activity['diem_danh']))
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                                    <i class="fas fa-qrcode mr-1.5 opacity-70"></i> {{ $activity['diem_danh'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Right Side: Points & Status --}}
                                                    <div class="flex flex-row md:flex-col items-center md:items-end gap-3 md:gap-1 mt-2 md:mt-0 pl-0 md:pl-4 border-t md:border-t-0 border-slate-100 pt-2 md:pt-0">
                                                        
                                                        {{-- Điểm Rèn Luyện (Highlight) --}}
                                                        @if(isset($activity['drl_points']))
                                                            <div class="flex items-center gap-1 order-2 md:order-1">
                                                                <span class="text-xs text-slate-400 font-medium uppercase hidden md:inline-block">Điểm cộng</span>
                                                                <span class="text-lg font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                                                    +{{ $activity['drl_points'] }}
                                                                </span>
                                                            </div>
                                                        @endif

                                                        {{-- Status Badge --}}
                                                        <div class="order-1 md:order-2 px-2.5 py-0.5 rounded-full border {{ $statusConfig['bg'] }} {{ $statusConfig['border'] }} {{ $statusConfig['text'] }} text-xs font-semibold flex items-center gap-1.5">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                                            {{ $activity['status'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State (Professional) --}}
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-clipboard-list text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-700">Chưa có dữ liệu hoạt động</h3>
                                <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">
                                    Các hoạt động học thuật và điểm rèn luyện của bạn sẽ được hiển thị chi tiết tại đây.
                                </p>
                                <a href="{{ route('client.events.index') }}" class="mt-5 text-indigo-600 font-medium text-sm hover:underline">
                                    Tìm kiếm hoạt động ngay <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Footer Summary (Optional) --}}
                    @if($activities->count() > 0)
                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 text-xs text-slate-500 flex justify-between items-center">
                        <span>Hiển thị {{ $activities->count() }} hoạt động gần nhất.</span>
                        <span class="font-medium text-indigo-700">Tổng điểm tích lũy: {{ $activities->sum('drl_points') ?? 0 }} DRL</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 📊 ĐIỂM RÈN LUYỆN - PHIÊN BẢN TỐI ƯU --}}
            <div x-show="tab==='points'" x-transition:enter.duration.200ms x-transition:leave.duration.100ms>
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                    
                    {{-- Header - Nền đậm và chuyên nghiệp --}}
                    <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-blue-900 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-3xl font-extrabold text-white mb-1 tracking-tight">Điểm Rèn Luyện</h3>
                                <p class="text-indigo-200 text-sm">Bảng tổng hợp kết quả đánh giá năm học {{ date('Y') }}</p>
                            </div>
                            {{-- <a href="{{ route('profile.diem.export') }}" 
                                class="flex items-center px-4 py-2 bg-white hover:bg-indigo-50 text-indigo-700 rounded-xl font-semibold transition text-sm shadow-lg border border-white/50 transform hover:scale-105 duration-300">
                                <i class="fas fa-file-pdf mr-2 text-red-500"></i>Xuất báo cáo PDF
                            </a> --}}
                        </div>
                    </div>

                    <div class="p-8">
                        @if(isset($diemRenLuyen['details']) && $diemRenLuyen['details']->count() > 0)
                            
                            {{-- Bảng tổng hợp điểm --}}
                            <div class="mb-10">
                                <h4 class="text-2xl font-extrabold text-gray-800 uppercase tracking-wide mb-6 border-b-2 border-indigo-500 pb-2 flex items-center">
                                    <i class="fas fa-chart-line text-indigo-500 mr-2"></i>Tổng hợp điểm rèn luyện
                                </h4>
                                
                                <div class="grid md:grid-cols-3 gap-6">
                                    
                                    {{-- Điểm cơ bản --}}
                                    <div class="bg-white rounded-xl p-6 shadow-xl border-l-4 border-blue-600 hover:shadow-2xl transition duration-300">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-sm font-semibold text-blue-600 uppercase">Điểm cơ bản (Base Score)</span>
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-seedling text-blue-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <p class="text-4xl font-bold text-gray-900 mb-1">{{ $diemRenLuyen['base'] ?? 0 }}</p>
                                        <p class="text-sm text-gray-500">Điểm khởi điểm được quy định</p>
                                    </div>
                                    
                                    {{-- Điểm cộng thêm --}}
                                    <div class="bg-white rounded-xl p-6 shadow-xl border-l-4 border-green-600 hover:shadow-2xl transition duration-300">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-sm font-semibold text-green-600 uppercase">Điểm cộng thêm (Bonus)</span>
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <p class="text-4xl font-bold text-green-600 mb-1">+{{ $diemRenLuyen['bonus'] ?? 0 }}</p>
                                        <p class="text-sm text-gray-500">Từ hoạt động, nghiên cứu & giải thưởng</p>
                                    </div>
                                    
                                    {{-- Tổng kết cuối kỳ --}}
                                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl p-6 text-white shadow-2xl transform hover:scale-[1.02] transition duration-500">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-base font-semibold text-indigo-200 uppercase">Tổng kết cuối kỳ</span>
                                            <div class="w-10 h-10 bg-white bg-opacity-30 rounded-full flex items-center justify-center">
                                                <i class="fas fa-award text-yellow-300 text-xl"></i>
                                            </div>
                                        </div>
                                        <p class="text-5xl font-extrabold mb-1">{{ $diemRenLuyen['final'] ?? 'N/A' }}</p>
                                        <p class="text-sm text-indigo-200 font-medium">Điểm rèn luyện chính thức</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bảng chi tiết --}}
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-2xl font-extrabold text-gray-800 uppercase tracking-wide border-b-2 border-green-600 pb-1 flex items-center">
                                        <i class="fas fa-list-alt text-green-600 mr-2"></i>Chi tiết điểm cộng
                                    </h4>
                                    <span class="text-sm font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">Tổng: {{ $diemRenLuyen['details']->count() }} hoạt động</span>
                                </div>

                                <div class="border border-gray-300 rounded-xl overflow-x-auto shadow-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider w-16">
                                                    STT
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
                                                    Hoạt động / Thành tích
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider w-32">
                                                    Phân loại
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider w-36">
                                                    Ngày thực hiện
                                                </th>
                                                <th class="px-6 py-3 text-right text-xs font-extrabold text-gray-600 uppercase tracking-wider w-24">
                                                    Điểm
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($diemRenLuyen['details'] as $index => $detail)
                                                <tr class="hover:bg-indigo-50 transition-colors cursor-pointer" 
                                                    x-data="{ expanded: false }" 
                                                    @click="expanded = !expanded">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 bg-{{ $detail['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                                <i class="fas {{ $detail['icon'] }} text-{{ $detail['color'] }}-600 text-sm"></i>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <p class="font-semibold text-gray-900 truncate">{{ $detail['title'] }}</p>
                                                                @if(isset($detail['mota']))
                                                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ Str::limit($detail['mota'], 70) }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-{{ $detail['color'] }}-100 text-{{ $detail['color'] }}-800 border border-{{ $detail['color'] }}-300">
                                                            {{ $detail['loai'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                        <i class="far fa-calendar-alt text-gray-400 mr-2"></i>{{ \Carbon\Carbon::parse($detail['ngay'])->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-extrabold bg-green-50 text-green-700 border border-green-300">
                                                            +{{ $detail['diem'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                {{-- Chi tiết mở rộng (Improved visibility) --}}
                                                @if(isset($detail['chi_tiet']))
                                                    <tr x-show="expanded" 
                                                        x-transition:enter="transition ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 transform scale-y-0"
                                                        x-transition:enter-end="opacity-100 transform scale-y-100"
                                                        x-transition:leave="transition ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 transform scale-y-100"
                                                        x-transition:leave-end="opacity-0 transform scale-y-0"
                                                        class="bg-indigo-50 origin-top">
                                                        <td colspan="5" class="px-6 py-4">
                                                            <div class="bg-white rounded-lg border-2 border-indigo-300 p-6 ml-10 shadow-md">
                                                                <h5 class="text-sm font-extrabold text-indigo-700 uppercase tracking-wider mb-4 border-b border-indigo-200 pb-2 flex items-center">
                                                                    <i class="fas fa-search-plus mr-2 text-indigo-500"></i>Thông tin chi tiết hoạt động
                                                                </h5>
                                                                
                                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 text-sm">
                                                                    
                                                                    {{-- Thông tin Cuộc thi --}}
                                                                    @if(isset($detail['chi_tiet']['ten_cuoc_thi']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-trophy text-yellow-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Cuộc thi</span>
                                                                                <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['ten_cuoc_thi'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Thông tin Giải thưởng --}}
                                                                    @if(isset($detail['chi_tiet']['ten_giai']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-medal text-amber-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Giải thưởng</span>
                                                                                <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['ten_giai'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Thông tin Xếp hạng --}}
                                                                    @if(isset($detail['chi_tiet']['xep_hang']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-ranking-star text-orange-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Xếp hạng</span>
                                                                                <span class="font-bold text-gray-900">
                                                                                    {{ $detail['chi_tiet']['xep_hang'] }}
                                                                                    @if(isset($detail['chi_tiet']['la_dong_hang']) && $detail['chi_tiet']['la_dong_hang'])
                                                                                        <span class="text-xs text-gray-500 font-normal">(Đồng hạng)</span>
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Thông tin Trạng thái --}}
                                                                    @if(isset($detail['chi_tiet']['trang_thai']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-certificate text-indigo-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Trạng thái phê duyệt</span>
                                                                                @if($detail['chi_tiet']['trang_thai'] === 'Approved')
                                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 border border-green-300">
                                                                                        <i class="fas fa-check-circle mr-1"></i>Đã phê duyệt
                                                                                    </span>
                                                                                @elseif($detail['chi_tiet']['trang_thai'] === 'Pending')
                                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                                                                        <i class="fas fa-clock mr-1"></i>Chờ duyệt
                                                                                    </span>
                                                                                @else
                                                                                    <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['trang_thai'] }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Thông tin Hoạt động hỗ trợ --}}
                                                                    @if(isset($detail['chi_tiet']['ten_hoat_dong']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-hands-helping text-purple-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Hoạt động</span>
                                                                                <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['ten_hoat_dong'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Thông tin Loại hoạt động --}}
                                                                    @if(isset($detail['chi_tiet']['loai']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-tag text-blue-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Loại hoạt động</span>
                                                                                <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['loai'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Thông tin Thời gian --}}
                                                                    @if(isset($detail['chi_tiet']['thoi_gian']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-clock text-gray-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Thời gian</span>
                                                                                <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['thoi_gian'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    {{-- Thông tin Địa điểm --}}
                                                                    @if(isset($detail['chi_tiet']['dia_diem']))
                                                                        <div class="flex items-start gap-3">
                                                                            <i class="fas fa-map-marker-alt text-red-600 mt-1 w-4 flex-shrink-0"></i>
                                                                            <div>
                                                                                <span class="text-xs text-gray-500 block">Địa điểm</span>
                                                                                <span class="font-bold text-gray-900">{{ $detail['chi_tiet']['dia_diem'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                
                                                                {{-- Ghi chú/Mô tả chi tiết --}}
                                                                @if(isset($detail['mota']))
                                                                    <div class="mt-6 pt-4 border-t border-indigo-100">
                                                                        <p class="text-sm text-gray-700 leading-relaxed">
                                                                            <i class="fas fa-comment-dots text-indigo-500 mr-2"></i>
                                                                            <span class="font-extrabold text-indigo-700">Mô tả/Ghi chú:</span> {{ $detail['mota'] }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                                            <tr>
                                                <td colspan="4" class="px-6 py-3 text-right text-base font-extrabold text-gray-700">
                                                    Tổng điểm cộng:
                                                </td>
                                                <td class="px-6 py-3 text-right">
                                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-lg font-extrabold bg-green-200 text-green-900 border-2 border-green-400 shadow-md">
                                                        +{{ $diemRenLuyen['bonus'] ?? 0 }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- Ghi chú quan trọng --}}
                                <div class="mt-8 p-6 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-inner">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-lightbulb text-indigo-600 text-xl mt-1 flex-shrink-0"></i>
                                        <div class="text-sm text-indigo-900">
                                            <p class="font-extrabold text-base mb-2 text-indigo-700">Lưu ý quan trọng về Điểm Rèn Luyện:</p>
                                            <ul class="list-disc list-inside space-y-1.5 text-indigo-800">
                                                <li>**Điểm cơ bản (Base Score)** có thể bị trừ nếu sinh viên vi phạm quy chế hoặc có kết quả học tập không đạt yêu cầu theo quy định của trường.</li>
                                                <li>**Điểm cộng (Bonus)** là điểm tích lũy từ các hoạt động ngoại khóa, nghiên cứu khoa học, và giải thưởng được cấp có thẩm quyền công nhận.</li>
                                                <li>Nhấp vào **từng dòng hoạt động** trong bảng để xem **thông tin chi tiết** (Tên cuộc thi, Xếp hạng, Trạng thái phê duyệt, v.v.).</li>
                                                <li>Để được hỗ trợ giải đáp thắc mắc hoặc khiếu nại về điểm, vui lòng liên hệ **Phòng Công tác sinh viên/Phòng Đào tạo** của trường.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Trạng thái rỗng --}}
                            <div class="text-center py-20 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="inline-flex items-center justify-center w-24 h-24 bg-indigo-100 rounded-full mb-4 shadow-xl">
                                    <i class="fas fa-chart-line text-5xl text-indigo-600"></i>
                                </div>
                                <h4 class="text-2xl font-bold text-gray-900 mb-3">Chưa có dữ liệu điểm rèn luyện</h4>
                                <p class="text-gray-600 text-base max-w-lg mx-auto">
                                    Hãy tích cực tham gia các hoạt động và cuộc thi do trường tổ chức để tích lũy điểm rèn luyện. Điểm số sẽ được cập nhật sau khi kết thúc học kỳ và được phê duyệt chính thức.
                                </p>
                                <a href="#" class="mt-5 inline-block px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition transform hover:scale-105">
                                    <i class="fas fa-search mr-2"></i>Tìm kiếm hoạt động mới
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 🏆 CHỨNG NHẬN & GIẢI THƯỞNG --}}
            <div x-show="tab==='certs'" 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-2" 
                x-transition:enter-end="opacity-100 translate-y-0">

                <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                        <div class="w-10 h-10 rounded-lg bg-yellow-500 text-white flex items-center justify-center shadow-md">
                            <i class="fas fa-medal text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Chứng nhận & Giải thưởng</h3>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Ghi nhận các thành tích nổi bật</p>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 min-h-[300px]">
                        @if($certificates->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($certificates as $cert)
                                    {{-- Thẻ Vinh Danh (Award Plaque) --}}
                                    <div class="relative bg-white border border-yellow-200 rounded-xl p-6 shadow-lg group transition duration-300 hover:shadow-xl hover:border-yellow-400">
                                        
                                        {{-- Dải màu Gold Accent --}}
                                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-300 to-orange-400 rounded-t-xl"></div>
                                        
                                        <div class="flex flex-col h-full">
                                            <div class="flex items-start gap-4 flex-1">
                                                {{-- Icon Medal --}}
                                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-yellow-300">
                                                    <i class="fas fa-award text-yellow-600 text-xl"></i>
                                                </div>
                                                
                                                <div class="flex-1">
                                                    {{-- Giải thưởng --}}
                                                    <h4 class="font-extrabold text-lg text-slate-800 mb-1 group-hover:text-yellow-700 transition line-clamp-2">
                                                        {{ $cert['award'] }}
                                                    </h4>
                                                    
                                                    {{-- Sự kiện/Cuộc thi --}}
                                                    <p class="text-xs text-slate-500 font-medium uppercase mt-0.5 line-clamp-1">
                                                        <i class="fas fa-trophy mr-1"></i> {{ $cert['event'] }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            {{-- Phần Thưởng và Điểm RL --}}
                                            <div class="mt-4 pt-4 border-t border-slate-100">
                                                <div class="flex items-center justify-between mb-2">
                                                    {{-- Điểm Rèn Luyện (Cực kỳ nổi bật) --}}
                                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-full border border-emerald-100 shadow-sm">
                                                        <i class="fas fa-star mr-1"></i> +{{ $cert['points'] }} RL
                                                    </span>
                                                    
                                                    {{-- Ngày nhận --}}
                                                    <span class="text-xs text-slate-500 flex items-center">
                                                        <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($cert['date'])->format('d/m/Y') }}
                                                    </span>
                                                </div>

                                                {{-- Phần thưởng chi tiết --}}
                                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                                    <i class="fas fa-gift text-orange-500 flex-shrink-0"></i>
                                                    <span class="font-medium truncate" title="{{ $cert['prize'] }}">
                                                        Phần thưởng: {{ $cert['prize'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State (Professional & Encouraging) --}}
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mb-4 border border-yellow-200">
                                    <i class="fas fa-award text-4xl text-yellow-400"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-700 mb-1">Hãy bắt đầu tích lũy thành tích</h3>
                                <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">
                                    Các giải thưởng và chứng nhận bạn đạt được trong quá trình học tập sẽ được ghi nhận tại đây để cộng điểm rèn luyện.
                                </p>
                                <a href="#" class="mt-4 text-indigo-600 font-medium text-sm hover:underline">
                                    Xem các cuộc thi đang diễn ra <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- 📣 CỔ VŨ - HỖ TRỢ --}}
            @if($user->vaitro === 'SinhVien')
            <div x-show="tab === 'activity'" 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-2" 
                x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-600 text-white flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-hand-holding-heart text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">Cổ vũ & Hỗ trợ sự kiện</h3>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Tích lũy điểm rèn luyện</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('client.events.index') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg shadow-sm transition gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Khám phá sự kiện
                        </a>
                    </div>

                    <div class="p-6 bg-slate-50 min-h-[300px]">
                        @if($registrations->isEmpty())
                            {{-- Empty State --}}
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mb-4 border border-purple-100">
                                    <i class="fa-solid fa-hands-clapping text-4xl text-purple-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-700 mb-1">Chưa đăng ký hoạt động nào</h3>
                                <p class="text-slate-500 text-sm mb-6 max-w-xs mx-auto">Tham gia vào ban tổ chức hoặc đội cổ vũ để hòa mình vào không khí sự kiện và nhận điểm rèn luyện.</p>
                                <a href="{{ route('client.events.index') }}" 
                                class="inline-flex items-center gap-2 text-purple-600 font-semibold hover:text-purple-800 hover:underline transition">
                                    Tìm kiếm sự kiện ngay <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        @else
                            {{-- List of Registrations --}}
                            <div class="space-y-4">
                                @foreach($registrations as $reg)
                                    {{-- Logic màu sắc dựa trên loại hoạt động --}}
                                    @php
                                        $theme = match($reg->loaihoatdong) {
                                            'CoVu' => ['border' => 'bg-purple-500', 'badge_bg' => 'bg-purple-50', 'badge_text' => 'text-purple-700', 'icon' => 'fa-flag'],
                                            'ToChuc' => ['border' => 'bg-blue-500', 'badge_bg' => 'bg-blue-50', 'badge_text' => 'text-blue-700', 'icon' => 'fa-clipboard-list'],
                                            'HoTroKyThuat' => ['border' => 'bg-teal-500', 'badge_bg' => 'bg-teal-50', 'badge_text' => 'text-teal-700', 'icon' => 'fa-wrench'],
                                            default => ['border' => 'bg-gray-500', 'badge_bg' => 'bg-gray-50', 'badge_text' => 'text-gray-700', 'icon' => 'fa-star']
                                        };
                                    @endphp

                                    <div class="group bg-white rounded-xl border border-slate-200 hover:border-slate-300 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden pl-5">
                                        
                                        {{-- Thanh màu nhận diện bên trái --}}
                                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $theme['border'] }}"></div>

                                        <div class="p-5">
                                            {{-- Top Section: Title & Status --}}
                                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 mb-4">
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                                        {{-- Loại hoạt động Badge --}}
                                                        <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide rounded-md border border-opacity-20 {{ $theme['badge_bg'] }} {{ $theme['badge_text'] }} border-current flex items-center gap-1.5">
                                                            <i class="fa-solid {{ $theme['icon'] }}"></i>
                                                            {{ $reg->loaihoatdong === 'CoVu' ? 'Cổ vũ' : ($reg->loaihoatdong === 'ToChuc' ? 'BTC / Hậu cần' : 'Hỗ trợ kỹ thuật') }}
                                                        </span>

                                                        {{-- Status Badge (Dynamic) --}}
                                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border flex items-center gap-1.5
                                                            {{ $reg->statusColor === 'green' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                                            {{ $reg->statusColor === 'blue' ? 'bg-sky-50 text-sky-700 border-sky-100' : '' }}
                                                            {{ $reg->statusColor === 'gray' ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $reg->statusColor === 'green' ? 'bg-emerald-500' : ($reg->statusColor === 'blue' ? 'bg-sky-500' : 'bg-slate-400') }}"></span>
                                                            {{ $reg->statusLabel }}
                                                        </span>
                                                    </div>

                                                    <h3 class="text-lg font-bold text-slate-800 group-hover:text-purple-700 transition">
                                                        {{ $reg->tenhoatdong }}
                                                    </h3>
                                                    <p class="text-sm text-slate-500 mt-0.5 font-medium">
                                                        <i class="fa-solid fa-trophy text-slate-400 mr-1"></i> Sự kiện: {{ $reg->tencuocthi }}
                                                    </p>
                                                </div>
                                                
                                                {{-- Action / Status Area --}}
                                                <div class="flex items-center gap-2 md:self-start">
                                                    @if($reg->diemdanhqr)
                                                        <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-700 flex items-center gap-2">
                                                            <i class="fa-solid fa-circle-check text-lg"></i>
                                                            <div class="flex flex-col">
                                                                <span class="text-xs font-bold uppercase leading-none">Đã điểm danh</span>
                                                                <span class="text-[10px] opacity-80 leading-none mt-0.5">{{ $reg->thoigiandiemdanh->format('H:i d/m') }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @if($reg->canCancel)
                                                            <form action="{{ route('profile.activity.cancel', $reg->madangkyhoatdong) }}" method="POST"
                                                                onsubmit="return confirm('Bạn có chắc muốn hủy vị trí này? Hành động này không thể hoàn tác.');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="group/btn flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:border-red-200 hover:bg-red-50 text-slate-500 hover:text-red-600 rounded-lg text-sm font-medium transition shadow-sm" title="Hủy đăng ký">
                                                                    <i class="fa-solid fa-xmark"></i> <span class="group-hover/btn:inline hidden sm:inline">Hủy đăng ký</span>
                                                                </button>
                                                            </form>
                                                        @elseif($reg->status === 'upcoming')
                                                            <span class="text-xs text-amber-600 bg-amber-50 border border-amber-100 px-2 py-1 rounded md:max-w-[120px] text-center">
                                                                <i class="fa-solid fa-lock"></i> Khóa hủy (24h)
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Divider --}}
                                            <div class="h-px bg-slate-100 my-3"></div>

                                            {{-- Grid Details --}}
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-slate-400 uppercase font-semibold">Thời gian bắt đầu</span>
                                                    <span class="text-slate-700 font-medium flex items-center gap-1.5">
                                                        <i class="fa-regular fa-calendar text-slate-400"></i>
                                                        {{ $reg->thoigianbatdau->format('H:i - d/m/Y') }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-slate-400 uppercase font-semibold">Thời gian kết thúc</span>
                                                    <span class="text-slate-700 font-medium flex items-center gap-1.5">
                                                        <i class="fa-regular fa-clock text-slate-400"></i>
                                                        {{ $reg->thoigianketthuc->format('H:i - d/m/Y') }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-slate-400 uppercase font-semibold">Ngày đăng ký</span>
                                                    <span class="text-slate-600 flex items-center gap-1.5">
                                                        <i class="fa-solid fa-pen-to-square text-slate-400"></i>
                                                        {{ $reg->ngaydangky->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Footer Info --}}
                            <div class="mt-6 flex items-start gap-3 p-4 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600">
                                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 text-lg"></i>
                                <div>
                                    <p class="font-bold text-slate-700 mb-1">Quy định về Điểm rèn luyện:</p>
                                    <ul class="list-disc pl-4 space-y-1 text-xs sm:text-sm text-slate-600">
                                        <li>Điểm rèn luyện chỉ được ghi nhận khi hệ thống xác nhận trạng thái <span class="text-emerald-600 font-bold">"Đã điểm danh"</span>.</li>
                                        <li>Bạn chỉ có thể hủy đăng ký <strong>trước 24 giờ</strong> khi sự kiện bắt đầu.</li>
                                        <li>Vui lòng có mặt đúng giờ và tuân thủ quy định của Ban tổ chức.</li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- 🏆 ĐĂNG KÝ DỰ THI --}}
            @if($user->vaitro === 'SinhVien')
            <div x-show="tab === 'competition'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                                <span class="bg-blue-600 text-white w-8 h-8 rounded-lg flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </span>
                                Hoạt động & Cuộc thi
                            </h3>
                            <p class="text-slate-500 text-sm mt-1">Quản lý các cuộc thi bạn đang tham gia để tích lũy điểm rèn luyện.</p>
                        </div>
                        <a href="{{ route('client.events.index') }}" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm shadow-sm hover:shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Đăng ký mới
                        </a>
                    </div>

                    <div class="p-6 bg-slate-50 min-h-[300px]">
                        @if($competitionRegistrations->isEmpty())
                            {{-- Empty State --}}
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="bg-white p-6 rounded-full shadow-sm mb-4">
                                    <i class="fa-solid fa-clipboard-list text-4xl text-slate-300"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-700 mb-1">Chưa có dữ liệu đăng ký</h3>
                                <p class="text-slate-500 text-sm mb-6 max-w-xs mx-auto">Bạn chưa đăng ký tham gia cuộc thi nào. Hãy tìm kiếm và tham gia để rèn luyện bản thân.</p>
                                <a href="{{ route('client.events.index') }}" 
                                class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-800 hover:underline transition">
                                    Xem danh sách cuộc thi <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        @else
                            {{-- List of Registrations --}}
                            <div class="space-y-4">
                                @foreach($competitionRegistrations as $reg)
                                {{-- Card Item --}}
                                <div class="group bg-white rounded-xl border border-slate-200 hover:border-blue-300 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden">
                                    
                                    {{-- Decorative Side Border --}}
                                    <div class="absolute left-0 top-0 bottom-0 w-1 
                                        {{ $reg->mabaithi ? 'bg-emerald-500' : ($reg->loaidangky === 'CaNhan' ? 'bg-blue-500' : 'bg-indigo-500') }}">
                                    </div>

                                    <div class="p-5 pl-7">
                                        {{-- Top Row: Title & Badges --}}
                                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 mb-4">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                                    {{-- Loại đăng ký Badge --}}
                                                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide rounded-md border
                                                        {{ $reg->loaidangky === 'CaNhan' 
                                                            ? 'bg-blue-50 text-blue-700 border-blue-100' 
                                                            : 'bg-indigo-50 text-indigo-700 border-indigo-100' }}">
                                                        {{ $reg->loaidangky === 'CaNhan' ? 'Cá nhân' : 'Đội nhóm' }}
                                                    </span>
                                                    
                                                    {{-- Status Badge --}}
                                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border flex items-center gap-1.5
                                                        {{ $reg->statusColor === 'green' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                                        {{ $reg->statusColor === 'blue' ? 'bg-sky-50 text-sky-700 border-sky-100' : '' }}
                                                        {{ $reg->statusColor === 'gray' ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}
                                                        {{ $reg->statusColor === 'red' ? 'bg-rose-50 text-rose-700 border-rose-100' : '' }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ str_replace(['bg-', 'text-', 'border-'], 'bg-', $reg->statusColor === 'green' ? 'bg-emerald-500' : ($reg->statusColor === 'blue' ? 'bg-sky-500' : ($reg->statusColor === 'red' ? 'bg-rose-500' : 'bg-slate-400'))) }}"></span>
                                                        {{ $reg->statusLabel }}
                                                    </span>
                                                </div>
                                                
                                                <h3 class="text-lg font-bold text-slate-800 group-hover:text-blue-700 transition">
                                                    {{ $reg->tencuocthi }}
                                                </h3>

                                                @if($reg->loaidangky === 'DoiNhom')
                                                    <p class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                                                        <i class="fa-solid fa-users text-indigo-500"></i>
                                                        Team: <span class="font-semibold text-slate-700">{{ $reg->tendoithi }}</span>
                                                        <span class="text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-600">
                                                            {{ $reg->vaitro === 'TruongDoi' ? 'Trưởng nhóm' : 'Thành viên' }}
                                                        </span>
                                                    </p>
                                                @endif
                                            </div>

                                            {{-- Action Area (Desktop: Right aligned) --}}
                                            <div class="flex items-center gap-2 md:self-start">
                                                @if($reg->mabaithi)
                                                    <div class="px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-700 flex flex-col items-end">
                                                        <span class="text-xs font-semibold uppercase tracking-wider"><i class="fa-solid fa-check-circle"></i> Đã nộp bài</span>
                                                        <span class="text-xs opacity-75">{{ $reg->thoigiannop->format('H:i d/m/Y') }}</span>
                                                    </div>
                                                @elseif($reg->trangthai === 'Cancelled')
                                                    <span class="text-sm text-rose-500 font-medium bg-rose-50 px-3 py-1 rounded-lg border border-rose-100">
                                                        <i class="fa-solid fa-ban"></i> Đã hủy
                                                    </span>
                                                @else
                                                    {{-- Nút HỦY --}}
                                                    @if($reg->canCancel)
                                                        <form action="{{ route('profile.competition.cancel', $reg->id) }}" method="POST" class="inline-block"
                                                            onsubmit="return confirm('{{ $reg->loaidangky === 'DoiNhom' && $reg->vaitro === 'TruongDoi' ? 'Cảnh báo: Hủy nhóm sẽ hủy đăng ký của toàn bộ thành viên!' : 'Bạn chắc chắn muốn hủy đăng ký?' }}');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="px-3 py-2 text-sm font-medium text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hủy đăng ký">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Nút NỘP BÀI --}}
                                                    @if($reg->canSubmit)
                                                        <a href="{{ route('profile.competition.submit.form', ['id' => $reg->id, 'loaidangky' => $reg->loaidangky]) }}"
                                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                                                            <i class="fa-solid fa-upload"></i> Nộp bài
                                                        </a>
                                                    @elseif($reg->status !== 'ended')
                                                        <button disabled class="px-4 py-2 bg-slate-100 text-slate-400 font-medium rounded-lg text-sm cursor-not-allowed">
                                                            Chưa mở nộp
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Divider --}}
                                        <div class="h-px bg-slate-100 my-3"></div>

                                        {{-- Bottom Row: Timeline Grid --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                            <div class="flex flex-col">
                                                <span class="text-xs text-slate-400 uppercase font-semibold">Thời gian thi</span>
                                                <span class="text-slate-700 font-medium">
                                                    {{ $reg->thoigianbatdau->format('d/m') }} - {{ $reg->thoigianketthuc->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-slate-400 uppercase font-semibold">Hạn nộp bài</span>
                                                <span class="text-slate-700 font-medium {{ $reg->thoigianketthuc->isPast() && !$reg->mabaithi ? 'text-rose-600' : '' }}">
                                                    {{ $reg->thoigianketthuc->format('H:i - d/m/Y') }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-slate-400 uppercase font-semibold">Ngày đăng ký</span>
                                                <span class="text-slate-600">
                                                    {{ $reg->ngaydangky->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Cảnh báo dành cho thành viên đội (nếu không phải trưởng đội) --}}
                                        @if($reg->loaidangky === 'DoiNhom' && $reg->vaitro !== 'TruongDoi' && $reg->trangthai !== 'Cancelled')
                                            <div class="mt-3 text-xs text-slate-400 italic flex items-center gap-1">
                                                <i class="fa-solid fa-info-circle"></i> Quyền chỉnh sửa/hủy thuộc về trưởng nhóm.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Modern Footer Note --}}
                            <div class="mt-6 flex items-start gap-3 p-4 bg-blue-50/50 border border-blue-100 rounded-lg text-sm text-slate-600">
                                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                                <div>
                                    <p class="font-semibold text-blue-800 mb-1">Quy định chung:</p>
                                    <ul class="list-disc pl-4 space-y-1 text-slate-600 text-xs sm:text-sm">
                                        <li>Chỉ được hủy đăng ký <strong>trước khi</strong> cuộc thi chính thức bắt đầu.</li>
                                        <li>Hệ thống đóng cổng nộp bài ngay khi hết thời gian thi (theo giờ máy chủ).</li>
                                        <li>Đối với thi đội nhóm: Chỉ <strong>Trưởng nhóm</strong> mới có quyền nộp bài hoặc hủy đăng ký.</li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ⚙️ CÀI ĐẶT --}}
            <div x-show="tab==='settings'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Cài đặt tài khoản</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Đổi mật khẩu - Link đến trang riêng --}}
                        <a href="{{ route('password.change') }}"
                            class="flex items-center justify-between p-5 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-key text-blue-600 group-hover:text-white text-lg transition"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition">Đổi mật khẩu</p>
                                    <p class="text-sm text-gray-500">Cập nhật mật khẩu bảo mật</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition"></i>
                        </a>

                        {{-- Đăng xuất --}}
                        <div class="pt-6 mt-6 border-t border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center justify-between p-5 border-2 border-red-200 rounded-xl hover:border-red-400 hover:bg-red-50 transition group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-red-100 group-hover:bg-red-600 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-right-from-bracket text-red-600 group-hover:text-white text-lg transition"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-red-600 group-hover:text-red-700 transition">Đăng xuất</p>
                                            <p class="text-sm text-gray-500">Thoát khỏi tài khoản</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-red-400 group-hover:text-red-600 transition"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</section>

@endsection