@extends('layouts.client')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <div class="max-w-md w-full mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-envelope text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Quên mật khẩu?</h2>
            <p class="mt-2 text-sm text-gray-600">Nhập email để nhận mã OTP xác thực</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-none space-y-1 m-0 p-0">
                @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-5">
            @csrf

            <div>
                <label for="Email" class="block mb-1.5 font-semibold text-gray-700">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="Email" name="Email" value="{{ old('Email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Nhập email đăng ký" required autofocus>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fa-solid fa-paper-plane mr-2"></i>
                Gửi mã OTP
            </button>

            <p class="text-center text-sm text-gray-600 mt-6">
                <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                    ← Quay lại đăng nhập
                </a>
            </p>
        </form>
    </div>
</div>
@endsection