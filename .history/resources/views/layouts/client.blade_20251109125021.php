<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống Quản lý Hội thảo - Khoa CNTT')</title>

    {{-- TailwindCSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', // xanh dương chính
                        secondary: '#1e40af', // xanh đậm hơn
                        accent: '#f97316', // cam nhấn
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @stack('styles')
</head>

<body class="font-inter bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- HEADER / NAVBAR --}}
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <a href="{{ route('client.home') }}" class="text-2xl font-bold text-primary tracking-wide">
                <i class="fa-solid fa-graduation-cap text-accent mr-2"></i> Hội thảo CNTT
            </a>

            <nav class="hidden md:flex space-x-8 font-medium">
                <a href="{{ route('client.home') }}" class="hover:text-primary transition">Trang chủ</a>
                <a href="{{ route('client.events') }}" class="hover:text-primary transition">Hội thảo</a>
            </nav>

            <div>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-secondary transition text-sm">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-secondary transition text-sm">
                        Đăng nhập
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
    <footer class="bg-primary text-white text-center py-8 mt-12">
        <p class="font-semibold text-lg">© {{ date('Y') }} Khoa Công nghệ Thông tin - Đại học Công Thương TP.HCM</p>
        <p class="text-sm mt-1 text-blue-100">Hệ thống Quản lý Hội thảo Học thuật</p>
    </footer>

    @stack('scripts')
</body>
</html>
