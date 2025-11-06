@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-xl shadow-md border border-gray-200">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Đổi mật khẩu</h2>

    {{-- Thông báo thành công --}}
    @if(session('success'))
    <div class="bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-md mb-4">
        {{ session('success') }}
    </div>
    @endif

    {{-- Thông báo lỗi --}}
    @if($errors->any())
    <div class="bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-md mb-4">
        <ul class="list-disc ml-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        {{-- Mật khẩu cũ --}}
        <div class="mb-5">
            <label for="MatKhauCu" class="block mb-2 text-sm font-medium text-gray-700">
                Mật khẩu hiện tại <span class="text-red-500">*</span>
            </label>
            <input type="password" id="MatKhauCu" name="MatKhauCu"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
                placeholder="Nhập mật khẩu hiện tại" required>
        </div>

        {{-- Mật khẩu mới --}}
        <div class="mb-5">
            <label for="MatKhauMoi" class="block mb-2 text-sm font-medium text-gray-700">
                Mật khẩu mới <span class="text-red-500">*</span>
            </label>
            <input type="password" id="MatKhauMoi" name="MatKhauMoi"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
                placeholder="Nhập mật khẩu mới" required>
        </div>

        {{-- Xác nhận mật khẩu mới --}}
        <div class="mb-5">
            <label for="MatKhauMoi_confirmation" class="block mb-2 text-sm font-medium text-gray-700">
                Xác nhận mật khẩu mới <span class="text-red-500">*</span>
            </label>
            <input type="password" id="MatKhauMoi_confirmation" name="MatKhauMoi_confirmation"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none"
                placeholder="Nhập lại mật khẩu mới" required>
        </div>

        {{-- Nút đổi mật khẩu --}}
        <button type="submit"
            class="w-full bg-red-600 text-white py-2 rounded-md font-medium hover:bg-red-700 transition duration-200">
            Cập nhật mật khẩu
        </button>

        {{-- Quay lại trang chủ --}}
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
                ← Quay lại trang chính
            </a>
        </div>
    </form>
</div>
@endsection