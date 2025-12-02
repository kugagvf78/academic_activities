{{-- resources/views/auth/change-password.blade.php --}}
@extends('layouts.client')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <div class="max-w-md w-full mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-key text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Đổi mật khẩu</h2>
            <p class="mt-2 text-sm text-gray-600">Xác thực qua email để đảm bảo an toàn</p>
        </div>

        {{-- Success message --}}
        @if(session('success'))
        <div class="bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
        @endif

        {{-- Error messages --}}
        @if($errors->any())
        <div class="bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-none space-y-1 m-0 p-0">
                @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORM GỬI OTP --}}
        <form method="POST" action="{{ route('password.change.send-otp') }}" class="space-y-5" novalidate>
            @csrf

            {{-- Hiển thị email (readonly) --}}
            <div>
                <label for="email" class="block mb-1.5 font-semibold text-gray-700">
                    Email xác thực
                </label>
                <div class="relative">
                    <input type="email" id="email" name="email" 
                        value="{{ $user->email }}" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base bg-gray-50 cursor-not-allowed focus:outline-none">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>Mã OTP sẽ được gửi đến email này
                </p>
            </div>

            {{-- Thông tin hướng dẫn --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <i class="fas fa-shield-alt text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-900">
                        <p class="font-medium mb-1">Quy trình đổi mật khẩu an toàn:</p>
                        <ol class="list-decimal list-inside space-y-0.5 text-blue-800">
                            <li>Nhận mã OTP qua email</li>
                            <li>Xác thực mã OTP (có hiệu lực 5 phút)</li>
                            <li>Đặt mật khẩu mới</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fas fa-paper-plane mr-2"></i>Gửi mã OTP xác thực
            </button>

            {{-- Quay lại --}}
            <div class="flex items-center justify-between text-sm mt-6">
                <a href="{{ route('client.home') }}" class="text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-arrow-left mr-1"></i>Quay lại
                </a>
                <a href="{{ route('profile.index') }}" class="text-blue-600 hover:underline font-medium">
                    Hồ sơ của tôi
                </a>
            </div>
        </form>
    </div>
</div>
@endsection