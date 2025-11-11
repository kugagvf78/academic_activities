@extends('layouts.client')

@section('title', 'Đăng nhập')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <div class="max-w-md w-full mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Đăng nhập hệ thống</h2>
            <p class="mt-2 text-sm text-gray-600">Quản lý hội thảo khoa Công nghệ Thông tin</p>
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

        {{-- FORM --}}
        <form method="POST" action="{{ route('login.post') }}" class="space-y-5" novalidate onsubmit="toggleLoadingSpinner(true)">
            @csrf

            {{-- Tên đăng nhập --}}
            <div>
                <label for="TenDangNhap" class="block mb-1.5 font-semibold text-gray-700">Tên đăng nhập <span class="text-red-500">*</span></label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" value="{{ old('TenDangNhap') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Nhập tên đăng nhập" autocomplete="username" required>
                @error('TenDangNhap')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div>
                <label for="MatKhau" class="block mb-1.5 font-semibold text-gray-700">Mật khẩu <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" id="MatKhau" name="MatKhau"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Nhập mật khẩu" autocomplete="current-password" required>
                    {{-- Toggle password --}}
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition focus:outline-none"
                        tabindex="-1">
                        <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eyeSlashIcon" class="h-5 w-5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
                @error('MatKhau')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me & Quên mật khẩu --}}
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2 text-gray-600 cursor-pointer">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 accent-blue-600">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline font-medium">Quên mật khẩu?</a>
            </div>

            {{-- Submit --}}
            <button type="submit" id="loginBtn"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="loginBtnText">Đăng nhập</span>
            </button>

            {{-- Register link --}}
            <p class="text-center text-sm text-gray-600 mt-6">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Đăng ký ngay</a>
            </p>
        </form>
    </div>
</div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const loginBtn = document.getElementById('loginBtn');
        const loginBtnText = document.getElementById('loginBtnText');
        const passwordInput = document.getElementById('MatKhau');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        form.addEventListener('submit', function(e) {
            if (loginBtn.disabled) {
                e.preventDefault();
                return;
            }
            loginBtn.disabled = true;
            loginBtnText.textContent = 'Đang xử lý...';
            setTimeout(() => {
                loginBtn.disabled = false;
                loginBtnText.textContent = 'Đăng nhập';
            }, 5000);
        });

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('hidden');
                eyeSlashIcon.classList.toggle('hidden');
            });
        }

        const usernameInput = document.getElementById('TenDangNhap');
        if (usernameInput && !usernameInput.value) {
            usernameInput.focus();
        }
    });
</script>
@endsection
