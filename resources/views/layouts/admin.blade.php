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

        .sidebar-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, #3b82f6, #0ea5e9);
            border-radius: 0 4px 4px 0;
            transition: all 0.3s ease;
        }

        .sidebar-link.active::before,
        .sidebar-link:hover::before {
            height: 100%;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--tw-gradient-stops));
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    @stack('styles')
</head>

<body class="font-oswald bg-gray-50 text-gray-800" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">

    {{-- Loading Spinner --}}
    <div id="loadingSpinner"
        class="hidden fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm flex-col items-center justify-center">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-3"></div>
        <p class="text-blue-700 font-semibold text-sm">Đang xử lý...</p>
    </div>

    <div class="flex h-screen overflow-hidden">
        
        {{-- SIDEBAR --}}
        <aside 
            :class="sidebarOpen ? 'w-72' : 'w-20'"
            class="hidden lg:flex flex-col bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 text-white transition-all duration-300 shadow-2xl">
            
            {{-- Logo & Brand --}}
            <div class="flex items-center justify-between p-4 border-b border-blue-700/50">
                <a href="#" class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-shield text-white text-xl"></i>
                    </div>
                    <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        <h1 class="font-bold text-lg leading-tight">Quản trị viên</h1>
                        <p class="text-xs text-blue-200">Khoa CNTT</p>
                    </div>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" 
                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                    <i class="fa-solid fa-angles-left text-sm transition-transform" :class="!sidebarOpen && 'rotate-180'"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-1">
                
                {{-- Dashboard --}}
                <a href="#" 
                    class="sidebar-link active bg-white/10 flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-chart-line w-5 text-center text-blue-300"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Dashboard</span>
                </a>

                {{-- Quản trị hệ thống --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" 
                        class="sidebar-link w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-cogs w-5 text-center text-blue-300"></i>
                            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Quản trị hệ thống</span>
                        </div>
                        <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-xs transition-transform" :class="open && 'rotate-180'"></i>
                    </button>
                    <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-users w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Người dùng</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-building w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Bộ môn</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-chalkboard w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Lớp học</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-user-tie w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Giảng viên</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-user-graduate w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Sinh viên</span>
                        </a>
                    </div>
                </div>

                {{-- Kế hoạch cuộc thi --}}
                <a href="#" 
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-clipboard-list w-5 text-center text-blue-300"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Duyệt kế hoạch</span>
                    <span x-show="sidebarOpen" class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">3</span>
                </a>

                {{-- Quản lý cuộc thi --}}
                <a href="#" 
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-trophy w-5 text-center text-blue-300"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Cuộc thi</span>
                </a>

                {{-- Quản lý tài chính --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" 
                        class="sidebar-link w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wallet w-5 text-center text-blue-300"></i>
                            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Tài chính</span>
                        </div>
                        <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-xs transition-transform" :class="open && 'rotate-180'"></i>
                    </button>
                    <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-money-bill-wave w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Chi phí</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-file-invoice-dollar w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Quyết toán</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                            <i class="fa-solid fa-chart-pie w-4 text-center text-blue-200"></i>
                            <span x-show="sidebarOpen" x-transition>Báo cáo TC</span>
                        </a>
                    </div>
                </div>

                {{-- Tin tức --}}
                <a href="#" 
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-newspaper w-5 text-center text-blue-300"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Tin tức</span>
                </a>

                {{-- Báo cáo & Thống kê --}}
                <a href="#" 
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-chart-bar w-5 text-center text-blue-300"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Báo cáo & Thống kê</span>
                </a>

                {{-- Divider --}}
                <div class="my-4 border-t border-blue-700/50"></div>

                {{-- Cài đặt --}}
                <a href="#" 
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                    <i class="fa-solid fa-gear w-5 text-center text-blue-300"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap font-medium">Cài đặt</span>
                </a>

            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-blue-700/50">
                <div x-data="{ userMenu: false }" class="relative">
                    <button @click="userMenu = !userMenu"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            A
                        </div>
                        <div x-show="sidebarOpen" x-transition class="flex-1 text-left overflow-hidden">
                            <p class="font-semibold text-sm truncate">Admin User</p>
                            <p class="text-xs text-blue-200 truncate">admin@huit.edu.vn</p>
                        </div>
                        <i x-show="sidebarOpen" class="fa-solid fa-ellipsis-vertical text-blue-200"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="userMenu" 
                        x-cloak
                        @click.away="userMenu = false"
                        x-transition
                        class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-xl overflow-hidden">
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-blue-50 transition text-sm">
                            <i class="fa-solid fa-user w-4"></i>
                            <span>Hồ sơ</span>
                        </a>
                        <a href="#" 
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-blue-50 transition text-sm">
                            <i class="fa-solid fa-home w-4"></i>
                            <span>Trang chủ</span>
                        </a>
                        <div class="border-t">
                            <button type="button" 
                                class="w-full flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition text-sm">
                                <i class="fa-solid fa-right-from-bracket w-4"></i>
                                <span>Đăng xuất</span>
                            </button>
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
                class="w-72 h-full bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 text-white overflow-y-auto">
                
                {{-- Header --}}
                <div class="p-4 border-b border-blue-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-user-shield text-xl"></i>
                            </div>
                            <div>
                                <h1 class="font-bold text-lg">Quản trị viên</h1>
                                <p class="text-xs text-blue-200">Khoa CNTT</p>
                            </div>
                        </div>
                        <button @click="mobileSidebarOpen = false" 
                            class="w-8 h-8 rounded-lg hover:bg-white/10">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <nav class="p-4 space-y-1">
                    <a href="#" 
                        @click="mobileSidebarOpen = false"
                        class="sidebar-link active bg-white/10 flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" 
                            class="sidebar-link w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-cogs w-5"></i>
                                <span class="font-medium">Quản trị hệ thống</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open && 'rotate-180'"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-users w-4"></i>
                                <span>Người dùng</span>
                            </a>
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-building w-4"></i>
                                <span>Bộ môn</span>
                            </a>
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-chalkboard w-4"></i>
                                <span>Lớp học</span>
                            </a>
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-user-tie w-4"></i>
                                <span>Giảng viên</span>
                            </a>
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-user-graduate w-4"></i>
                                <span>Sinh viên</span>
                            </a>
                        </div>
                    </div>

                    <a href="#" @click="mobileSidebarOpen = false" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-clipboard-list w-5"></i>
                        <span>Duyệt kế hoạch</span>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">3</span>
                    </a>

                    <a href="#" @click="mobileSidebarOpen = false" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-trophy w-5"></i>
                        <span>Cuộc thi</span>
                    </a>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" 
                            class="sidebar-link w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-wallet w-5"></i>
                                <span class="font-medium">Tài chính</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open && 'rotate-180'"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-money-bill-wave w-4"></i>
                                <span>Chi phí</span>
                            </a>
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-file-invoice-dollar w-4"></i>
                                <span>Quyết toán</span>
                            </a>
                            <a href="#" @click="mobileSidebarOpen = false" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/10 text-sm">
                                <i class="fa-solid fa-chart-pie w-4"></i>
                                <span>Báo cáo TC</span>
                            </a>
                        </div>
                    </div>

                    <a href="#" @click="mobileSidebarOpen = false" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-newspaper w-5"></i>
                        <span>Tin tức</span>
                    </a>

                    <a href="#" @click="mobileSidebarOpen = false" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-chart-bar w-5"></i>
                        <span>Báo cáo & Thống kê</span>
                    </a>

                    <div class="my-4 border-t border-blue-700/50"></div>

                    <a href="#" @click="mobileSidebarOpen = false" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-gear w-5"></i>
                        <span>Cài đặt</span>
                    </a>
                </nav>
            </aside>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            
            {{-- TOP NAVBAR --}}
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                
                {{-- Left: Hamburger + Breadcrumb --}}
                <div class="flex items-center gap-4">
                    <button @click="mobileSidebarOpen = true" 
                        class="lg:hidden w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    
                    <div class="hidden sm:block">
                        <h2 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        <div class="text-sm text-gray-500 flex items-center gap-2 mt-0.5">
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
                            class="w-64 pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    {{-- Notifications --}}
                    <div x-data="{ notifOpen: false }" class="relative">
                        <button @click="notifOpen = !notifOpen"
                            class="relative w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center">
                            <i class="fa-solid fa-bell text-gray-600"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <div x-show="notifOpen" 
                            x-cloak
                            @click.away="notifOpen = false"
                            x-transition
                            class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border overflow-hidden">
                            <div class="p-4 border-b">
                                <h3 class="font-bold text-gray-800">Thông báo</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <a href="#" class="flex gap-3 p-4 hover:bg-gray-50 border-b">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-clipboard-check text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Kế hoạch mới cần duyệt</p>
                                        <p class="text-xs text-gray-500 mt-1">Bộ môn HTTT gửi kế hoạch cuộc thi</p>
                                        <p class="text-xs text-blue-600 mt-1">5 phút trước</p>
                                    </div>
                                </a>
                                <a href="#" class="flex gap-3 p-4 hover:bg-gray-50 border-b">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-trophy text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Cuộc thi hoàn thành</p>
                                        <p class="text-xs text-gray-500 mt-1">Olympic Tin học đã kết thúc</p>
                                        <p class="text-xs text-blue-600 mt-1">2 giờ trước</p>
                                    </div>
                                </a>
                            </div>
                            <a href="#" class="block p-3 text-center text-sm text-blue-600 hover:bg-blue-50 font-medium">
                                Xem tất cả
                            </a>
                        </div>
                    </div>

                    {{-- User Avatar --}}
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center font-bold text-white">
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