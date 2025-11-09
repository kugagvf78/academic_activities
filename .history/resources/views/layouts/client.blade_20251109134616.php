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
        @yield('content')
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