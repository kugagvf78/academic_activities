@extends('layouts.client')

@section('title', 'Đăng ký')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50 py-8">
    <div class="max-w-md w-full mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Đăng ký tài khoản</h2>
            <p class="mt-2 text-sm text-gray-600">Tạo tài khoản mới để tham gia hội thảo</p>
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
        <form method="POST" action="{{ route('register.post') }}" class="space-y-4" novalidate>
            @csrf

            {{-- Họ tên --}}
            <div>
                <label for="HoTen" class="block mb-1.5 font-semibold text-gray-700">
                    Họ và tên <span class="text-red-500">*</span>
                </label>
                <input type="text" id="HoTen" name="HoTen" value="{{ old('HoTen') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Nhập họ và tên đầy đủ" maxlength="150" required>
                @error('HoTen')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tên đăng nhập --}}
            <div>
                <label for="TenDangNhap" class="block mb-1.5 font-semibold text-gray-700">
                    Tên đăng nhập <span class="text-red-500">*</span>
                </label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" value="{{ old('TenDangNhap') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Chọn tên đăng nhập" autocomplete="username" required>
                @error('TenDangNhap')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="Email" class="block mb-1.5 font-semibold text-gray-700">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="Email" name="Email" value="{{ old('Email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="email@example.com" autocomplete="email" required>
                @error('Email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số điện thoại --}}
            <div>
                <label for="SoDienThoai" class="block mb-1.5 font-semibold text-gray-700">
                    Số điện thoại
                </label>
                <input type="tel" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Nhập số điện thoại" maxlength="20">
                @error('SoDienThoai')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div>
                <label for="MatKhau" class="block mb-1.5 font-semibold text-gray-700">
                    Mật khẩu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhau" name="MatKhau"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Tối thiểu 6 ký tự" autocomplete="new-password" required>
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition focus:outline-none"
                        tabindex="-1">
                        <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eyeSlashIcon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
                @error('MatKhau')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div>
                <label for="MatKhau_confirmation" class="block mb-1.5 font-semibold text-gray-700">
                    Xác nhận mật khẩu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhau_confirmation" name="MatKhau_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Nhập lại mật khẩu" autocomplete="new-password" required>
                    <button type="button" id="togglePasswordConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition focus:outline-none"
                        tabindex="-1">
                        <svg id="eyeIconConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eyeSlashIconConfirm" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
                @error('MatKhau_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" id="registerBtn"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition disabled:opacity-50 disabled:cursor-not-allowed mt-6">
                <span id="registerBtnText">Đăng ký</span>
            </button>

            {{-- Login link --}}
            <p class="text-center text-sm text-gray-600 mt-4">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Đăng nhập ngay</a>
            </p>
        </form>
    </div>
</div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const registerBtn = document.getElementById('registerBtn');
        const registerBtnText = document.getElementById('registerBtnText');
        
        // Password toggle
        const passwordInput = document.getElementById('MatKhau');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        // Password confirmation toggle
        const passwordConfirmInput = document.getElementById('MatKhau_confirmation');
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');
        const eyeSlashIconConfirm = document.getElementById('eyeSlashIconConfirm');

        // Form submit
        form.addEventListener('submit', function(e) {
            if (registerBtn.disabled) {
                e.preventDefault();
                return;
            }
            registerBtn.disabled = true;
            registerBtnText.textContent = 'Đang xử lý...';
            setTimeout(() => {
                registerBtn.disabled = false;
                registerBtnText.textContent = 'Đăng ký';
            }, 5000);
        });

        // Toggle password visibility
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('hidden');
                eyeSlashIcon.classList.toggle('hidden');
            });
        }

        // Toggle password confirmation visibility
        if (togglePasswordConfirm && passwordConfirmInput) {
            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmInput.setAttribute('type', type);
                eyeIconConfirm.classList.toggle('hidden');
                eyeSlashIconConfirm.classList.toggle('hidden');
            });
        }

        // Focus first input
        const firstInput = document.getElementById('HoTen');
        if (firstInput && !firstInput.value) {
            firstInput.focus();
        }
    });
</script>
@endsection