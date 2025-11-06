@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
{{-- ✅ Thêm lớp flex để căn giữa --}}
<div class="flex items-center justify-center min-h-screen bg-gray-50  border border-gray-100" >
    <div class="max-w-md w-full mx-auto p-5 bg-white rounded-xl shadow-sm">
        <div class="bg-white p-8 rounded-xl">
            <div class="mb-6 text-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    Đăng nhập tài khoản
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Đăng nhập vào tài khoản để bắt đầu
                </p>
            </div>

            {{-- Success message --}}
            @if(session('success'))
            <div class="bg-green-50 text-green-600 border border-green-200 px-4 py-3 rounded-md mb-4 text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- Error messages --}}
            @if($errors->any())
            <div class="bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-md mb-4 text-sm">
                <ul class="list-none m-0 p-0">
                    @foreach($errors->all() as $error)
                    <li class="mb-1 last:mb-0">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('login.post') }}" class="mb-6" novalidate>
                @csrf

                @php
                $usernameInputClass = $errors->has('TenDangNhap')
                ? 'w-full px-4 py-3 border border-red-300 rounded-md text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:border-red-600 focus:ring-red-600/20 hover:border-gray-400'
                : 'w-full px-4 py-3 border border-gray-300 rounded-md text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:border-red-600 focus:ring-red-600/20 hover:border-gray-400';
                @endphp

                {{-- Tên đăng nhập --}}
                <div class="mb-5">
                    <label for="TenDangNhap" class="block mb-1.5 font-medium text-gray-700">
                        Tên đăng nhập: <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        id="TenDangNhap"
                        name="TenDangNhap"
                        value="{{ old('TenDangNhap') }}"
                        class="{{ $usernameInputClass }}"
                        placeholder="Nhập tên đăng nhập"
                        autocomplete="username"
                        required>
                    @error('TenDangNhap')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @php
                $passwordInputClass = $errors->has('MatKhau')
                ? 'w-full px-4 py-3 border border-red-300 rounded-md text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:border-red-600 focus:ring-red-600/20 hover:border-gray-400 pr-12'
                : 'w-full px-4 py-3 border border-gray-300 rounded-md text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:border-red-600 focus:ring-red-600/20 hover:border-gray-400 pr-12';
                @endphp

                {{-- Mật khẩu --}}
                <div class="mb-5">
                    <label for="MatKhau" class="block mb-1.5 font-medium text-gray-700">
                        Mật khẩu: <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password"
                            id="MatKhau"
                            name="MatKhau"
                            class="{{ $passwordInputClass }}"
                            placeholder="Nhập mật khẩu"
                            autocomplete="current-password"
                            required>
                        {{-- Toggle password visibility --}}
                        <button type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                            tabindex="-1">
                            <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eyeSlashIcon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M9.878 9.878l.785-.785m4.242 4.242L15.95 15.95m0 0l1.414 1.414M15.95 15.95l.785-.785M2.5 2.5L21.5 21.5"></path>
                            </svg>
                        </button>
                    </div>
                    @error('MatKhau')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me & Quên mật khẩu --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <input type="checkbox"
                            id="remember"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 bg-white border-gray-300 rounded 
                                    focus:ring-red-600 focus:ring-2 accent-red-600">
                        <label for="remember" class="text-sm text-gray-500 cursor-pointer">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    <a href="#"
                        class="text-sm text-red-600 hover:text-red-700 hover:underline no-underline">
                        Quên mật khẩu?
                    </a>
                </div>

                {{-- Submit button --}}
                <button type="submit"
                    id="loginBtn"
                    class="w-full bg-red-600 text-white py-3 px-6 rounded-md text-base font-medium 
                               transition-all duration-200 cursor-pointer
                               hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg 
                               focus:outline-none focus:ring-2 focus:ring-red-600/50
                               active:transform-none active:bg-red-800 
                               disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:transform-none">
                    <span id="loginBtnText">Đăng nhập</span>
                </button>
            </form>

            {{-- Registration link --}}
            <p class="text-center mt-6 mb-0">
                <a href="#"
                    class="text-red-600 no-underline text-sm font-medium 
                          hover:text-red-700 hover:underline">
                    Chưa có tài khoản? Đăng ký
                </a>
            </p>
        </div>
    </div>
</div>

{{-- Giữ nguyên script --}}
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