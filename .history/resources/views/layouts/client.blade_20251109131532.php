<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Hội thảo - Khoa CNTT</title>

    {{-- TailwindCSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#0ea5e9',
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.2);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, #3b82f6, #0ea5e9);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-link:hover::before {
            width: 80%;
        }
    </style>

    @stack('styles')
</head>

<body class="font-inter bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- HEADER / NAVBAR --}}
    <header class="bg-white/90 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-50 shadow-sm transition-all duration-300">
        <div class="container mx-auto flex items-center justify-between px-5 py-4 lg:px-8 lg:py-5">
            {{-- LOGO + BRAND --}}
            <a href="{{ route('client.home') }}" class="group flex items-center space-x-3 transition-all duration-300 hover:scale-[1.03]">
                <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-blue-400 p-0.5 shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <div class="w-full h-full rounded-2xl bg-white flex items-center justify-center">
                        <i class="fa-solid fa-graduation-cap text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="leading-tight">
                    <h1 class="text-lg font-black text-gray-900 tracking-tighter group-hover:text-blue-700 transition-colors">
                        Hội thảo CNTT
                    </h1>
                    <p class="text-xs font-bold text-cyan-600 tracking-wider">Khoa Công nghệ Thông tin</p>
                </div>
            </a>

            {{-- NAVIGATION - Desktop --}}
            <nav class="hidden lg:flex items-center space-x-10 font-medium text-gray-700">
                @php
                $navItems = [
                ['route' => 'client.home', 'icon' => 'fa-home', 'label' => 'Trang chủ'],
                ['route' => 'client.events', 'icon' => 'fa-calendar-days', 'label' => 'Hội thảo'],
                ['href' => '#news', 'icon' => 'fa-newspaper', 'label' => 'Tin tức'],
                ['href' => '#contact', 'icon' => 'fa-envelope', 'label' => 'Liên hệ'],
                ];
                @endphp

                @foreach ($navItems as $item)
                @if (isset($item['route']))
                <a href="{{ route($item['route']) }}"
                    class="group flex items-center gap-2 py-2 px-1 text-sm hover:text-blue-600 transition-all duration-300 {{ request()->routeIs($item['route']) ? 'text-blue-600 font-bold' : '' }}">
                    @else
                    <a href="{{ $item['href'] }}"
                        class="group flex items-center gap-2 py-2 px-1 text-sm hover:text-blue-600 transition-all duration-300">
                        @endif
                        <i class="fa-solid {{ $item['icon'] }} text-blue-500 text-base transition-transform group-hover:scale-125 group-hover:text-blue-600"></i>
                        <span class="relative">
                            {{ $item['label'] }}
                            @if (request()->routeIs($item['route'] ?? ''))
                            <span class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full"></span>
                            @endif
                        </span>
                    </a>
                    @endforeach
            </nav>

            {{-- AUTH BUTTONS - Desktop --}}
            <div class="hidden lg:flex items-center space-x-3">
                @auth
                <div class="flex items-center bg-gradient-to-r from-blue-50 to-cyan-50 rounded-full px-4 py-2 border border-blue-200/50 shadow-sm hover:shadow-md transition-all">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-user text-white text-xs"></i>
                    </div>
                    <span class="ml-2 text-sm font-bold text-gray-800">{{ Str::limit(Auth::user()->name, 12) }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button class="group flex items-center gap-2 px-5 py-2 rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white font-bold text-sm shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
                        <i class="fa-solid fa-right-from-bracket transition-transform group-hover:-rotate-12"></i>
                        Đăng xuất
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}"
                    class="group flex items-center gap-2 px-6 py-2.5 rounded-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <i class="fa-solid fa-right-to-bracket transition-transform group-hover:translate-x-1"></i>
                    Đăng nhập
                    </button>
                    @endauth
            </div>

            {{-- MOBILE MENU TOGGLE --}}
            <button class="lg:hidden p-2 rounded-xl hover:bg-gray-100 transition-all" id="mobile-menu-btn">
                <i class="fa-solid fa-bars text-xl text-gray-700"></i>
            </button>
        </div>

        {{-- MOBILE MENU - SIÊU ĐẸP --}}
        <div class="lg:hidden hidden bg-white/95 backdrop-blur-2xl border-t border-gray-100 shadow-2xl" id="mobile-menu">
            <div class="container mx-auto px-6 py-6 space-y-3">
                @foreach ($navItems as $item)
                @if (isset($item['route']))
                <a href="{{ route($item['route']) }}"
                    class="flex items-center gap-4 py-3.5 px-5 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all {{ request()->routeIs($item['route']) ? 'bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-600 font-bold' : 'text-gray-700' }}">
                    @else
                    <a href="{{ $item['href'] }}"
                        class="flex items-center gap-4 py-3.5 px-5 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all text-gray-700">
                        @endif
                        <i class="fa-solid {{ $item['icon'] }} text-blue-500 w-5"></i>
                        <span class="font-semibold text-base">{{ $item['label'] }}</span>
                    </a>
                    @endforeach

                    <div class="pt-4 border-t border-gray-200 mt-4">
                        @auth
                        <div class="flex items-center justify-between py-3 px-5 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center">
                                    <i class="fa-solid fa-user text-white text-sm"></i>
                                </div>
                                <span class="font-bold text-gray-800">{{ Str::limit(Auth::user()->name, 16) }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button class="text-red-500 font-bold text-sm hover:text-red-600 transition-colors">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                        @else
                        <a href="{{ route('login') }}"
                            class="block text-center py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold shadow-lg hover:shadow-xl transition-all">
                            Đăng nhập
                        </a>
                        @endauth
                    </div>
            </div>
        </div>
    </header>


    {{-- MAIN --}}
    <main class="flex-1">
        {{-- HERO SECTION --}}
        <section class="relative bg-gradient-to-br from-blue-50 via-white to-cyan-50 overflow-hidden">
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
                        <h1 class="text-5xl md:text-6xl font-black text-gray-900 leading-tight">
                            Quản lý
                            <span class="block text-blue-600">Hội thảo CNTT</span>
                            Dễ dàng – Hiệu quả
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

        {{-- ABOUT SECTION - CHỨC NĂNG HỆ THỐNG --}}
        <section id="about" class="relative py-24 overflow-hidden bg-gradient-to-b from-gray-50 to-white">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <div class="inline-block mb-4">
                        <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                            Chức năng nổi bật
                        </span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                        Hệ thống Quản lý Hội thảo<br>
                        <span class="bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-500 bg-clip-text text-transparent">
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

        {{-- CONTACT SECTION --}}
        <section id="contact" class="relative py-24 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: linear-gradient(rgba(59, 130, 246, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(59, 130, 246, 0.3) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>

            <!-- Gradient Orbs -->
            <div class="absolute top-20 right-20 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl"></div>

            <div class="container mx-auto px-6 relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-block mb-4">
                        <span class="bg-gradient-to-r from-cyan-400 to-blue-400 text-slate-900 text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                            Liên hệ với chúng tôi
                        </span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-white mb-6">
                        Sẵn sàng hỗ trợ bạn
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-300 text-lg">
                        Đội ngũ của chúng tôi luôn sẵn sàng giải đáp mọi thắc mắc và hỗ trợ bạn
                    </p>
                </div>

                <div class="grid lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <!-- Contact Cards -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Card 1 -->
                        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all group">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-location-dot text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg mb-2 text-white">Địa chỉ</h4>
                                    <p class="text-gray-300 text-sm leading-relaxed">
                                        Khoa Công nghệ Thông tin<br>
                                        Đại học Công Thương TP.HCM<br>
                                        140 Lê Trọng Tấn, Tân Phú, TP.HCM
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all group">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-phone text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg mb-2 text-white">Điện thoại</h4>
                                    <p class="text-gray-300 text-sm">
                                        +84 (28) 3816 5673<br>
                                        +84 (28) 3816 5674
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all group">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-envelope text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg mb-2 text-white">Email</h4>
                                    <p class="text-gray-300 text-sm">
                                        cntt@hutech.edu.vn<br>
                                        hoithao.cntt@hutech.edu.vn
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
                            <h4 class="font-bold text-lg mb-4 text-white">Theo dõi chúng tôi</h4>
                            <div class="flex space-x-3">
                                <a href="#" class="w-11 h-11 bg-white/10 hover:bg-blue-500 rounded-xl flex items-center justify-center transition-all hover:scale-110">
                                    <i class="fab fa-facebook-f text-white"></i>
                                </a>
                                <a href="#" class="w-11 h-11 bg-white/10 hover:bg-red-500 rounded-xl flex items-center justify-center transition-all hover:scale-110">
                                    <i class="fab fa-youtube text-white"></i>
                                </a>
                                <a href="#" class="w-11 h-11 bg-white/10 hover:bg-blue-600 rounded-xl flex items-center justify-center transition-all hover:scale-110">
                                    <i class="fab fa-linkedin-in text-white"></i>
                                </a>
                                <a href="#" class="w-11 h-11 bg-white/10 hover:bg-pink-500 rounded-xl flex items-center justify-center transition-all hover:scale-110">
                                    <i class="fab fa-instagram text-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <form class="bg-white rounded-3xl p-8 md:p-10 shadow-2xl">
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">
                                        Họ và tên <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" placeholder="Nguyễn Văn A"
                                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white transition text-gray-900">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" placeholder="example@email.com"
                                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white transition text-gray-900">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">
                                        Số điện thoại <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" placeholder="0912 345 678"
                                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white transition text-gray-900">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">
                                        Chủ đề
                                    </label>
                                    <select class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white transition text-gray-900">
                                        <option>Câu hỏi chung</option>
                                        <option>Đăng ký hội thảo</option>
                                        <option>Hỗ trợ kỹ thuật</option>
                                        <option>Hợp tác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    Nội dung <span class="text-red-500">*</span>
                                </label>
                                <textarea rows="5" placeholder="Nhập nội dung bạn muốn gửi..."
                                    class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white transition resize-none text-gray-900"></textarea>
                            </div>

                            <div class="flex items-start mb-6">
                                <input type="checkbox" id="agree" class="mt-1 mr-3 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                <label for="agree" class="text-sm text-gray-600">
                                    Tôi đồng ý với <a href="#" class="text-blue-600 font-semibold hover:underline">điều khoản dịch vụ</a> và <a href="#" class="text-blue-600 font-semibold hover:underline">chính sách bảo mật</a>
                                </label>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl hover:scale-105 transition-all flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Gửi tin nhắn</span>
                            </button>

                            <p class="text-center text-sm text-gray-500 mt-4">
                                Chúng tôi sẽ phản hồi trong vòng 24 giờ
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-primary text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-accent rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Hội thảo CNTT</h3>
                            <p class="text-xs text-gray-400">ĐH Công Thương</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Nền tảng hỗ trợ tổ chức và quản lý hội thảo học thuật chuyên nghiệp
                    </p>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Liên kết nhanh</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-accent transition">Trang chủ</a></li>
                        <li><a href="#" class="hover:text-accent transition">Hội thảo</a></li>
                        <li><a href="#" class="hover:text-accent transition">Tin tức</a></li>
                        <li><a href="#" class="hover:text-accent transition">Liên hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Hỗ trợ</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-accent transition">Hướng dẫn sử dụng</a></li>
                        <li><a href="#" class="hover:text-accent transition">Câu hỏi thường gặp</a></li>
                        <li><a href="#" class="hover:text-accent transition">Chính sách</a></li>
                        <li><a href="#" class="hover:text-accent transition">Điều khoản</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Kết nối với chúng tôi</h4>
                    <div class="flex space-x-3 mb-4">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                    </div>
                    <p class="text-sm text-gray-400">
                        <i class="fa-solid fa-envelope mr-2"></i>
                        cntt@hutech.edu.vn
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-8 text-center">
                <p class="text-sm text-gray-400">
                    © {{ date('Y') }} <span class="font-semibold text-white">Khoa Công nghệ Thông tin</span> - Đại học Công Thương TP.HCM
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Hệ thống Quản lý Hội thảo Học thuật | Phát triển bởi Sinh viên CNTT
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>