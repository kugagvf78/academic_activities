<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống học thuật - Khoa CNTT')</title>

    {{-- TailwindCSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
        <div class="container mx-auto text-center">
            <h1 class="text-2xl font-bold uppercase">Hệ thống Quản lý Cuộc thi Học thuật</h1>
            <p class="text-sm">Khoa Công nghệ Thông tin - Đại học Công Thương TP.HCM</p>
        </div>
    </header>

    {{-- NAVBAR --}}
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-lg font-semibold text-blue-700 hover:text-blue-900">
                Trang chủ
            </a>

            <ul class="hidden md:flex space-x-6">
                <li><a href="{{ route('client.home') }}" class="hover:text-blue-700">Trang chủ</a></li>
                <li><a href="{{ route('client.about') }}" class="hover:text-blue-700">Giới thiệu</a></li>
                <li><a href="{{ route('client.contact') }}" class="hover:text-blue-700">Liên hệ</a></li>
            </ul>

            <div class="flex items-center space-x-3">
                @auth
                <span class="text-gray-700">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 text-sm">
                        Đăng xuất
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 text-sm">
                    Đăng nhập
                </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- CONTENT --}}
    <main class="flex-1 container mx-auto px-4 py-8">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-white text-center py-4 mt-8">
        <p>&copy; {{ date('Y') }} Khoa Công nghệ Thông tin - Đại học Công Thương TP.HCM</p>
        <p class="text-sm mt-1">Phát triển bởi nhóm sinh viên CNTT</p>
    </footer>

    {{-- SCRIPTS --}}
    <script>
        // Toggle menu cho mobile
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.querySelector('#menu-btn');
            const nav = document.querySelector('#menu');
            if (btn && nav) {
                btn.addEventListener('click', () => nav.classList.toggle('hidden'));
            }
        });
    </script>
    @stack('scripts')

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