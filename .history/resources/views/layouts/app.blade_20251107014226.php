<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống học thuật')</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Fonts + Icons (optional) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<!-- 🔄 Spinner toàn màn hình -->
<div id="loadingSpinner"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="w-16 h-16 border-4 border-t-transparent border-white rounded-full animate-spin"></div>
</div>


<body class="bg-white text-gray-900 font-[Inter] min-h-screen">

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

</body>

</html>