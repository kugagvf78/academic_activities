@extends('layouts.client')
@section('title', 'Trang chủ')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br px-10 from-blue-50 via-white to-cyan-50 overflow-hidden">
    <!-- Background Subtle Pattern -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 80%, #3b82f6 1px, transparent 1px), radial-gradient(circle at 80% 20%, #06b6d4 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="space-y-7 max-w-xl">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 bg-blue-100 text-blue-700 text-xs font-semibold px-4 py-2 rounded-full">
                    <i class="fa-solid fa-circle-dot text-green-500"></i>
                    <span>Đang hoạt động – Năm học 2024-2025</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-5xl md:text-6xl xl:text-7xl font-black text-gray-900">
                    <span class="block mb-3">Quản lý</span>
                    <span class="block mb-5 text-blue-600">Hội thảo khoa CNTT</span>
                    <span class="block text-gray-800">Dễ dàng – Hiệu quả</span>
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed">
                    Hệ thống toàn diện giúp Khoa CNTT tổ chức, theo dõi và đánh giá các hội thảo học thuật một cách chuyên nghiệp và minh bạch.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('client.events') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-7 py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        <span>Xem hội thảo</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="#about" class="inline-flex items-center gap-2 border-2 border-blue-600 text-blue-600 hover:bg-blue-50 font-bold px-7 py-3.5 rounded-xl transition-all hover:scale-105">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Tìm hiểu thêm</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-8 pt-6 border-t border-gray-200">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">150+</div>
                        <div class="text-sm text-gray-600">Hội thảo</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-cyan-600">2.5K+</div>
                        <div class="text-sm text-gray-600">Sinh viên</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-indigo-600">80+</div>
                        <div class="text-sm text-gray-600">Giảng viên</div>
                    </div>
                </div>
            </div>

            <!-- Right Visual - Card đơn giản -->
            <div class="hidden lg:block">
                <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-calendar-days text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Sắp tới</p>
                                <h3 class="font-bold text-lg">AI & Machine Learning</h3>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Mở đăng ký</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">15/11</div>
                            <div class="text-xs text-gray-600">Ngày diễn ra</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-cyan-600">120</div>
                            <div class="text-xs text-gray-600">Đã đăng ký</div>
                        </div>
                    </div>

                    <button class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold py-3 rounded-xl transition-all hover:scale-105">
                        Đăng ký ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subtle Wave Bottom -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-24">
            <path d="M0 100L60 85C120 70 240 40 360 35C480 30 600 50 720 55C840 60 960 50 1080 40C1200 30 1320 20 1380 15L1440 10V100H0Z" fill="white" />
        </svg>
    </div>
</section>

{{-- ABOUT SECTION --}}
<section id="about" class="relative py-24  overflow-hidden">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-20">
            <div class="inline-block mb-4">
                <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                    Chức năng nổi bật
                </span>
            </div>
            <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6 leading-[1.3] tracking-tight">
                <span class="block mb-3">Hệ thống Quản lý Hội thảo</span>
                <span class="block bg-gradient-to-r pb-3 from-blue-600 via-cyan-600 to-blue-500 bg-clip-text text-transparent">
                    Dành cho Khoa Công nghệ Thông tin
                </span>
            </h2>
            <p class="max-w-2xl mx-auto text-gray-600 text-lg">
                Hệ thống được thiết kế nhằm hỗ trợ quản lý, tổ chức, và theo dõi các hoạt động hội thảo học thuật trong Khoa CNTT — đảm bảo tính minh bạch, tiện lợi và hiệu quả.
            </p>
        </div>

        <!-- Main Feature Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-20">
            <!-- Card 1 -->
            <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-calendar-days text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Tổ chức & Quản lý Hội thảo</h3>
                    <p class="text-blue-50 leading-relaxed">
                        Quản lý toàn bộ quy trình từ khâu lập kế hoạch, phê duyệt, triển khai cho đến tổng kết hội thảo.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="group relative bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Quản lý Người tham gia</h3>
                    <p class="text-cyan-50 leading-relaxed">
                        Theo dõi danh sách giảng viên, sinh viên đăng ký; quản lý điểm danh, vai trò và thành tích tham gia.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="group relative bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Thống kê & Báo cáo</h3>
                    <p class="text-indigo-50 leading-relaxed">
                        Cung cấp báo cáo tự động về số lượng hội thảo, mức độ tham gia, kết quả đánh giá và đề xuất cải tiến.
                    </p>
                </div>
            </div>
        </div>

        <!-- Secondary Features -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-bell text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Thông báo tự động</h4>
                <p class="text-gray-600 text-sm">Nhận thông báo nhanh về lịch, thay đổi hoặc kết quả hội thảo.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-database text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Lưu trữ dữ liệu</h4>
                <p class="text-gray-600 text-sm">Lưu trữ toàn bộ thông tin hội thảo, người tham gia và tài liệu.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-certificate text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Chứng nhận tham gia</h4>
                <p class="text-gray-600 text-sm">Cấp chứng nhận tự động cho sinh viên tham dự hội thảo.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-user-shield text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Phân quyền linh hoạt</h4>
                <p class="text-gray-600 text-sm">Quản lý quyền truy cập theo vai trò: Sinh viên, Giảng viên, Ban Chủ nhiệm.</p>
            </div>
        </div>
    </div>
