@extends('layouts.client')
@section('title', 'Trang chủ')

@section('content')

{{-- 🖼️ CAROUSEL SECTION (auto-slide 5s) --}}
<section
    x-data="{
        active: 0,
        slides: [
            '{{ asset('images/home/banner1.png') }}',
            '{{ asset('images/home/banner2.png') }}',
            '{{ asset('images/home/banner3.jpg') }}'
        ],
        interval: null,
        start() {
            this.interval = setInterval(() => {
                this.active = (this.active + 1) % this.slides.length;
            }, 5000);
        },
        stop() { clearInterval(this.interval); }
    }"
    x-init="start()"
    class="relative w-full h-[87vh] overflow-hidden"
    @mouseenter="stop()" @mouseleave="start()">

    {{-- Slides --}}
    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="active === index"
            x-transition:enter="transition-opacity ease-out duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0">
            <div class="relative w-full h-full">
                <img :src="slide"
                    class="w-full h-full object-cover brightness-[0.80] transition-transform duration-[5000ms] scale-100 group-[.active]:scale-105">
            </div>
        </div>
    </template>

    {{-- Controls --}}
    <button @click="active = active === 0 ? slides.length - 1 : active - 1"
        class="absolute left-6 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white rounded-full w-11 h-11 flex items-center justify-center transition">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <button @click="active = active === slides.length - 1 ? 0 : active + 1"
        class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white rounded-full w-11 h-11 flex items-center justify-center transition">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    {{-- Indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
        <template x-for="(slide, i) in slides" :key="i">
            <button @click="active = i"
                :class="active === i ? 'bg-white w-6' : 'bg-white/50 w-3'"
                class="h-3 rounded-full transition-all duration-300"></button>
        </template>
    </div>
</section>


{{-- 🎓 HERO + FEATURED EVENTS --}}
<section class="relative bg-gradient-to-br from-blue-50 via-white to-cyan-50 overflow-hidden px-[100px] ">
    <!-- Background subtle pattern -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 80%, #3b82f6 1px, transparent 1px), radial-gradient(circle at 80% 20%, #06b6d4 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <div class="container px-6 py-20 md:py-28 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-start">

            {{-- Left content --}}
            <div class="space-y-7 max-w-xl">
                {{-- Badge --}}
                <div class="inline-flex items-center space-x-2 bg-blue-100 text-blue-700 text-xs font-semibold px-4 py-2 rounded-full">
                    <i class="fa-solid fa-bolt text-yellow-400"></i>
                    <span>Khám phá – Học hỏi – Tỏa sáng cùng CNTT</span>
                </div>

                {{-- Main Heading --}}
                <h1 class="text-5xl md:text-6xl xl:text-7xl font-black text-gray-900 leading-tight">
                    <span class="block mb-2 text-blue-700">Cuộc thi Học thuật</span>
                    <span class="block text-gray-800">Dành cho sinh viên CNTT</span>
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed">
                    Tham gia các cuộc thi học thuật, hội thảo và hoạt động chuyên môn để nâng cao kỹ năng, kết nối cộng đồng và khẳng định bản lĩnh sinh viên Công nghệ Thông tin.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('client.events') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold px-7 py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        <i class="fa-solid fa-trophy"></i>
                        <span>Xem cuộc thi</span>
                    </a>
                    <a href="#about"
                        class="inline-flex items-center gap-2 border-2 border-blue-600 text-blue-600 hover:bg-blue-50 font-bold px-7 py-3.5 rounded-xl transition-all hover:scale-105">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Tìm hiểu thêm</span>
                    </a>
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-8 pt-6 border-t border-gray-200">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">15+</div>
                        <div class="text-sm text-gray-600">Cuộc thi mỗi năm</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-cyan-600">500+</div>
                        <div class="text-sm text-gray-600">Sinh viên tham gia</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-indigo-600">50+</div>
                        <div class="text-sm text-gray-600">Giải thưởng & chứng nhận</div>
                    </div>
                </div>
            </div>

            {{-- Right content: Featured Events --}}
            <div class="grid gap-6 ml-5">
                <div class="text-center mb-5">
                    <div class="inline-block mb-3">
                        <span class="bg-white text-blue-700 border border-blue-700 text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                            Sự kiện nổi bật
                        </span>
                    </div>
                </div>
                {{-- Event 1 --}}
                <div class="relative group rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="https://source.unsplash.com/600x400/?coding,competition"
                        alt="Sự kiện 1"
                        class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                        <h3 class="text-lg font-semibold mb-1">AI Innovation Contest 2025</h3>
                        <p class="text-xs text-gray-200 mb-3">Khám phá tiềm năng trí tuệ nhân tạo</p>
                        <a href="{{ route('client.events') }}"
                            class="text-sm bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-2 rounded-full font-medium hover:from-blue-700 hover:to-cyan-600 transition-all">
                            Xem chi tiết <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                {{-- Event 2 --}}
                <div class="relative group rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500">
                    <img src="https://source.unsplash.com/600x400/?web,design,students"
                        alt="Sự kiện 2"
                        class="w-full h-60 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                        <h3 class="text-lg font-semibold mb-1">Hội thi Thiết kế Web 2025</h3>
                        <p class="text-xs text-gray-200 mb-3">Thể hiện sáng tạo trong lập trình giao diện</p>
                        <a href="{{ route('client.events') }}"
                            class="text-sm bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-2 rounded-full font-medium hover:from-blue-700 hover:to-cyan-600 transition-all">
                            Xem chi tiết <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave bottom --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-24">
            <path d="M0 100L60 85C120 70 240 40 360 35C480 30 600 50 720 55C840 60 960 50 1080 40C1200 30 1320 20 1380 15L1440 10V100H0Z" fill="white" />
        </svg>
    </div>
