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

    <div class="grid lg:grid-cols-4 gap-8 relative" x-data="{ tab: 'info' }">

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
                    <button @click="tab='info'"
                        :class="tab==='info' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition">
                        <i class="fas fa-user w-5"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

                    @if($user->vaitro === 'SinhVien')
                        <button @click="tab='activities'"
                            :class="tab==='activities' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                            <i class="fas fa-trophy w-5"></i>
                            <span>Hoạt động học thuật</span>
                        </button>

                        <button @click="tab='points'"
                            :class="tab==='points' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Điểm rèn luyện</span>
                        </button>

                        <button @click="tab='certs'"
                            :class="tab==='certs' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                            <i class="fas fa-certificate w-5"></i>
                            <span>Chứng nhận</span>
                        </button>
                    @endif

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
            <div x-show="tab==='activities'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Hoạt động học thuật</h3>
                    </div>

                    @if($activities->count() > 0)
                        <div class="space-y-4">
                            @foreach($activities as $activity)
                                <div class="border border-gray-200 rounded-xl p-5 hover:border-{{ $activity['color'] }}-300 hover:bg-{{ $activity['color'] }}-50 transition">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-{{ $activity['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <h4 class="font-bold text-gray-800 mb-1">{{ $activity['title'] }}</h4>
                                                    @if($activity['subtitle'])
                                                        <p class="text-sm text-gray-600 mb-2">{{ $activity['subtitle'] }}</p>
                                                    @endif
                                                    <div class="flex flex-wrap gap-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <i class="fas fa-user-tag mr-1.5"></i>{{ $activity['role'] }}
                                                        </span>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            {{ $activity['status'] === 'Active' || $activity['status'] === 'Approved' || $activity['status'] === 'Registered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                            {{ $activity['status'] }}
                                                        </span>
                                                        @if(isset($activity['diem_danh']))
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                <i class="fas fa-qrcode mr-1.5"></i>{{ $activity['diem_danh'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-500 whitespace-nowrap ml-4">
                                                    {{ \Carbon\Carbon::parse($activity['date'])->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-trophy text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Chưa có hoạt động học thuật nào</p>
                            <p class="text-gray-400 text-sm mt-2">Hãy tham gia các cuộc thi để tích lũy kinh nghiệm!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 📊 ĐIỂM RÈN LUYỆN --}}
            <div x-show="tab==='points'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Điểm rèn luyện</h3>
                        <a href="{{ route('profile.diem.export') }}" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition text-sm">
                            <i class="fas fa-download mr-2"></i>Xuất PDF
                        </a>
                    </div>

                    @if(isset($diemRenLuyen['details']) && $diemRenLuyen['details']->count() > 0)
                        {{-- Tổng điểm --}}
                        <div class="grid md:grid-cols-3 gap-4 mb-8">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                                <p class="text-blue-100 text-sm mb-1">Điểm cơ bản</p>
                                <p class="text-3xl font-bold">{{ $diemRenLuyen['base'] }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                                <p class="text-green-100 text-sm mb-1">Điểm cộng thêm</p>
                                <p class="text-3xl font-bold">+{{ $diemRenLuyen['bonus'] }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                                <p class="text-purple-100 text-sm mb-1">Tổng điểm</p>
                                <p class="text-3xl font-bold">{{ $diemRenLuyen['final'] }}</p>
                            </div>
                        </div>

                        {{-- Chi tiết điểm --}}
                        <h4 class="font-bold text-lg text-gray-800 mb-4">Chi tiết điểm cộng</h4>
                        <div class="space-y-3">
                            @foreach($diemRenLuyen['details'] as $detail)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">{{ $detail['title'] }}</p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($detail['ngay'])->format('d/m/Y') }}
                                            </span>
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                                {{ $detail['loai'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">+{{ $detail['diem'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Chưa có điểm rèn luyện</p>
                            <p class="text-gray-400 text-sm mt-2">Tham gia hoạt động để tích lũy điểm!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🎓 CHỨNG NHẬN --}}
            <div x-show="tab==='certs'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Chứng nhận & Giải thưởng</h3>
                    </div>

                    @if($certificates->count() > 0)
                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach($certificates as $cert)
                                <div class="border-2 border-yellow-200 rounded-xl p-6 bg-gradient-to-br from-yellow-50 to-orange-50 hover:shadow-lg transition">
                                    <div class="flex items-start gap-4">
                                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-trophy text-white text-2xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-lg text-gray-800 mb-1">{{ $cert['award'] }}</h4>
                                            <p class="text-sm text-gray-600 mb-2">{{ $cert['event'] }}</p>
                                            <p class="text-sm text-gray-700 font-medium mb-2">
                                                <i class="fas fa-gift mr-1.5 text-orange-500"></i>{{ $cert['prize'] }}
                                            </p>
                                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-yellow-200">
                                                <span class="text-xs text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($cert['date'])->format('d/m/Y') }}
                                                </span>
                                                <span class="text-sm font-bold text-green-600">
                                                    +{{ $cert['points'] }} điểm RL
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-certificate text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Chưa có chứng nhận nào</p>
                            <p class="text-gray-400 text-sm mt-2">Hãy cố gắng đạt giải trong các cuộc thi!</p>
                        </div>
                    @endif
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