</section>

<section class="border-y border-gray-200 mx-[100px]">
    <div class="container mx-auto px-6 py-8 grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
        
        {{-- 1️⃣ Hội thảo --}}
        <div class="flex flex-col items-center sm:flex-row sm:justify-center sm:space-x-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 text-blue-600 shadow-inner">
                <i class="fa-solid fa-chalkboard-user text-xl"></i>
            </div>
            <div class="mt-3 sm:mt-0">
                <h3 class="text-4xl font-bold text-blue-700">150+</h3>
                <p class="text-gray-600 text-2xl font-medium">Hội thảo</p>
            </div>
        </div>

        {{-- 2️⃣ Sinh viên --}}
        <div class="flex flex-col items-center sm:flex-row sm:justify-center sm:space-x-4 border-t sm:border-t-0">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 text-blue-600 shadow-inner">
                <i class="fa-solid fa-user-graduate text-xl"></i>
            </div>
            <div class="mt-3 sm:mt-0">
                <h3 class="text-4xl font-bold text-blue-700">2.5K+</h3>
                <p class="text-gray-600 text-2xl font-medium">Sinh viên</p>
            </div>
        </div>

        {{-- 3️⃣ Giảng viên --}}
        <div class="flex flex-col items-center sm:flex-row sm:justify-center sm:space-x-4 border-t sm:border-t-0">
            <div class="flex items-center justify-center w-12 h-12 to-cyan-100 text-blue-600 shadow-inner">
                <img src="" alt="">
            </div>
            <div class="mt-3 sm:mt-0">
                <h3 class="text-4xl font-bold text-blue-700">80+</h3>
                <p class="text-gray-600 text-2xl font-medium">Giảng viên</p>
            </div>
        </div>

    </div>
</section>


{{-- CONTACT SECTION --}}
<section id="contact" class="py-24">
    <div class="container mx-auto px-6">
        <!-- HEADER -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-primary mb-4">
                Liên hệ với Khoa Công nghệ Thông tin
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Hãy liên hệ với chúng tôi để được hỗ trợ nhanh chóng về các hội thảo, hoạt động học thuật và thông tin hệ thống.
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-10 max-w-6xl mx-auto">
            <!-- LEFT: CONTACT INFO -->
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl shadow">
                            <i class="fa-solid fa-location-dot text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Địa chỉ</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Khoa Công nghệ Thông tin<br>
                                Đại học Công Thương TP.HCM<br>
                                140 Lê Trọng Tấn, Tân Phú, TP.HCM
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl shadow">
                            <i class="fa-solid fa-phone text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Điện thoại</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                +84 (28) 3816 5673<br>
                                +84 (28) 3816 5674
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl shadow">
                            <i class="fa-solid fa-envelope text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Email</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                cntt@huit.edu.vn<br>
                                hoithao.cntt@huit.edu.vn
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 pt-2">
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- RIGHT: CONTACT FORM -->
            <div class="lg:col-span-2">
                <form class="bg-white border border-gray-200 rounded-3xl p-8 shadow-md hover:shadow-lg transition">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" placeholder="Nguyễn Văn A"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" placeholder="example@email.com"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" placeholder="0912 345 678"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Chủ đề</label>
                            <select
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition text-gray-700">
                                <option>Câu hỏi chung</option>
                                <option>Đăng ký hội thảo</option>
                                <option>Hỗ trợ kỹ thuật</option>
                                <option>Hợp tác</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung <span class="text-red-500">*</span></label>
                        <textarea rows="5" placeholder="Nhập nội dung bạn muốn gửi..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition resize-none text-gray-700"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-semibold py-3.5 rounded-lg shadow-md hover:shadow-xl transition-all">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Gửi liên hệ
                    </button>

                    <p class="text-center text-sm text-gray-500 mt-4">
                        Phản hồi sẽ được gửi trong vòng 24 giờ làm việc
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection