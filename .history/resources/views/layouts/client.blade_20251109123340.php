<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống Quản lý Hội thảo - Khoa CNTT')</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .btn-primary { @apply bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition; }
        .btn-outline { @apply border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-5 py-2 rounded-lg transition; }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    
    <!-- 🔄 Spinner toàn màn hình -->
    <div id="loadingSpinner"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="w-16 h-16 border-4 border-t-transparent border-white rounded-full animate-spin"></div>
    </div>
    {{-- HEADER --}}
    <header class="bg-blue-700 text-white py-4 shadow-md">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="{{ route('client.home') }}" class="text-2xl font-bold tracking-wide">
                Hội thảo CNTT
            </a>
            <nav class="hidden md:flex space-x-8 text-white font-medium">
                <a href="{{ route('client.home') }}" class="hover:text-blue-200">Trang chủ</a>
                <a href="{{ route('client.about') }}" class="hover:text-blue-200">Giới thiệu</a>
                <a href="{{ route('client.events') }}" class="hover:text-blue-200">Hội thảo</a>
                <a href="{{ route('client.contact') }}" class="hover:text-blue-200">Liên hệ</a>
            </nav>
            <div>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button class="btn-outline text-sm">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-primary text-sm">Đăng nhập</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-blue-800 text-white text-center py-6 mt-12">
        <p class="font-semibold">© {{ date('Y') }} Khoa Công nghệ Thông tin - Đại học Công Thương TP.HCM</p>
        <p class="text-sm mt-1">Hệ thống Quản lý Hội thảo Học thuật</p>
    </footer>

    <script>
        window.addEventListener("load", () => toggleLoadingSpinner(false));
    </script>

    @if (session('toast'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toast = JSON.parse(`{!! json_encode(session('toast')) !!}`);
            showToast(toast.type, toast.message);
        });
    </script>
    @endif
</body>
</html>
