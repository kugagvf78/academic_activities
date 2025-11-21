@extends('layouts.client')

@section('title', 'Hồ sơ Giảng viên')

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

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-4 gap-8 relative" x-data="{ tab: 'info' }">

        {{-- 👤 LEFT SIDEBAR --}}
        <aside class="lg:col-span-1 sticky top-28 self-start">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

                {{-- Profile Header --}}
                <div class="px-6 pb-3 pt-6 text-center border-b border-gray-300 mx-8">
                    <div class="relative inline-block mb-4">
                        @php
                            $avatarUrl = $giangvien->nguoiDung->anhdaidien 
                                ? Storage::url($giangvien->nguoiDung->anhdaidien)
                                : asset('images/users/avt.jpg');
                        @endphp
                        <img src="{{ $avatarUrl }}"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover mx-auto"
                            alt="Avatar">
                        <button onclick="document.getElementById('avatar-input').click()"
                            class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white shadow-lg transition">
                            <i class="fas fa-camera text-xs"></i>
                        </button>
                        
                        {{-- Hidden file input --}}
                        <form action="{{ route('giangvien.profile.avatar.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                            @csrf
                            <input type="file" id="avatar-input" name="Avatar" class="hidden" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                        </form>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 mb-1">{{ $giangvien->nguoiDung->hoten ?? $giangvien->nguoiDung->tendangnhap }}</h2>
                    
                    <p class="text-sm text-gray-500 mb-1">MSGV: {{ $giangvien->magiangvien }}</p>
                    @if($giangvien->chucvu)
                        <p class="text-sm text-gray-600 font-medium">{{ $giangvien->chucvu }}</p>
                    @endif
                </div>

                {{-- Navigation Menu --}}
                <nav class="flex flex-col text-left mt-3">
                    <button @click="tab='info'"
                        :class="tab==='info' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition">
                        <i class="fas fa-user w-5"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

                    <button @click="tab='stats'"
                        :class="tab==='stats' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Thống kê</span>
                    </button>

                    <button @click="tab='settings'"
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
                        <h3 class="text-2xl font-bold text-gray-800">Thông tin Giảng viên</h3>
                    </div>

                    <form action="{{ route('giangvien.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Thông tin chung --}}
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            {{-- Mã giảng viên --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mã Giảng viên</label>
                                <input type="text" 
                                    value="{{ $giangvien->magiangvien }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                    readonly>
                            </div>

                            {{-- Họ tên --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="HoTen" 
                                    value="{{ old('HoTen', $giangvien->nguoiDung->hoten) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('HoTen') border-red-500 @enderror"
                                    placeholder="Nhập họ tên đầy đủ" required>
                                @error('HoTen')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="Email" 
                                    value="{{ old('Email', $giangvien->nguoiDung->email) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('Email') border-red-500 @enderror"
                                    placeholder="example@gmail.com" required>
                                @error('Email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Số điện thoại --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại</label>
                                <input type="text" name="SoDienThoai" 
                                    value="{{ old('SoDienThoai', $giangvien->nguoiDung->sodienthoai) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="0123456789">
                            </div>

                            {{-- Bộ môn (read-only) --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bộ môn</label>
                                <input type="text" 
                                    value="{{ $giangvien->boMon->tenbomon ?? 'Chưa có' }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                    readonly>
                            </div>

                            {{-- Chức vụ --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chức vụ</label>
                                <input type="text" name="ChucVu" 
                                    value="{{ old('ChucVu', $giangvien->chucvu) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Giảng viên chính">
                            </div>

                            {{-- Học vị --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Học vị</label>
                                <select name="HocVi" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    <option value="">-- Chọn học vị --</option>
                                    <option value="Thạc sĩ" {{ $giangvien->hocvi == 'Thạc sĩ' ? 'selected' : '' }}>Thạc sĩ</option>
                                    <option value="Tiến sĩ" {{ $giangvien->hocvi == 'Tiến sĩ' ? 'selected' : '' }}>Tiến sĩ</option>
                                    <option value="Giáo sư" {{ $giangvien->hocvi == 'Giáo sư' ? 'selected' : '' }}>Giáo sư</option>
                                </select>
                            </div>

                            {{-- Vai trò (read-only) --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Vai trò</label>
                                <input type="text" value="Giảng viên"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                    readonly>
                            </div>

                            {{-- Chuyên môn --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chuyên môn</label>
                                <textarea name="ChuyenMon" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Mô tả về chuyên môn...">{{ old('ChuyenMon', $giangvien->chuyenmon) }}</textarea>
                            </div>
                        </div>

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

            {{-- 📊 THỐNG KÊ --}}
            <div x-show="tab==='stats'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Thống kê công việc</h3>
                    </div>

                    {{-- Statistics Cards --}}
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm mb-1">Cuộc Thi Tạo</p>
                                    <p class="text-3xl font-bold">{{ $stats['tong_cuoc_thi'] }}</p>
                                    <p class="text-blue-100 text-xs mt-2">Tổng số cuộc thi đã tạo</p>
                                </div>
                                <i class="fas fa-trophy text-5xl text-blue-200"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm mb-1">Đề Thi</p>
                                    <p class="text-3xl font-bold">{{ $stats['tong_de_thi'] }}</p>
                                    <p class="text-green-100 text-xs mt-2">Tổng số đề thi đã tạo</p>
                                </div>
                                <i class="fas fa-file-alt text-5xl text-green-200"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100 text-sm mb-1">Cần Chấm</p>
                                    <p class="text-3xl font-bold">{{ $stats['tong_bai_can_cham'] }}</p>
                                    <p class="text-yellow-100 text-xs mt-2">Bài thi đang chờ chấm điểm</p>
                                </div>
                                <i class="fas fa-edit text-5xl text-yellow-200"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm mb-1">Công Việc</p>
                                    <p class="text-3xl font-bold">{{ $stats['tong_phan_cong'] }}</p>
                                    <p class="text-purple-100 text-xs mt-2">Tổng số công việc được giao</p>
                                </div>
                                <i class="fas fa-tasks text-5xl text-purple-200"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div class="grid md:grid-cols-2 gap-4">
                        <a href="{{ route('giangvien.cuocthi.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-trophy text-blue-600 group-hover:text-white transition"></i>
                                </div>
                                <span class="font-medium text-gray-700 group-hover:text-blue-600 transition">Quản lý Cuộc thi</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition"></i>
                        </a>

                        <a href="{{ route('giangvien.dethi.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 group-hover:bg-green-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-file-alt text-green-600 group-hover:text-white transition"></i>
                                </div>
                                <span class="font-medium text-gray-700 group-hover:text-green-600 transition">Quản lý Đề thi</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600 transition"></i>
                        </a>

                        <a href="{{ route('giangvien.chamdiem.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-orange-300 hover:bg-orange-50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 group-hover:bg-orange-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-edit text-orange-600 group-hover:text-white transition"></i>
                                </div>
                                <span class="font-medium text-gray-700 group-hover:text-orange-600 transition">Chấm bài thi</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600 transition"></i>
                        </a>

                        <a href="{{ route('giangvien.phancong.index') }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-purple-300 hover:bg-purple-50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-tasks text-purple-600 group-hover:text-white transition"></i>
                                </div>
                                <span class="font-medium text-gray-700 group-hover:text-purple-600 transition">Phân công</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600 transition"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ⚙️ CÀI ĐẶT --}}
            <div x-show="tab==='settings'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Cài đặt tài khoản</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Đổi mật khẩu --}}
                        <a href="{{ route('password.change.view') }}"
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