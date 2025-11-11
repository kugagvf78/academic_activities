<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Hội thảo - Khoa CNTT</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .nav-link {
            position: relative;
            display: inline-block;
            padding-bottom: 6px;
            font-size: 18px;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #0ea5e9);
            border-radius: 2px;
            transform: translateX(-50%);
            transition: all 0.3s ease;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            width: 90%;
        }
        
        [x-cloak] { 
            display: none !important; 
        }

        /* Avatar animation */
        .user-avatar {
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
    </style>

    @stack('styles')
</head>

<body class="font-oswald bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- 🔄 Global Spinner --}}
    <div id="loadingSpinner"
        class="hidden fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm flex-col items-center justify-center">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-blue-700 font-semibold text-sm">Đang xử lý...</p>
    </div>

    {{-- ✅ HEADER CẢI TIẾN --}}
    <header class="bg-white backdrop-blur-md shadow sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            
            {{-- 🧩 Logo & Title --}}
            <a href="{{ route('client.home') }}" class="flex items-center space-x-4 group">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Logo HUIT"
                    class="h-12 md:h-14 object-contain hover:scale-105 transition-transform duration-300">
                <div class="leading-tight hidden sm:block">
                    <h1 class="text-lg md:text-xl font-extrabold text-gray-800 group-hover:text-blue-700 transition">
                        Cuộc thi Học thuật Khoa CNTT
                    </h1>
                </div>
            </a>

            {{-- 📱 Nút hamburger (mobile) --}}
            <button 
                x-data 
                @click="$dispatch('toggle-mobile-menu')"
                class="lg:hidden text-gray-700 hover:text-blue-600 focus:outline-none transition">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>

            {{-- 🌐 Navigation desktop --}}
            <nav class="hidden lg:flex space-x-8 font-medium">
                <a href="{{ route('client.home') }}"
                    class="nav-link hover:text-blue-600 {{ request()->routeIs('client.home') ? 'active text-blue-600 font-semibold' : '' }}">
                    Trang chủ
                </a>
                <a href="{{ route('client.events.index') }}"
                    class="nav-link hover:text-blue-600 {{ request()->routeIs('client.events.index') ? 'active text-blue-600 font-semibold' : '' }}">
                    Cuộc thi
                </a>
                <a href="{{ route('client.results.index') }}"
                    class="nav-link hover:text-blue-600 {{ request()->routeIs('client.results.index') ? 'active text-blue-600 font-semibold' : '' }}">
                    Kết quả
                </a>
                <a href="{{ route('client.news.index') }}"
                    class="nav-link hover:text-blue-600 {{ request()->routeIs('client.news.index') ? 'active text-blue-600 font-semibold' : '' }}">
                    Tin tức
                </a>
                <a href="{{ route('client.home') }}#contact" class="nav-link hover:text-blue-600">Liên hệ</a>
            </nav>

            @php
                // ✅ SỬA: Dùng guard 'api' thay vì guard 'web'
                try {
                    if (request()->cookie('jwt_token')) {
                        Auth::guard('api')->setToken(request()->cookie('jwt_token'));
                        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
                    } else {
                        $user = null;
                    }
                } catch (\Exception $e) {
                    $user = null;
                }
            @endphp

            {{-- Desktop User Section --}}
            <div class="hidden lg:flex items-center space-x-4">
                @if($user)
                    <div x-data="{ userDropdown: false }" class="relative">
                        <button 
                            @click="userDropdown = !userDropdown" 
                            class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
                            
                            {{-- Avatar --}}
                            <div class="user-avatar w-10 h-10 bg-gradient-to-tr from-blue-600 to-cyan-500 text-white rounded-full flex items-center justify-center font-bold uppercase shadow-md">
                                {{ strtoupper(substr($user->ho_ten ?? $user->ten_dang_nhap, 0, 1)) }}
                            </div>
                            
                            {{-- Tên + Icon --}}
                            <div class="flex items-center gap-2">
                                <div class="text-left">
                                    <p class="font-semibold text-gray-800 text-sm leading-tight">
                                        {{ Str::limit($user->ho_ten ?? $user->ten_dang_nhap, 20) }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ '@' . $user->ten_dang_nhap }}</p>
                                </div>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" 
                                :class="userDropdown ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div 
                            x-show="userDropdown" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @click.away="userDropdown = false"
                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xl">
                            
                            {{-- User Info Header --}}
                            <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
                                <p class="font-bold text-gray-800 text-sm">{{ $user->ho_ten ?? $user->ten_dang_nhap }}</p>
                                <p class="text-xs text-gray-600">{{ $user->email ?? 'Chưa có email' }}</p>
                            </div>

                            {{-- Menu Items --}}
                            <div class="py-2">
                                <a href="{{ route('client.profile') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fa-solid fa-user w-4"></i>
                                    <span class="text-sm font-medium">Hồ sơ cá nhân</span>
                                </a>
                                
                                <a href="{{ route('password.change') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fa-solid fa-key w-4"></i>
                                    <span class="text-sm font-medium">Đổi mật khẩu</span>
                                </a>
                            </div>

                            {{-- Logout --}}
                            <div class="border-t border-gray-200">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-3 w-full text-left px-4 py-2.5 text-red-600 hover:bg-red-50 font-semibold transition">
                                        <i class="fa-solid fa-right-from-bracket w-4"></i>
                                        <span class="text-sm">Đăng xuất</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Chưa đăng nhập --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}"
                            class="px-5 py-2.5 rounded-lg border-2 border-blue-600 text-blue-600 font-semibold text-sm hover:bg-blue-50 transition">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition shadow-md hover:shadow-lg">
                            Đăng ký
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </header>

    {{-- 📱 MOBILE MENU --}}
    <div 
        x-data="{ mobileMenuOpen: false }" 
        @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen"
        x-show="mobileMenuOpen"
        x-cloak
        class="fixed inset-0 z-[999] lg:hidden"
        @click.self="mobileMenuOpen = false">

        {{-- Overlay --}}
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm" 
            @click="mobileMenuOpen = false">
        </div>

        {{-- Slide Panel --}}
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 h-full w-4/5 max-w-sm bg-white shadow-2xl flex flex-col">
            
            {{-- Close Button --}}
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-800">Menu</h3>
                <button 
                    @click="mobileMenuOpen = false" 
                    class="w-10 h-10 flex items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- User Info Mobile --}}
            @if($user)
                <div class="p-6 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-cyan-500 text-white rounded-full flex items-center justify-center font-bold uppercase shadow-md">
                            {{ strtoupper(substr($user->ho_ten ?? $user->ten_dang_nhap, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $user->ho_ten ?? $user->ten_dang_nhap }}</p>
                            <p class="text-xs text-gray-600">{{ '@' . $user->ten_dang_nhap }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Navigation Links --}}
            <nav class="flex-1 overflow-y-auto p-6">
                <div class="space-y-2">
                    <a href="{{ route('client.home') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition {{ request()->routeIs('client.home') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700' }}">
                        <i class="fa-solid fa-house w-5"></i>
                        <span>Trang chủ</span>
                    </a>
                    
                    <a href="{{ route('client.events.index') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition {{ request()->routeIs('client.events.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700' }}">
                        <i class="fa-solid fa-calendar-days w-5"></i>
                        <span>Cuộc thi</span>
                    </a>
                    
                    <a href="{{ route('client.results.index') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition {{ request()->routeIs('client.results.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700' }}">
                        <i class="fa-solid fa-trophy w-5"></i>
                        <span>Kết quả</span>
                    </a>
                    
                    <a href="{{ route('client.news.index') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition {{ request()->routeIs('client.news.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700' }}">
                        <i class="fa-solid fa-newspaper w-5"></i>
                        <span>Tin tức</span>
                    </a>
                    
                    <a href="{{ route('client.home') }}#contact"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition text-gray-700">
                        <i class="fa-solid fa-envelope w-5"></i>
                        <span>Liên hệ</span>
                    </a>
                </div>

                @if($user)
                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                        <a href="{{ route('client.profile') }}"
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition text-gray-700">
                            <i class="fa-solid fa-user w-5"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        
                        <a href="{{ route('password.change') }}"
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-50 transition text-gray-700">
                            <i class="fa-solid fa-key w-5"></i>
                            <span>Đổi mật khẩu</span>
                        </a>
                    </div>
                @endif
            </nav>

            {{-- Bottom Action --}}
            <div class="p-6 border-t border-gray-200">
                @if($user)
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                @else
                    <div class="space-y-3">
                        <a href="{{ route('login') }}"
                            @click="mobileMenuOpen = false"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-lg border-2 border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Đăng nhập</span>
                        </a>
                        
                        <a href="{{ route('register') }}"
                            @click="mobileMenuOpen = false"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-user-plus"></i>
                            <span>Đăng ký</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white text-gray-700 border-t border-blue-100">
        <div class="container mx-auto px-6 pb-10 pt-8">
            <div class="grid md:grid-cols-4 gap-10 mb-10">

                {{-- Cột giới thiệu --}}
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-blue-800">Hội thảo CNTT</h3>
                            <p class="text-sm text-blue-500">ĐH Công Thương TP.HCM</p>
                        </div>
                    </div>
                    <p class="text-base text-gray-600 leading-relaxed border-l-4 border-blue-200 pl-3">
                        Nền tảng hỗ trợ tổ chức và quản lý hội thảo học thuật chuyên nghiệp, dễ dàng và hiệu quả.
                    </p>
                </div>

                {{-- Cột liên kết nhanh --}}
                <div>
                    <h4 class="font-bold mb-4 text-lg text-blue-800 border-b-2 border-blue-200 inline-block pb-1">
                        Liên kết nhanh
                    </h4>
                    <ul class="space-y-2 text-base">
                        <li><a href="{{ route('client.home') }}" class="hover:text-blue-600 transition">Trang chủ</a></li>
                        <li><a href="{{ route('client.events.index') }}" class="hover:text-blue-600 transition">Hội thảo</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Tin tức</a></li>
                        <li><a href="#contact" class="hover:text-blue-600 transition">Liên hệ</a></li>
                    </ul>
                </div>

                {{-- Cột hỗ trợ --}}
                <div>
                    <h4 class="font-bold mb-4 text-lg text-blue-800 border-b-2 border-blue-200 inline-block pb-1">
                        Hỗ trợ
                    </h4>
                    <ul class="space-y-2  text-base">
                        <li><a href="#" class="hover:text-blue-600 transition">Hướng dẫn sử dụng</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Câu hỏi thường gặp</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Chính sách</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Điều khoản</a></li>
                    </ul>
                </div>

                {{-- Cột liên hệ --}}
                <div>
                    <h4 class="font-bold mb-4 text-lg text-blue-800 border-b-2 border-blue-200 inline-block pb-1">
                        Kết nối với chúng tôi
                    </h4>
                    <div class="flex space-x-3 mb-4">
                        <a href="#" class="w-10 h-10 border border-blue-200 hover:border-blue-400 text-blue-600 hover:text-white hover:bg-blue-500 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 border border-blue-200 hover:border-blue-400 text-blue-600 hover:text-white hover:bg-blue-500 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="w-10 h-10 border border-blue-200 hover:border-blue-400 text-blue-600 hover:text-white hover:bg-blue-500 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 border border-blue-200 hover:border-blue-400 text-blue-600 hover:text-white hover:bg-blue-500 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                    <p class="text-base text-gray-600 flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-blue-500"></i>
                        cntt@hutech.edu.vn
                    </p>
                </div>
            </div>

            {{-- Footer bottom --}}
            <div class="border-t border-blue-100 pt-6 text-center">
                <p class="text-sm text-gray-600">
                    © {{ date('Y') }}
                    <span class="font-semibold text-blue-700">Khoa Công nghệ Thông tin</span> - Đại học Công Thương TP.HCM
                </p>
                <p class="text-xs mt-1 text-gray-500">
                    Hệ thống Quản lý Hội thảo Học thuật | Phát triển bởi Sinh viên CNTT
                </p>
            </div>
        </div>
    </footer>

    {{-- Truyền session toast cho JS --}}
    @if(session('toast'))
    <script>
        window.LaravelToast = @json(session('toast'));
    </script>
    @endif

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>