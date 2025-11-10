@extends('layouts.client')
@section('title', 'Hồ sơ cá nhân')

@section('content')

{{-- 🎓 HEADER --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-20 pb-24 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 80%, #fff 1px, transparent 1px), radial-gradient(circle at 80% 20%, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Hồ sơ cá nhân</h1>
            <p class="text-blue-100 text-lg">Quản lý thông tin, hoạt động học thuật và điểm rèn luyện của bạn</p>
        </div>
    </div>
</section>

{{-- 📋 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-4 gap-8" x-data="{ tab: 'info' }">

        {{-- 👤 LEFT SIDEBAR - Profile Section (Profile Card + Navigation Menu) --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

                {{-- Profile Header --}}
                <div class="px-6 p text-center border-b border-gray-300 mx-4 ">
                    <div class="relative inline-block mb-4">
                        <img src="{{ asset('images/default-avatar.png') }}"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover mx-auto"
                            alt="Avatar">
                        <button class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white shadow-lg transition">
                            <i class="fas fa-camera text-xs"></i>
                        </button>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 mb-1">Nguyễn Văn A</h2>
                    <p class="text-sm text-gray-500 mb-1">MSSV: 20123456</p>
                    <p class="text-sm text-gray-600 font-medium">DHCNTT17A</p>

                </div>

                {{-- Navigation Menu --}}
                <nav class="flex flex-col text-left">
                    <button @click="tab='info'"
                        :class="tab==='info' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition">
                        <i class="fas fa-user w-5"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

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

                    <button @click="tab='settings'"
                        :class="tab==='settings' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100">
                        <i class="fas fa-cog w-5"></i>
                        <span>Cài đặt</span>
                    </button>

                    <a href="#"
                        class="w-full px-6 py-4 flex items-center gap-3 font-medium transition border-t border-gray-100 text-red-600 hover:bg-red-50">
                        <i class="fas fa-right-from-bracket w-5"></i>
                        <span>Đăng xuất</span>
                    </a>
                </nav>

                {{-- Quick Stats Footer --}}
                <div class="bg-gradient-to-br from-blue-600 to-cyan-500 text-white p-6 mt-4 rounded-b-2xl">
                    <h3 class="text-sm font-semibold mb-4 opacity-90">Thống kê nhanh</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Hoạt động tham gia</span>
                            <span class="font-bold text-xl">12</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Điểm rèn luyện</span>
                            <span class="font-bold text-xl">+25</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Chứng nhận</span>
                            <span class="font-bold text-xl">8</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- 📄 RIGHT CONTENT AREA --}}
        <main class="lg:col-span-3">

            {{-- 🧍 THÔNG TIN CÁ NHÂN --}}
            <div x-show="tab==='info'" x-transition class="space-y-6">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Thông tin cá nhân</h3>
                        <a href="{{ route('password.change') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition text-sm font-semibold inline-flex items-center gap-2">
                            <i class="fas fa-pen"></i>
                            <span>Chỉnh sửa</span>
                        </a>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Họ và tên</label>
                            <p class="text-gray-800 font-semibold">Nguyễn Văn A</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Mã số sinh viên</label>
                            <p class="text-gray-800 font-semibold">20123456</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Lớp</label>
                            <p class="text-gray-800 font-semibold">DHCNTT17A</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Ngành học</label>
                            <p class="text-gray-800 font-semibold">Công nghệ thông tin</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Email</label>
                            <p class="text-gray-800 font-semibold">nguyenvana@stu.huit.edu.vn</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Số điện thoại</label>
                            <p class="text-gray-800 font-semibold">0901234567</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Ngày sinh</label>
                            <p class="text-gray-800 font-semibold">01/01/2003</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 block mb-2">Giới tính</label>
                            <p class="text-gray-800 font-semibold">Nam</p>
                        </div>
                    </div>
                </div>

                {{-- Academic Info --}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Thông tin học tập</h3>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Khóa học</p>
                            <p class="text-2xl font-bold text-blue-600">2020-2024</p>
                        </div>

                        <div class="text-center p-4 bg-emerald-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Năm hiện tại</p>
                            <p class="text-2xl font-bold text-emerald-600">Năm 3</p>
                        </div>

                        <div class="text-center p-4 bg-amber-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">GPA</p>
                            <p class="text-2xl font-bold text-amber-600">3.45</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🏆 HOẠT ĐỘNG HỌC THUẬT --}}
            <div x-show="tab==='activities'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Hoạt động học thuật</h3>
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">12 hoạt động</span>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        @foreach (range(1,6) as $i)
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-blue-200 transition group">
                            <div class="flex justify-between items-start mb-3">
                                <span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full">Đã hoàn thành</span>
                                <i class="fas fa-trophy text-yellow-500 text-lg"></i>
                            </div>

                            <h4 class="font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition">
                                Database Design Challenge #{{ $i }}
                            </h4>

                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="far fa-calendar text-blue-500 w-4"></i>
                                    <span>07/12/2025</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tag text-purple-500 w-4"></i>
                                    <span>Vai trò: <strong class="text-blue-700">Thí sinh</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-star text-amber-500 w-4"></i>
                                    <span>Điểm: <strong class="text-emerald-600">+10</strong></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination placeholder --}}
                    <div class="mt-8 flex justify-center">
                        <nav class="flex gap-2">
                            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                                <i class="fas fa-chevron-left text-sm"></i>
                            </button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white font-semibold">1</button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">2</button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">3</button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- 📊 ĐIỂM RÈN LUYỆN --}}
            <div x-show="tab==='points'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Điểm rèn luyện</h3>
                    </div>

                    {{-- Summary Card --}}
                    <div class="bg-gradient-to-br from-blue-600 to-cyan-500 rounded-xl p-8 mb-8 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 mb-2">Tổng điểm cộng hiện tại</p>
                                <p class="text-5xl font-bold">+25</p>
                                <p class="text-blue-100 mt-2 text-sm">Học kỳ 1, năm học 2024-2025</p>
                            </div>
                            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-chart-line text-5xl"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Points Breakdown --}}
                    <h4 class="font-bold text-lg text-gray-800 mb-4">Chi tiết điểm</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-semibold text-gray-800">Tham gia Database Design Challenge</p>
                                <p class="text-sm text-gray-500">07/12/2025</p>
                            </div>
                            <span class="text-emerald-600 font-bold text-lg">+10</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-semibold text-gray-800">Cổ vũ AI Innovation Contest</p>
                                <p class="text-sm text-gray-500">15/11/2025</p>
                            </div>
                            <span class="text-emerald-600 font-bold text-lg">+5</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-semibold text-gray-800">Hỗ trợ tổ chức Web Dev Challenge</p>
                                <p class="text-sm text-gray-500">22/10/2025</p>
                            </div>
                            <span class="text-emerald-600 font-bold text-lg">+10</span>
                        </div>
                    </div>

                    {{-- Export Button --}}
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition inline-flex items-center gap-2">
                            <i class="fas fa-download"></i>
                            <span>Xuất bảng điểm PDF</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 📜 CHỨNG NHẬN --}}
            <div x-show="tab==='certs'" x-transition>
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-800">Chứng nhận & Giấy khen</h3>
                        <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm font-semibold">8 chứng nhận</span>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-blue-200 transition group">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-certificate text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition">
                                        Giấy chứng nhận tham dự
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">Database Design Challenge 2025</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <i class="far fa-calendar"></i>
                                        <span>07/12/2025</span>
                                    </div>
                                </div>
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="fas fa-download text-lg"></i>
                                </a>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-amber-200 transition group">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-award text-amber-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition">
                                        Giấy khen "Sinh viên năng động"
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">AI Innovation 2025</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <i class="far fa-calendar"></i>
                                        <span>15/11/2025</span>
                                    </div>
                                </div>
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="fas fa-download text-lg"></i>
                                </a>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-emerald-200 transition group">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-trophy text-emerald-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition">
                                        Giải Ba - Web Dev Challenge
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">Cuộc thi Thiết kế Web 2025</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <i class="far fa-calendar"></i>
                                        <span>22/10/2025</span>
                                    </div>
                                </div>
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="fas fa-download text-lg"></i>
                                </a>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-purple-200 transition group">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-certificate text-purple-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition">
                                        Chứng nhận hoàn thành khóa học
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">Machine Learning Basics</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <i class="far fa-calendar"></i>
                                        <span>05/09/2025</span>
                                    </div>
                                </div>
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition">
                                    <i class="fas fa-download text-lg"></i>
                                </a>
                            </div>
                        </div>
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

                        <a href="#"
                            class="flex items-center justify-between p-5 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-100 group-hover:bg-emerald-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-user-pen text-emerald-600 group-hover:text-white text-lg transition"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition">Chỉnh sửa thông tin</p>
                                    <p class="text-sm text-gray-500">Cập nhật thông tin cá nhân</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition"></i>
                        </a>

                        <a href="#"
                            class="flex items-center justify-between p-5 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-image text-purple-600 group-hover:text-white text-lg transition"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition">Thay ảnh đại diện</p>
                                    <p class="text-sm text-gray-500">Tải lên ảnh mới</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition"></i>
                        </a>

                        <a href="#"
                            class="flex items-center justify-between p-5 border border-gray-200 rounded-xl hover:border-amber-300 hover:bg-amber-50 transition group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-amber-100 group-hover:bg-amber-600 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-bell text-amber-600 group-hover:text-white text-lg transition"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition">Thông báo</p>
                                    <p class="text-sm text-gray-500">Quản lý thông báo và email</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition"></i>
                        </a>

                        <div class="pt-6 mt-6 border-t border-gray-100">
                            <a href="#"
                                class="flex items-center justify-between p-5 border-2 border-red-200 rounded-xl hover:border-red-400 hover:bg-red-50 transition group">
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
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</section>

@endsection