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
            <p class="mt-2 text-sm text-gray-600">Cập nhật mật khẩu mới để bảo mật tài khoản</p>
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
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5" novalidate>
            @csrf

            {{-- Mật khẩu hiện tại --}}
            <div>
                <label for="MatKhauCu" class="block mb-1.5 font-semibold text-gray-700">
                    Mật khẩu hiện tại <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhauCu" name="MatKhauCu"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Nhập mật khẩu hiện tại" required autocomplete="current-password">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition focus:outline-none" tabindex="-1">
                        <svg class="eye-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-slash-icon h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
                @error('MatKhauCu')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mật khẩu mới --}}
            <div>
                <label for="MatKhauMoi" class="block mb-1.5 font-semibold text-gray-700">
                    Mật khẩu mới <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhauMoi" name="MatKhauMoi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Nhập mật khẩu mới" required autocomplete="new-password">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition focus:outline-none" tabindex="-1">
                        <svg class="eye-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-slash-icon h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
                @error('MatKhauMoi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Xác nhận mật khẩu mới --}}
            <div>
                <label for="MatKhauMoi_confirmation" class="block mb-1.5 font-semibold text-gray-700">
                    Xác nhận mật khẩu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhauMoi_confirmation" name="MatKhauMoi_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Nhập lại mật khẩu mới" required autocomplete="new-password">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition focus:outline-none" tabindex="-1">
                        <svg class="eye-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-slash-icon h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition">
                Cập nhật mật khẩu
            </button>

            {{-- Quay lại trang chủ --}}
            <p class="text-center text-sm text-gray-600 mt-6">
                <a href="{{ route('client.home') }}" class="text-blue-600 font-semibold hover:underline">
                    ← Quay lại trang chính
                </a>
            </p>
        </form>
    </div>
</div>

{{-- Script: Toggle Password Visibility --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {
                const input = this.closest('div').querySelector('input');
                const eyeIcon = this.querySelector('.eye-icon');
                const eyeSlashIcon = this.querySelector('.eye-slash-icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
            });
        });

        // Focus vào ô đầu tiên nếu trống
        const firstInput = document.querySelector('input[autofocus]') || document.querySelector('input[name="MatKhauCu"]');
        if (firstInput && !firstInput.value) {
            firstInput.focus();
        }
    });
</script>
@endsection