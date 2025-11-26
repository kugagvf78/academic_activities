<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị Hệ thống - Khoa CNTT</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
    
    <style>
        [x-cloak] { 
            display: none !important; 
        }

        /* Gradient background cho sidebar */
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 50%, #0c4a6e 100%);
        }

        /* Menu item styles */
        .menu-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, #06b6d4, #0891b2);
            border-radius: 0 4px 4px 0;
            transition: height 0.3s ease;
        }

        .menu-item:hover::before,
        .menu-item.active::before {
            height: 70%;
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.15), rgba(8, 145, 178, 0.05));
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Khi sidebar thu nhỏ */
        aside:not(.w-72) .menu-item {
            transform: none;
        }

        aside:not(.w-72) .menu-item:hover {
            transform: none;
        }

        /* Icon container */
        .icon-container {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .menu-item:hover .icon-container,
        .menu-item.active .icon-container {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        /* Stat card animation */
        .stat-card {
            background: linear-gradient(135deg, var(--tw-gradient-stops));
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* User avatar gradient */
        .user-avatar {
            background: linear-gradient(135deg, #06b6d4, #0891b2, #0e7490);
        }

        /* Notification badge pulse */
        @keyframes pulse-scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .notification-badge {
            animation: pulse-scale 2s infinite;
        }

        /* Loading spinner */
        #loadingSpinner {
            backdrop-filter: blur(8px);
        }
    </style>

    @stack('styles')
</head>

<body class="font-oswald bg-gray-50 text-gray-800" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">

    {{-- Loading Spinner --}}
    <div id="loadingSpinner"
        class="hidden fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm flex-col items-center justify-center">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-blue-700 font-semibold text-sm">Đang xử lý...</p>
    </div>

    <div class="flex h-screen overflow-hidden">
        
        {{-- SIDEBAR --}}
        <aside 
            :class="sidebarOpen ? 'w-72' : 'w-20'"
            class="hidden lg:flex flex-col sidebar-gradient text-white transition-all duration-300 shadow-2xl">
            
            {{-- Logo & Brand --}}
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <a href="#" class="flex items-center gap-3 overflow-hidden min-w-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fa-solid fa-user-shield text-white text-xl"></i>
                    </div>
                    <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        <h1 class="font-bold text-xl leading-tight">Quản trị viên</h1>
                        <p class="text-xs text-cyan-200">Khoa CNTT</p>
                    </div>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" 
                    x-show="sidebarOpen"
                    class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all hover:scale-110 flex-shrink-0">
                    <i class="fa-solid fa-angles-left text-sm transition-transform duration-300"></i>
                </button>
                <button @click="sidebarOpen = !sidebarOpen" 
                    x-show="!sidebarOpen"
                    class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all hover:scale-110 flex-shrink-0 mx-auto">
                    <i class="fa-solid fa-angles-right text-sm"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-2">
                
                {{-- Dashboard --}}
                <a href="#" 
                    class="menu-item active flex items-center gap-4 px-4 py-3.5 rounded-xl group"
                    :class="!sidebarOpen && 'justify-center px-0'">
                    <div class="icon-container flex-shrink-0">
                        <i class="fa-solid fa-chart-line text-lg text-cyan-300"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-semibold text-base">Dashboard</span>
                </a>

                {{-- Người dùng --}}
                <a href="#" 
                    class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl group"
                    :class="!sidebarOpen && 'justify-center px-0'">
                    <div class="icon-container flex-shrink-0">
                        <i class="fa-solid fa-users text-lg text-cyan-300"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-semibold text-base">Người dùng</span>
                </a>

                {{-- Cuộc thi --}}
                <a href="#" 
                    class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl group"
                    :class="!sidebarOpen && 'justify-center px-0'">
                    <div class="icon-container flex-shrink-0">
                        <i class="fa-solid fa-trophy text-lg text-cyan-300"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-semibold text-base">Cuộc thi</span>
                </a>

                {{-- Tin tức --}}
                <a href="#" 
                    class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl group"
                    :class="!sidebarOpen && 'justify-center px-0'">
                    <div class="icon-container flex-shrink-0">
                        <i class="fa-solid fa-newspaper text-lg text-cyan-300"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-semibold text-base">Tin tức</span>
                </a>

                {{-- Divider --}}
                <div class="my-6 border-t border-white/10"></div>

                {{-- Cài đặt --}}
                <a href="#" 
                    class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl group"
                    :class="!sidebarOpen && 'justify-center px-0'">
                    <div class="icon-container flex-shrink-0">
                        <i class="fa-solid fa-gear text-lg text-cyan-300"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-semibold text-base">Cài đặt</span>
                </a>

            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-white/10">
                <div x-data="{ userMenu: false }" class="relative">
                    <button @click="userMenu = !userMenu"
                        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/10 transition-all group"
                        :class="!sidebarOpen && 'justify-center px-0'">
                        <div class="user-avatar w-11 h-11 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                            {{ strtoupper(substr(Auth::user()->hoten ?? 'A', 0, 1)) }}
                        </div>
                        <div x-show="sidebarOpen" x-transition class="flex-1 text-left overflow-hidden">
                            <p class="font-semibold text-sm truncate">{{ Auth::user()->hoten ?? 'Admin' }}</p>
                            <p class="text-xs text-cyan-200 truncate">{{ Auth::user()->email ?? 'admin@huit.edu.vn' }}</p>
                        </div>
                        <i x-show="sidebarOpen" class="fa-solid fa-ellipsis-vertical text-cyan-200 group-hover:text-white transition flex-shrink-0"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="userMenu" 
                        x-cloak
                        @click.away="userMenu = false"
                        x-transition
                        class="absolute bottom-full mb-2 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100"
                        :class="sidebarOpen ? 'left-0 right-0' : 'left-1/2 -translate-x-1/2 w-64'">
                        
                        <div class="border-t border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition text-sm font-medium">
                                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MOBILE SIDEBAR --}}
        <div x-show="mobileSidebarOpen" 
            x-cloak
            @click="mobileSidebarOpen = false"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden">
            <aside @click.stop 
                x-show="mobileSidebarOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="w-72 h-full sidebar-gradient text-white overflow-y-auto shadow-2xl">
                
                {{-- Header --}}
                <div class="p-5 border-b border-white/10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fa-solid fa-user-shield text-xl"></i>
                            </div>
                            <div>
                                <h1 class="font-bold text-xl">Quản trị viên</h1>
                                <p class="text-xs text-cyan-200">Khoa CNTT</p>
                            </div>
                        </div>
                        <button @click="mobileSidebarOpen = false" 
                            class="w-9 h-9 rounded-lg hover:bg-white/10 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                <nav class="p-4 space-y-2">
                    <a href="#" 
                        @click="mobileSidebarOpen = false"
                        class="menu-item active flex items-center gap-4 px-4 py-3.5 rounded-xl">
                        <div class="icon-container">
                            <i class="fa-solid fa-chart-line text-lg text-cyan-300"></i>
                        </div>
                        <span class="font-semibold">Dashboard</span>
                    </a>

                    <a href="#" 
                        @click="mobileSidebarOpen = false"
                        class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl">
                        <div class="icon-container">
                            <i class="fa-solid fa-users text-lg text-cyan-300"></i>
                        </div>
                        <span class="font-semibold">Người dùng</span>
                    </a>

                    <a href="#" 
                        @click="mobileSidebarOpen = false"
                        class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl">
                        <div class="icon-container">
                            <i class="fa-solid fa-trophy text-lg text-cyan-300"></i>
                        </div>
                        <span class="font-semibold">Cuộc thi</span>
                    </a>

                    <a href="#" 
                        @click="mobileSidebarOpen = false"
                        class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl">
                        <div class="icon-container">
                            <i class="fa-solid fa-newspaper text-lg text-cyan-300"></i>
                        </div>
                        <span class="font-semibold">Tin tức</span>
                    </a>

                    <div class="my-6 border-t border-white/10"></div>

                    <a href="#" 
                        @click="mobileSidebarOpen = false"
                        class="menu-item flex items-center gap-4 px-4 py-3.5 rounded-xl">
                        <div class="icon-container">
                            <i class="fa-solid fa-gear text-lg text-cyan-300"></i>
                        </div>
                        <span class="font-semibold">Cài đặt</span>
                    </a>
                </nav>
            </aside>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            
            {{-- TOP NAVBAR --}}
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm">
                
                {{-- Left: Hamburger + Breadcrumb --}}
                <div class="flex items-center gap-4">
                    <button @click="mobileSidebarOpen = true" 
                        class="lg:hidden w-10 h-10 rounded-xl hover:bg-gradient-to-br hover:from-cyan-50 hover:to-blue-50 flex items-center justify-center transition-all">
                        <i class="fa-solid fa-bars text-xl text-gray-700"></i>
                    </button>
                    
                    <div class="hidden sm:block">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-700 to-cyan-600 bg-clip-text text-transparent">
                            @yield('page-title', 'Dashboard')
                        </h2>
                        <div class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                            <i class="fa-solid fa-house text-xs"></i>
                            <span>@yield('breadcrumb', 'Trang chủ')</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Search + Notifications + User --}}
                <div class="flex items-center gap-3">
                    
                    {{-- Search --}}
                    <div class="hidden md:block relative">
                        <input type="text" 
                            placeholder="Tìm kiếm..." 
                            class="w-64 pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    {{-- Notifications --}}
                    <div x-data="{ notifOpen: false }" class="relative">
                        <button @click="notifOpen = !notifOpen"
                            class="relative w-11 h-11 rounded-xl hover:bg-gradient-to-br hover:from-cyan-50 hover:to-blue-50 flex items-center justify-center transition-all group">
                            <i class="fa-solid fa-bell text-gray-600 text-lg group-hover:text-cyan-600 transition"></i>
                            <span class="notification-badge absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <div x-show="notifOpen" 
                            x-cloak
                            @click.away="notifOpen = false"
                            x-transition
                            class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="p-5 border-b bg-gradient-to-r from-cyan-50 to-blue-50">
                                <h3 class="font-bold text-gray-800 text-lg">Thông báo</h3>
                                <p class="text-sm text-gray-600 mt-0.5">Bạn có 2 thông báo mới</p>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="flex gap-4 p-4 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 border-b transition">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <i class="fa-solid fa-clipboard-check text-white text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">Kế hoạch mới cần duyệt</p>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Bộ môn HTTT gửi kế hoạch cuộc thi mới</p>
                                        <p class="text-xs text-cyan-600 mt-2 font-medium">5 phút trước</p>
                                    </div>
                                </a>
                                <a href="#" class="flex gap-4 p-4 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 border-b transition">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <i class="fa-solid fa-trophy text-white text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">Cuộc thi hoàn thành</p>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Olympic Tin học đã kết thúc thành công</p>
                                        <p class="text-xs text-cyan-600 mt-2 font-medium">2 giờ trước</p>
                                    </div>
                                </a>
                            </div>
                            <a href="#" class="block p-4 text-center text-sm text-cyan-600 hover:bg-cyan-50 font-semibold transition">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>

                    {{-- User Avatar --}}
                    <div class="user-avatar w-11 h-11 rounded-full flex items-center justify-center font-bold text-white shadow-lg hover:scale-110 transition-transform cursor-pointer">
                        A
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Toast Notification --}}
    @if(session('toast'))
    <script>
        window.LaravelToast = @json(session('toast'));
    </script>
    @endif

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>

</html>