</section>


{{-- ABOUT SECTION --}}
<section id="about" class="relative py-24 overflow-hidden">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-20">
            <div class="inline-block mb-4">
                <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                    Tính năng dành cho sinh viên
                </span>
            </div>
            <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6 leading-[1.3] tracking-tight">
                <span class="block mb-3">Tham gia Cuộc thi Học thuật</span>
                <span class="block bg-gradient-to-r pb-3 from-blue-600 via-cyan-600 to-blue-500 bg-clip-text text-transparent">
                    Dễ dàng - Nhanh chóng - Hiệu quả
                </span>
            </h2>
            <p class="max-w-2xl mx-auto text-gray-600 text-lg">
                Hệ thống giúp sinh viên Khoa CNTT dễ dàng tìm kiếm, đăng ký và theo dõi các cuộc thi học thuật.
                Mọi thông tin, kết quả và chứng nhận đều được quản lý tập trung, hiện đại và minh bạch.
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
                        <i class="fa-solid fa-trophy text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Đăng ký cuộc thi dễ dàng</h3>
                    <p class="text-blue-50 leading-relaxed">
                        Sinh viên có thể xem danh sách các cuộc thi học thuật đang mở, xem chi tiết thể lệ,
                        và đăng ký tham gia trực tuyến chỉ trong vài bước.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="group relative bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-user-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Theo dõi tiến trình & lịch thi</h3>
                    <p class="text-cyan-50 leading-relaxed">
                        Sau khi đăng ký, sinh viên có thể theo dõi lịch thi, cập nhật thông báo
                        và kết quả trực tiếp trên hệ thống mọi lúc, mọi nơi.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="group relative bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">Nhận chứng nhận & thành tích</h3>
                    <p class="text-indigo-50 leading-relaxed">
                        Sinh viên đạt giải hoặc hoàn thành cuộc thi sẽ nhận được chứng nhận điện tử
                        và được ghi nhận thành tích học thuật trực tuyến.
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
                <h4 class="font-bold text-lg mb-2 text-gray-900">Thông báo tức thì</h4>
                <p class="text-gray-600 text-sm">
                    Hệ thống gửi thông báo nhanh khi có cuộc thi mới, thay đổi lịch hoặc công bố kết quả.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-book-open text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Xem lại kết quả & đề thi</h4>
                <p class="text-gray-600 text-sm">
                    Sinh viên có thể xem lại bài thi, kết quả hoặc thống kê điểm để rút kinh nghiệm cho kỳ thi sau.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-award text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Vinh danh sinh viên xuất sắc</h4>
                <p class="text-gray-600 text-sm">
                    Sinh viên đạt giải cao được hiển thị trong bảng vinh danh của Khoa và nhận giấy chứng nhận.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
                </div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Phát triển kỹ năng học thuật</h4>
                <p class="text-gray-600 text-sm">
                    Tham gia các cuộc thi giúp sinh viên rèn luyện tư duy, sáng tạo và nâng cao kỹ năng chuyên môn.
                </p>
            </div>
        </div>
    </div>
</section>



<section class="border-y border-gray-300 mx-[100px] ">
    <div class="container mx-auto px-6 py-10 grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">

        {{-- Hội thảo --}}
        <div class="flex flex-col items-center sm:flex-row sm:justify-center sm:space-x-4">
            <div class="flex items-center justify-center flow-hidden">
                <img src="{{ asset('images/home/seminar.png') }}" alt="Hội thảo" class="w-20 h-20 object-contain">
            </div>
            <div class="mt-3 sm:mt-0">
                <h3 class="text-4xl font-bold text-blue-700">150+</h3>
                <p class="text-gray-600 text-2xl font-medium">Hội thảo</p>
            </div>
        </div>

        {{-- Sinh viên --}}
        <div class="flex flex-col items-center sm:flex-row sm:justify-center sm:space-x-4 border-t sm:border-t-0  border-gray-200">
            <div class="flex items-center justify-center  overflow-hidden">
                <img src="{{ asset('images/home/student.png') }}" alt="Sinh viên" class="w-20 h-20 object-contain">
            </div>
            <div class="mt-3 sm:mt-0">
                <h3 class="text-4xl font-bold text-blue-700">2.5K+</h3>
                <p class="text-gray-600 text-2xl font-medium">Sinh viên</p>
            </div>
        </div>

        {{-- Giảng viên --}}
        <div class="flex flex-col items-center sm:flex-row sm:justify-center sm:space-x-4 border-t sm:border-t-0 border-gray-200">
            <div class="flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/home/teacher.png') }}" alt="Giảng viên" class="w-20 h-20 object-contain">
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