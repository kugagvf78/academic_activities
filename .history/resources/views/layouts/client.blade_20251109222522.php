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
                        oswald: ['Oswald', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

<body class="font-oswald bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- HEADER / NAVBAR --}}
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
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
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
                    <a href="{{ route('home') }}" class="relative group flex items-center gap-2 hover:text-blue-600 transition">
                        <span>Trang chủ</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-[2px] bg-gradient-to-r from-blue-500 to-cyan-400 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="{{ route('events') }}" class="relative group flex items-center gap-2 hover:text-blue-600 transition">
                        <span>Hội thảo</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-[2px] bg-gradient-to-r from-blue-500 to-cyan-400 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="#" class="relative group flex items-center gap-2 hover:text-blue-600 transition">
                        <span>Tin tức</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-[2px] bg-gradient-to-r from-blue-500 to-cyan-400 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="#contact" class="relative group flex items-center gap-2 hover:text-blue-600 transition">
                        <span>Liên hệ</span>
                        <span class="absolute -bottom-1 left-0 w-0 h-[2px] bg-gradient-to-r from-blue-500 to-cyan-400 group-hover:w-full transition-all"></span>
                    </a>
                </nav>

                {{-- AUTH / LOGIN --}}
                <div class="hidden lg:flex items-center space-x-3">
                    @auth
                    <div class="flex items-center bg-gray-50 rounded-full px-4 py-2 border border-gray-200 shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center mr-2">
                            <i class="fa-solid fa-user text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">{{ Str::limit(Auth::user()->name, 12) }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button
                            class="ml-2 px-5 py-2 rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white font-semibold text-sm hover:shadow-md hover:scale-105 transition">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i> Đăng xuất
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                        class="px-6 py-2.5 rounded-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-sm shadow-md hover:shadow-lg hover:scale-105 transition">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>Đăng nhập
                    </a>
                    @endauth
                </div>

                {{-- MOBILE MENU ICON --}}
                <button id="menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fa-solid fa-bars text-xl text-gray-700"></i>
                </button>
            </div>

            {{-- MOBILE MENU --}}
            <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 shadow-xl">
                <div class="px-6 py-4 space-y-3">
                    <a href="{{ route('home') }}" class="block py-2 text-gray-700 hover:text-blue-600">Trang chủ</a>
                    <a href="{{ route('events') }}" class="block py-2 text-gray-700 hover:text-blue-600">Hội thảo</a>
                    <a href="#" class="block py-2 text-gray-700 hover:text-blue-600">Tin tức</a>
                    <a href="#contact" class="block py-2 text-gray-700 hover:text-blue-600">Liên hệ</a>

                    <div class="pt-3 border-t border-gray-200">
                        @auth
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white text-xs">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="font-semibold text-gray-800">{{ Str::limit(Auth::user()->name, 16) }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="w-full text-left text-red-500 font-semibold hover:text-red-600 transition">Đăng xuất</button>
                        </form>
                        @else
                        <a href="{{ route('login') }}"
                            class="block w-full text-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-2.5 rounded-full font-semibold shadow hover:shadow-md transition">
                            Đăng nhập
                        </a>
                        @endauth
                    </div>
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