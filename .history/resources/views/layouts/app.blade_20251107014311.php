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

<body class="bg-white text-gray-900 font-[Inter] min-h-screen">

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>
@if (session('success'))
<script>
    showToast("success", "{{ session('success') }}");
</script>
@endif

@if ($errors->any())
<script>
    showToast("error", "{{ $errors->first() }}");
</script>
@endif
</body>

</html>