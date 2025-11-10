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
            display: inline-block;
            padding-bottom: 6px;
            font-size: 18px;
            /* tạo khoảng cách giữa chữ và gạch */
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            /* đường kẻ nằm thấp hơn chữ */
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
            /* tăng độ dài gạch dưới */
        }
        [x-cloak] { display: none !important; }
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

    <header class="bg-white backdrop-blur-md shadow sticky top-0 z-50 border-b border-gray-100">
        <div x-data="{ openMenu: false }" class="container mx-auto flex justify-between items-center py-4 px-6">
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

            {{-- 🔘 Nút hamburger (mobile) --}}
            <button @click.stop="openMenu = true"
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
                <a href="#"
                    class="nav-link hover:text-blue-600 {{ request()->is('news*') ? 'active text-blue-600 font-semibold' : '' }}">
                    Tin tức
                </a>
                <a href="{{ route('client.home') }}#contact" class="nav-link hover:text-blue-600">Liên hệ</a>
            </nav>

            {{-- 👤 User desktop --}}
            <div class="hidden lg:flex items-center space-x-4">
                @auth
                {{-- Tách phạm vi riêng, không liên quan đến openMenu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2">
                        <div
                            class="w-9 h-9 bg-gradient-to-tr from-blue-600 to-cyan-500 text-white rounded-full flex items-center justify-center font-bold uppercase">
                            {{ strtoupper(Str::substr(Auth::user()->TenDangNhap, 0, 1)) }}
                        </div>
                        <span class="font-semibold text-gray-700">{{ Auth::user()->TenDangNhap }}</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    {{-- dropdown user --}}
                    <div x-show="open" x-cloak
                        x-transition.origin.top.right
                        @click.away="open = false"
                        class="absolute right-0 mt-3 w-52 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-lg">
                        <a href="{{ route('client.profile') }}"
                            class="flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fa-solid fa-user text-sm"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 font-semibold transition">
                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                                <span>Đăng xuất</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}"
                    class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                </a>
                @endauth
            </div>
        </div>

        {{-- 📱 OFFCANVAS MENU (mobile) --}}
        <div x-data="{ openMenu: false }" x-show="openMenu"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed inset-0 z-[999] flex justify-end lg:hidden"
            @click.self="openMenu = false">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="openMenu = false"></div>

            {{-- Panel --}}
            <div class="relative bg-white w-4/5 sm:w-2/5 h-full shadow-xl p-6 flex flex-col">
                {{-- Close --}}
                <button @click.stop="openMenu = false" class="absolute top-4 right-4 text-gray-700 hover:text-blue-600">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>

                {{-- Nav links --}}
                <nav class="mt-12 flex flex-col space-y-5 text-lg font-medium text-gray-700">
                    <a href="{{ route('client.home') }}"
                        class="hover:text-blue-600 {{ request()->routeIs('client.home') ? 'font-bold text-blue-600' : '' }}">
                        Trang chủ
                    </a>
                    <a href="{{ route('client.events.index') }}"
                        class="hover:text-blue-600 {{ request()->routeIs('client.events.index') ? 'font-bold text-blue-600' : '' }}">
                        Cuộc thi
                    </a>
                    <a href="{{ route('client.results.index') }}"
                        class="hover:text-blue-600 {{ request()->is('results*') ? 'font-bold text-blue-600' : '' }}">
                        Kết quả
                    </a>
                    <a href="{{ route('client.news.index') }}"
                        class="hover:text-blue-600 {{ request()->is('news*') ? 'font-bold text-blue-600' : '' }}">
                        Tin tức
                    </a>
                    <a href="#contact" class="hover:text-blue-600">Liên hệ</a>
                </nav>

                {{-- User mobile --}}
                <div class="mt-auto pt-6 border-t border-gray-200">
                    @auth
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-cyan-500 text-white rounded-full flex items-center justify-center font-bold uppercase">
                            {{ strtoupper(Str::substr(Auth::user()->TenDangNhap, 0, 1)) }}
                        </div>
                        <span class="font-semibold text-gray-700">{{ Auth::user()->TenDangNhap }}</span>
                    </div>
                    <a href="{{ route('client.profile') }}" class="block mb-3 text-gray-700 hover:text-blue-600">Hồ sơ cá nhân</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-left text-red-600 font-semibold hover:underline">Đăng xuất</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                        class="block text-blue-600 font-semibold hover:underline mt-2">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>


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