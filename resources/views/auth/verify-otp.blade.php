@extends('layouts.client')

@section('title', 'Xác thực OTP')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <div class="max-w-md w-full mx-auto p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-shield-halved text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Xác thực OTP</h2>
            <p class="mt-2 text-sm text-gray-600">
                Mã OTP đã được gửi đến<br>
                <strong class="text-blue-600">{{ session('email') }}</strong>
            </p>
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

        <form method="POST" action="{{ route('password.verify-otp.post') }}" class="space-y-5">
            @csrf

            <div>
                <label for="otp" class="block mb-1.5 font-semibold text-gray-700 text-center">
                    Nhập mã OTP (6 chữ số)
                </label>
                <input type="text" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-2xl font-bold tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="000000" required autofocus>
                <p class="mt-2 text-xs text-gray-500 text-center">
                    <i class="fa-solid fa-clock mr-1"></i>
                    Mã OTP có hiệu lực trong 5 phút
                </p>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white py-3 rounded-lg text-base font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition">
                <i class="fa-solid fa-check-circle mr-2"></i>
                Xác thực
            </button>
        </form>

        <!-- Gửi lại OTP -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-2">Không nhận được mã?</p>
            <form method="POST" action="{{ route('password.resend-otp') }}" class="inline">
                @csrf
                <button type="submit" class="text-blue-600 font-semibold hover:underline text-sm">
                    <i class="fa-solid fa-rotate-right mr-1"></i>
                    Gửi lại mã OTP
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="{{ route('password.request') }}" class="text-blue-600 font-semibold hover:underline">
                ← Thay đổi email
            </a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    
    // Chỉ cho phép nhập số
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Auto submit khi nhập đủ 6 số
    otpInput.addEventListener('input', function() {
        if (this.value.length === 6) {
            this.form.submit();
        }
    });
});
</script>
@endsection