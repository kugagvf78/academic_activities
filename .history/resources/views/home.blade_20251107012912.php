@extends('layouts.app')

@section('title', 'Trang chính')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="max-w-lg w-full bg-white p-8 rounded-2xl shadow-md border border-gray-100 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            👋 Xin chào, {{ Auth::user()->TenDangNhap }}
        </h1>

        <p class="text-gray-600 mb-8">
            Chào mừng bạn đã đăng nhập hệ thống Quản lý hoạt động học thuật.
        </p>

        <div class="flex justify-center gap-4">
            {{-- Nút đổi mật khẩu --}}
            <a href="{{ route('password.change') }}" 
               class="bg-blue-500 text-white px-5 py-2 rounded-md text-base font-medium 
                      hover:bg-blue-600 transition-all duration-200 shadow-sm">
                Đổi mật khẩu
            </a>

            {{-- Nút đăng xuất --}}
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="bg-red-600 text-white px-5 py-2 rounded-md text-base font-medium 
                               hover:bg-red-700 transition-all duration-200 shadow-sm">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
