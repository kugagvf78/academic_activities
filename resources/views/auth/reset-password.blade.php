@extends('layouts.client')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <div class="max-w-md w-full mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-lock text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Đặt lại mật khẩu</h2>
            <p class="mt-2 text-sm text-gray-600">Nhập mật khẩu mới cho tài khoản của bạn</p>
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

        <form method="POST" action="{{ route('password.reset.post') }}" class="space-y-5">
            @csrf

            <div>
                <label for="Email" class="block mb-1.5 font-semibold text-gray-700">Email</label>
                <input type="email" id="Email" name="Email" value="{{ session('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base bg-gray-50" readonly>
            </div>

            <div>
                <label for="MatKhau" class="block mb-1.5 font-semibold text-gray-700">
                    Mật khẩu mới <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhau" name="MatKhau"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Ít nhất 6 ký tự" required autofocus>
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition" tabindex="-1">
                        <svg class="eye-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-slash-icon h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="MatKhau_confirmation" class="block mb-1.5 font-semibold text-gray-700">
                    Xác nhận mật khẩu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="MatKhau_confirmation" name="MatKhau_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Nhập lại mật khẩu mới" required>
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition" tabindex="-1">
                        <svg class="eye-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-slash-icon h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M2.5 2.5L21.5 21.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fa-solid fa-check mr-2"></i>
                Cập nhật mật khẩu
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
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
});
</script>
@endsection