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

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background: #3b82f6;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
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