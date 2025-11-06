@extends('layouts.app')

@section('title', 'Trang chính')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-lg border border-gray-200 text-center">
        {{-- Tiêu đề --}}
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">
            🎓 Hệ thống Quản lý Hoạt động Học thuật
        </h1>
        <p class="text-gray-600 text-sm mb-8">
            Chào mừng bạn đã đăng nhập thành công!
        </p>

        {{-- Khu vực nút thao tác --}}
        <div class="flex justify-center gap-4">
            {{-- Nút đổi mật khẩu --}}
            <a href="{{ route('password.change') }}"
                class="bg-blue-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium 
                       hover:bg-blue-600 hover:scale-[1.03] transition-all duration-200 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                🔒 Đổi mật khẩu
            </a>

            {{-- Nút đăng xuất --}}
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="bg-red-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium 
                           hover:bg-red-700 hover:scale-[1.03] transition-all duration-200 shadow-sm focus:ring-2 focus:ring-red-400 focus:outline-none">
                    🚪 Đăng xuất
                </button>
            </form>
        </div>

        {{-- Dòng thông tin thêm (tuỳ chọn) --}}
        <div class="mt-8 text-xs text-gray-400">
            © {{ date('Y') }} Hệ thống học thuật | Thiết kế bởi <span class="text-gray-500 font-medium">Laravel + Tailwind</span>
        </div>
    </div>
</div>
@endsection
