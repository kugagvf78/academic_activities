<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Admin') - Hệ thống Quản lý</title>
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Custom Styles --}}
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    {{-- Sidebar --}}
    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">
        
    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
        class="bg-gradient-to-b from-slate-800 to-slate-900 text-white transition-all duration-300 flex flex-col">
        
        {{-- Logo --}}
        <div class="p-4 border-b border-slate-700">
            <div class="flex items-center justify-between">
                <h1 x-show="sidebarOpen" class="text-xl font-bold">Admin Panel</h1>
                <button @click="sidebarOpen = !sidebarOpen" 
                    class="p-2 hover:bg-slate-700 rounded-lg transition">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
        
        {{-- Navigation --}}
        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700' : '' }}">
                        <i class="fa-solid fa-home text-lg"></i>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" 
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-700' : '' }}">
                        <i class="fa-solid fa-users text-lg"></i>
                        <span x-show="sidebarOpen">Người dùng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.news.index') }}" 
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.news.*') ? 'bg-slate-700' : '' }}">
                        <i class="fa-solid fa-newspaper text-lg"></i>
                        <span x-show="sidebarOpen">Tin tức</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.competitions.index') }}" 
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition {{ request()->routeIs('admin.competitions.*') ? 'bg-slate-700' : '' }}">
                        <i class="fa-solid fa-trophy text-lg"></i>
                        <span x-show="sidebarOpen">Cuộc thi</span>
                    </a>
                </li>
                <li>
                    <a href="#" 
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition">
                        <i class="fa-solid fa-cog text-lg"></i>
                        <span x-show="sidebarOpen">Cài đặt</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        {{-- User Profile with Dropdown --}}
        <div class="p-4 border-t border-slate-700 relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen" 
                class="w-full flex items-center gap-3 hover:bg-slate-700 rounded-lg p-2 transition">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                    {{ strtoupper(substr($user->hoten, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen" class="flex-1 text-left">
                    <p class="font-semibold truncate">{{ $user->hoten }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                </div>
                <i x-show="sidebarOpen" class="fa-solid fa-chevron-up text-xs transition-transform" 
                    :class="profileOpen ? 'rotate-180' : ''"></i>
            </button>
            
            {{-- Dropdown Menu --}}
            <div x-show="profileOpen" 
                x-cloak
                @click.away="profileOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-lg shadow-xl py-2 border border-slate-200"
                style="display: none;">
                
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="w-full flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="text-sm font-medium">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>
        
        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            
            {{-- Header --}}
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">@yield('page-title')</h2>
                        <p class="text-sm text-slate-600">@yield('breadcrumb')</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="p-2 hover:bg-slate-100 rounded-lg transition relative">
                            <i class="fa-solid fa-bell text-slate-600"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <button class="p-2 hover:bg-slate-100 rounded-lg transition">
                            <i class="fa-solid fa-user-circle text-slate-600 text-xl"></i>
                        </button>
                    </div>
                </div>
            </header>
            
            {{-- Content Area --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    {{-- Scripts --}}
    <script>
        // Configure Tailwind
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#8B5CF6',
                    }
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>