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
            display: inline-block;
            padding-bottom: 6px;
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
    <header class="bg-white/90 backdrop-blur-md shadow sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            {{-- Logo & Title --}}
            <a href="{{ route('client.home') }}" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-cyan-500 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-gray-800">Cuộc thi Học thuật Khoa CNTT</h1>
                    <p class="text-xs text-gray-500">Đại học Công Thương TP.HCM</p>
                </div>
            </a>

            {{-- Navigation --}}
            <nav class="hidden md:flex space-x-8 font-medium">
                <a href="{{ route('client.home') }}" class="nav-link hover:text-blue-600">Trang chủ</a>
                <a href="{{ route('client.events') }}" class="nav-link hover:text-blue-600">Cuộc thi</a>
                <a href="#" class="nav-link hover:text-blue-600">Kết quả</a>
                <a href="#" class="nav-link hover:text-blue-600">Tin tức</a>
                <a href="{{ route('client.home') }}#contact" class="nav-link hover:text-blue-600">Liên hệ</a>
            </nav>

            {{-- User --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2">
                        <div class="w-9 h-9 bg-gradient-to-tr from-blue-600 to-cyan-500 text-white rounded-full flex items-center justify-center font-bold uppercase">
                            {{ strtoupper(Str::substr(Auth::user()->TenDangNhap, 0, 1)) }}
                        </div>
                        <span class="font-semibold text-gray-700">{{ Auth::user()->TenDangNhap }}</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-3 w-52 bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden">
                        <a href="{{ route('password.change') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fa-solid fa-user text-sm"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 font-semibold transition">
                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                                <span>Đăng xuất</span>
                            </button>
                        </form>
                    </div>

                </div>
                @else
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold">Đăng nhập</a>
                @endauth
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
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>