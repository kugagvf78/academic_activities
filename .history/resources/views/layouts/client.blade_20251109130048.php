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
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
    <header class="bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto flex justify-between items-center py-5 px-6">
            <a href="{{ route('client.home') }}" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Hội thảo CNTT</h1>
                    <p class="text-xs text-gray-500">ĐH Công Thương TP.HCM</p>
                </div>
            </a>

            <nav class="hidden md:flex space-x-10 font-medium">
                <a href="{{ route('client.home') }}" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-home mr-2"></i>Trang chủ
                </a>
                <a href="{{ route('client.events') }}" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-calendar-days mr-2"></i>Hội thảo
                </a>
                <a href="#" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-newspaper mr-2"></i>Tin tức
                </a>
                <a href="#contact" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-envelope mr-2"></i>Liên hệ
                </a>
            </nav>

            <div class="flex items-center space-x-3">
                @auth
                    <div class="flex items-center space-x-3">
                        <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-lg">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary to-accent rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-white text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">User Name</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white hover:shadow-lg hover:scale-105 transition text-sm font-semibold">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-primary to-secondary text-white hover:shadow-xl hover:scale-105 transition text-sm font-semibold">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- MAIN --}}
    <main class="flex-1">
        {{-- HERO SECTION --}}
        <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white overflow-hidden">
            <!-- Animated Grid Background -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute inset-0" style="background-image: linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>

            <!-- Gradient Orbs -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-float"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
            </div>

            <div class="container mx-auto px-6 py-20 md:py-28 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="space-y-8">
                        <!-- Badge -->
                        <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span class="text-sm font-medium">Đang hoạt động - Năm học 2024-2025</span>
                        </div>

                        <!-- Main Heading -->
                        <div>
                            <h1 class="text-5xl md:text-6xl lg:text-7xl font-black leading-tight mb-6">
                                Hệ thống
                                <span class="block bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500 bg-clip-text text-transparent">
                                    Quản lý Hội thảo
                                </span>
                            </h1>
                            <p class="text-xl text-gray-300 leading-relaxed max-w-xl">
                                Nền tảng toàn diện kết nối sinh viên và giảng viên Khoa CNTT, 
                                nơi tri thức được chia sẻ và năng lực được khẳng định
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('client.events') }}" class="group relative inline-flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 px-8 py-4 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-blue-500/50 transition-all hover:scale-105">
                                <span>Khám phá ngay</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#about" class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 px-8 py-4 rounded-2xl font-semibold text-lg transition-all hover:scale-105">
                                <i class="fa-solid fa-play-circle"></i>
                                <span>Xem demo</span>
                            </a>
                        </div>

                        <!-- Stats Mini -->
                        <div class="flex items-center space-x-8 pt-4">
                            <div>
                                <div class="text-3xl font-bold text-blue-400">150+</div>
                                <div class="text-sm text-gray-400">Hội thảo</div>
                            </div>
                            <div class="w-px h-12 bg-white/10"></div>
                            <div>
                                <div class="text-3xl font-bold text-cyan-400">2.5K+</div>
                                <div class="text-sm text-gray-400">Sinh viên</div>
                            </div>
                            <div class="w-px h-12 bg-white/10"></div>
                            <div>
                                <div class="text-3xl font-bold text-indigo-400">80+</div>
                                <div class="text-sm text-gray-400">Giảng viên</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Visual -->
                    <div class="relative hidden lg:block">
                        <div class="relative">
                            <!-- Main Card -->
                            <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
                                <div class="space-y-6">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                                <i class="fa-solid fa-calendar-days text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-400">Hội thảo sắp tới</div>
                                                <div class="font-bold text-lg">AI & Machine Learning</div>
                                            </div>
                                        </div>
                                        <span class="bg-green-500/20 text-green-400 text-xs font-semibold px-3 py-1.5 rounded-full border border-green-500/30">
                                            Đang mở
                                        </span>
                                    </div>

                                    <!-- Info Grid -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                            <div class="text-2xl font-bold text-blue-400">15/11</div>
                                            <div class="text-sm text-gray-400">Ngày diễn ra</div>
                                        </div>
                                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                            <div class="text-2xl font-bold text-cyan-400">120</div>
                                            <div class="text-sm text-gray-400">Đã đăng ký</div>
                                        </div>
                                    </div>

                                    <!-- Participants -->
                                    <div>
                                        <div class="text-sm text-gray-400 mb-3">Diễn giả chính</div>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex -space-x-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 border-2 border-slate-800 flex items-center justify-center text-white font-semibold text-sm">A</div>
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 border-2 border-slate-800 flex items-center justify-center text-white font-semibold text-sm">B</div>
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 to-rose-500 border-2 border-slate-800 flex items-center justify-center text-white font-semibold text-sm">C</div>
                                            </div>
                                            <span class="text-sm text-gray-400">+3 Giảng viên</span>
                                        </div>
                                    </div>

                                    <!-- Button -->
                                    <button class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 py-3 rounded-xl font-semibold transition-all hover:scale-105">
                                        Đăng ký ngay
                                    </button>
                                </div>
                            </div>

                            <!-- Floating Elements -->
                            <div class="absolute -top-6 -right-6 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-xl animate-float">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-users text-cyan-400 text-xl"></i>
                                    <div>
                                        <div class="text-xs text-gray-400">Tham gia</div>
                                        <div class="font-bold">+45 hôm nay</div>
                                    </div>
                                </div>
                            </div>

                            <div class="absolute -bottom-6 -left-6 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-xl animate-float" style="animation-delay: 2s;">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-award text-yellow-400 text-xl"></i>
                                    <div>
                                        <div class="text-xs text-gray-400">Giải thưởng</div>
                                        <div class="font-bold">25+ prizes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Wave -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                    <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 80C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="rgb(249, 250, 251)"/>
                </svg>
            </div>
        </section>

        {{-- ABOUT SECTION --}}
        <section id="about" class="relative py-24 overflow-hidden bg-gradient-to-b from-gray-50 to-white">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <div class="inline-block mb-4">
                        <span class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">
                            Về chúng tôi
                        </span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                        Tại sao chọn <br>
                        <span class="bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-500 bg-clip-text text-transparent">
                            Nền tảng của chúng tôi?
                        </span>
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-600 text-lg">
                        Giải pháp toàn diện giúp tổ chức và quản lý hội thảo học thuật một cách chuyên nghiệp
                    </p>
                </div>

                <!-- Main Features - Large Cards -->
                <div class="grid md:grid-cols-3 gap-6 mb-20">
                    <!-- Card 1 -->
                    <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                        <div class="relative">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                                <i class="fa-solid fa-rocket text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Phát triển Tri thức</h3>
                            <p class="text-blue-50 leading-relaxed">
                                Tiếp cận kiến thức từ các chuyên gia, nâng cao kỹ năng và mở rộng tầm nhìn trong lĩnh vực công nghệ
                            </p>
                            <div class="mt-6 flex items-center text-white/80 text-sm">
                                <span class="font-semibold">Tìm hiểu thêm</span>
                                <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                            </div>
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
                            <h3 class="text-2xl font-bold text-white mb-4">Kết nối Cộng đồng</h3>
                            <p class="text-cyan-50 leading-relaxed">
                                Xây dựng mạng lưới quan hệ với sinh viên, giảng viên và các chuyên gia trong ngành
                            </p>
                            <div class="mt-6 flex items-center text-white/80 text-sm">
                                <span class="font-semibold">Tìm hiểu thêm</span>
                                <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="group relative bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-3xl p-8 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                        <div class="relative">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                                <i class="fa-solid fa-trophy text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Khẳng định Bản thân</h3>
                            <p class="text-indigo-50 leading-relaxed">
                                Nhận chứng chỉ, giải thưởng và xây dựng hồ sơ năng lực ấn tượng cho tương lai
                            </p>
                            <div class="mt-6 flex items-center text-white/80 text-sm">
                                <span class="font-semibold">Tìm hiểu thêm</span>
                                <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Features - Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-calendar-check text-white text-lg"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2 text-gray-900">Quản lý Thông minh</h4>
                        <p class="text-gray-600 text-sm">Đăng ký và theo dõi lịch trình dễ dàng</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-bell text-white text-lg"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2 text-gray-900">Thông báo Realtime</h4>
                        <p class="text-gray-600 text-sm">Cập nhật tức thì mọi thông tin</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-chart-line text-white text-lg"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2 text-gray-900">Thống kê Chi tiết</h4>
                        <p class="text-gray-600 text-sm">Phân tích hoạt động tham gia</p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-certificate text-white text-lg"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2 text-gray-900">Chứng chỉ Điện tử</h4>
                        <p class="text-gray-600 text-sm">Xác thực và tải xuống dễ dàng</p>
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