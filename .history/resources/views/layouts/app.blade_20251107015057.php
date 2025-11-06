<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống học thuật')</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    {{-- Gọi file JS Laravel --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 font-[Inter] min-h-screen">

    <!-- 🔄 Spinner toàn màn hình -->
    <div id="loadingSpinner"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="w-16 h-16 border-4 border-t-transparent border-white rounded-full animate-spin"></div>
    </div>

    <main>
        @yield('content')
    </main>

    <script>
        window.addEventListener("load", () => toggleLoadingSpinner(false));
    </script>

    @if (session('toast'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toast = @json(session('toast'));
            showToast(toast.type, toast.message);
        });
    </script>
    @endif

</body>

</html>