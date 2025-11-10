@extends('layouts.client')
@section('title', 'Hồ sơ cá nhân')

@section('content')

{{-- 🎓 HEADER --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight">Hồ sơ cá nhân</h1>
        <p class="text-blue-100 text-lg">Quản lý thông tin, hoạt động học thuật và điểm rèn luyện của bạn tại Khoa CNTT 💙</p>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z"/>
        </svg>
    </div>
</section>

{{-- 🧾 PROFILE BODY --}}
<section class="container mx-auto px-6 py-16" x-data="{ tab: 'info' }">
    
    {{-- Tabs Header --}}
    <div class="flex flex-wrap justify-center gap-4 mb-12">
        <button @click="tab='info'" 
            :class="tab==='info' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-6 py-2 rounded-full font-semibold transition-all">Thông tin cá nhân</button>
        <button @click="tab='activities'" 
            :class="tab==='activities' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-6 py-2 rounded-full font-semibold transition-all">Hoạt động học thuật</button>
        <button @click="tab='points'" 
            :class="tab==='points' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-6 py-2 rounded-full font-semibold transition-all">Điểm rèn luyện</button>
        <button @click="tab='certs'" 
            :class="tab==='certs' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-6 py-2 rounded-full font-semibold transition-all">Chứng nhận</button>
        <button @click="tab='settings'" 
            :class="tab==='settings' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-6 py-2 rounded-full font-semibold transition-all">Cài đặt</button>
    </div>

    {{-- 🧍 THÔNG TIN CÁ NHÂN --}}
    <div x-show="tab==='info'" x-transition>
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 flex flex-col md:flex-row gap-6 items-center">
            <img src="{{ asset('images/default-avatar.png') }}" class="w-28 h-28 rounded-full border-4 border-blue-100 object-cover" alt="Avatar">

            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Nguyễn Văn A</h2>
                <p class="text-gray-600 mb-1"><strong>MSSV:</strong> 20123456</p>
                <p class="text-gray-600 mb-1"><strong>Lớp:</strong> DHCNTT17A</p>
                <p class="text-gray-600 mb-1"><strong>Ngành:</strong> Công nghệ thông tin</p>
                <p class="text-gray-600 mb-1"><strong>Email:</strong> nguyenvana@stu.huit.edu.vn</p>
                <p class="text-gray-600"><strong>SĐT:</strong> 0901234567</p>
            </div>

            <a href="{{ route('password.change') }}" 
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                <i class="fa-solid fa-pen mr-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>

    {{-- 🏆 HOẠT ĐỘNG HỌC THUẬT --}}
    <div x-show="tab==='activities'" x-transition>
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Hoạt động học thuật đã tham gia</h3>

            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-6">
                @foreach (range(1,6) as $i)
                <div class="border rounded-xl p-5 hover:shadow-md transition group">
                    <div class="flex justify-between mb-2">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">Đã hoàn thành</span>
                        <i class="fa-solid fa-trophy text-yellow-500"></i>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-1 group-hover:text-blue-600">Database Design Challenge #{{ $i }}</h4>
                    <p class="text-gray-600 text-sm mb-2">07/12/2025</p>
                    <p class="text-gray-500 text-sm">Vai trò: <span class="font-medium text-blue-700">Thí sinh</span></p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 📊 ĐIỂM RÈN LUYỆN --}}
    <div x-show="tab==='points'" x-transition>
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Tổng điểm rèn luyện</h3>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 flex justify-between items-center mb-6">
                <p class="text-gray-700 font-medium">Tổng điểm cộng hiện tại:</p>
                <p class="text-blue-700 font-bold text-2xl">+25 điểm</p>
            </div>

            <ul class="divide-y divide-gray-100 text-sm">
                <li class="py-3 flex justify-between">
                    <span>Tham gia Database Design Challenge</span>
                    <span class="text-green-600 font-semibold">+10</span>
                </li>
                <li class="py-3 flex justify-between">
                    <span>Cổ vũ AI Innovation Contest</span>
                    <span class="text-green-600 font-semibold">+5</span>
                </li>
                <li class="py-3 flex justify-between">
                    <span>Hỗ trợ tổ chức Web Dev Challenge</span>
                    <span class="text-green-600 font-semibold">+10</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- 📜 CHỨNG NHẬN --}}
    <div x-show="tab==='certs'" x-transition>
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Chứng nhận & Giấy khen</h3>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="border rounded-xl p-5 flex justify-between items-center hover:shadow-md transition">
                    <div>
                        <p class="font-semibold text-gray-800">Giấy chứng nhận tham dự</p>
                        <p class="text-sm text-gray-500">Database Design Challenge 2025</p>
                    </div>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                        <i class="fa-solid fa-download"></i>Tải
                    </a>
                </div>

                <div class="border rounded-xl p-5 flex justify-between items-center hover:shadow-md transition">
                    <div>
                        <p class="font-semibold text-gray-800">Giấy khen “Sinh viên năng động”</p>
                        <p class="text-sm text-gray-500">AI Innovation 2025</p>
                    </div>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                        <i class="fa-solid fa-download"></i>Tải
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ⚙️ CÀI ĐẶT --}}
    <div x-show="tab==='settings'" x-transition>
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 max-w-2xl mx-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Cài đặt tài khoản</h3>

            <div class="space-y-5">
                <a href="{{ route('password.change') }}" class="flex items-center gap-3 text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-key text-lg"></i><span>Đổi mật khẩu</span>
                </a>
                <a href="#" class="flex items-center gap-3 text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-user-pen text-lg"></i><span>Chỉnh sửa thông tin cá nhân</span>
                </a>
                <a href="#" class="flex items-center gap-3 text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-image text-lg"></i><span>Thay ảnh đại diện</span>
                </a>
                <a href="#" class="flex items-center gap-3 text-red-600 hover:text-red-700">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i><span>Đăng xuất</span>
                </a>
            </div>
        </div>
    </div>

</section>

@endsection
