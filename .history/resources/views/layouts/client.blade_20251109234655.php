<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Hội thảo - Khoa CNTT</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Base Animations --}}
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
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

<body class="font-oswald bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- 🔄 Global Spinner --}}
    <div id="loadingSpinner"
        class="hidden fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm flex-col items-center justify-center">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-blue-700 font-semibold text-sm">Đang xử lý...</p>
    </div>

    {{-- HEADER --}}
    <header class="w-full z-50">
        {{-- TOP BAR --}}
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 text-white text-sm">
            <div class="container mx-auto flex flex-col md:flex-row items-center justify-between py-2 px-6 space-y-2 md:space-y-0">
                <div class="flex items-center space-x-6">
                    <span><i class="fa-regular fa-clock mr-2 text-cyan-200"></i>Thứ Hai - Thứ Sáu: 08:00 - 17:00</span>
                    <span><i class="fa-regular fa-envelope mr-2 text-cyan-200"></i>cntt@hutech.edu.vn</span>
                    <span><i class="fa-solid fa-phone mr-2 text-cyan-200"></i>(028) 3816 5673</span>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-cyan-200 transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="hover:text-cyan-200 transition"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="hover:text-cyan-200 transition"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="hover:text-cyan-200 transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        {{-- MAIN NAVBAR --}}
        <div class="bg-white shadow-sm border-b border-gray-100">
            <div class="container mx-auto flex items-center justify-between py-4 px-6">

                {{-- LOGO --}}
                <a href="{{ route('client.home') }}" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div class="leading-tight">
                        <h1 class="text-xl font-extrabold text-gray-800 group-hover:text-blue-700 transition">
                            Hội thảo CNTT
                        </h1>
                        <p class="text-xs text-gray-500 font-medium">Khoa Công nghệ Thông tin</p>
                    </div>
                </a>

                {{-- NAVIGATION --}}
                <nav class="hidden lg:flex items-center space-x-8 font-medium text-gray-700">
                    <a href="{{ route('client.home') }}" class="nav-link hover:text-blue-600">Trang chủ</a>
                    <a href="{{ route('client.events') }}" class="nav-link hover:text-blue-600">Hội thảo</a>
                    <a href="#" class="nav-link hover:text-blue-600">Tin tức</a>
                    <a href="#contact" class="nav-link hover:text-blue-600">Liên hệ</a>
                </nav>

                {{-- USER MENU --}}
                <div class="hidden lg:flex items-center space-x-4 relative">
                    @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 group focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-white font-bold uppercase">
                                {{ strtoupper(Str::substr(Auth::user()->TenDangNhap, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-gray-800">{{ Str::limit(Auth::user()->TenDangNhap, 12) }}</span>
                            <i class="fa-solid fa-chevron-down text-gray-500 text-sm group-hover:text-blue-600 transition"></i>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-3 w-60 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="p-4 border-b border-gray-100 flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-white font-bold uppercase">
                                    {{ strtoupper(Str::substr(Auth::user()->TenDangNhap, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">{{ Auth::user()->TenDangNhap }}</h4>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->Email }}</p>
                                </div>
                            </div>

                            <div class="p-2">
                                <a href="{{ route('password.change') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fa-solid fa-user text-sm"></i>
                                    <span>Hồ sơ cá nhân</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-2 w-full text-left px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 font-semibold transition">
                                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}"
                        class="px-6 py-2.5 rounded-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-sm shadow-md hover:shadow-lg hover:scale-105 transition">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>Đăng nhập
                    </a>
                    @endauth
                </div>

                {{-- MOBILE ICON --}}
                <button id="menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fa-solid fa-bars text-xl text-gray-700"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="grid md:grid-cols-4 gap-10 mb-10">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Hội thảo CNTT</h3>
                            <p class="text-xs text-cyan-100">ĐH Công Thương TP.HCM</p>
                        </div>
                    </div>
                    <p class="text-sm text-cyan-100 leading-relaxed">
                        Nền tảng hỗ trợ tổ chức và quản lý hội thảo học thuật chuyên nghiệp, dễ dàng và hiệu quả.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-lg">Liên kết nhanh</h4>
                    <ul class="space-y-2 text-sm text-cyan-100">
                        <li><a href="{{ route('client.home') }}" class="hover:text-white transition">Trang chủ</a></li>
                        <li><a href="{{ route('client.events') }}" class="hover:text-white transition">Hội thảo</a></li>
                        <li><a href="#" class="hover:text-white transition">Tin tức</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Liên hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-lg">Hỗ trợ</h4>
                    <ul class="space-y-2 text-sm text-cyan-100">
                        <li><a href="#" class="hover:text-white transition">Hướng dẫn sử dụng</a></li>
                        <li><a href="#" class="hover:text-white transition">Câu hỏi thường gặp</a></li>
                        <li><a href="#" class="hover:text-white transition">Chính sách</a></li>
                        <li><a href="#" class="hover:text-white transition">Điều khoản</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-lg">Kết nối với chúng tôi</h4>
                    <div class="flex space-x-3 mb-4">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition"><i class="fab fa-instagram"></i></a>
                    </div>
                    <p class="text-sm text-cyan-100"><i class="fa-solid fa-envelope mr-2"></i>cntt@hutech.edu.vn</p>
                </div>
            </div>
            <div class="border-t border-white/20 pt-6 text-center text-cyan-100">
                <p class="text-sm">© {{ date('Y') }} <span class="font-semibold text-white">Khoa Công nghệ Thông tin</span> - Đại học Công Thương TP.HCM</p>
                <p class="text-xs mt-1 text-cyan-200">Hệ thống Quản lý Hội thảo Học thuật | Phát triển bởi Sinh viên CNTT</p>
            </div>
        </div>
    </footer>

    {{-- Truyền session toast cho JS --}}
    @if(session('toast'))
    <script>
        window.LaravelToast = @json(session('toast'));
    </script>
    @endif

    {{-- Gọi script qua Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>

</html>