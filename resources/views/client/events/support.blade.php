@extends('layouts.client')
@section('title', 'Đăng ký hỗ trợ Ban tổ chức - ' . $cuocthi->tencuocthi)

@section('content')

{{-- HEADER SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">
    <div class="container mx-auto px-6 text-center relative z-10">
        <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-4">
            <span class="text-sm font-medium">{{ $cuocthi->loaicuocthi ?? 'Cuộc thi' }}</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Đăng ký hỗ trợ Ban tổ chức</h1>
        <p class="text-blue-100 text-lg mb-6">{{ $cuocthi->tencuocthi }}</p>
        <p class="text-blue-50 text-base">Trở thành một phần của đội ngũ tổ chức [power]</p>
    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z" />
        </svg>
    </div>
</section>

{{-- TOAST NOTIFICATION --}}
@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show"
     x-init="setTimeout(() => { show = false }, 5000);"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-full"
     class="fixed top-6 right-6 z-50 min-w-[320px] max-w-md">
    
    <div class="bg-white rounded-xl shadow-2xl border border-indigo-100 overflow-hidden">
        <div class="h-1 bg-indigo-500 animate-[shrink_5s_linear_forwards]"></div>
        
        <div class="p-4 flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-indigo-600 text-lg"></i>
            </div>
            
            <div class="flex-1 pt-0.5">
                <h4 class="font-bold text-gray-900 mb-1">Đăng ký thành công!</h4>
                <p class="text-sm text-gray-600">{{ session('success') }}</p>
            </div>
            
            <button @click="show = false" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes shrink {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
@endif

{{-- FORM SECTION --}}
<section class="container mx-auto px-6 py-16">
    @if($hoatdongs->isEmpty())
        {{-- Không có hoạt động --}}
        <div class="max-w-2xl mx-auto text-center py-16">
            <div class="bg-white rounded-2xl shadow-lg p-12 border border-gray-100">
                <i class="fa-solid fa-calendar-xmark text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Chưa có hoạt động hỗ trợ</h3>
                <p class="text-gray-600 mb-6">Cuộc thi này hiện chưa có hoạt động hỗ trợ Ban tổ chức nào được mở đăng ký.</p>
                <a href="{{ route('client.events.show', $slug) }}" 
                   class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    Quay lại chi tiết cuộc thi
                </a>
            </div>
        </div>
    @else
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-10"
             x-data="supportForm()">

            {{-- ERROR MESSAGE --}}
            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <p class="font-semibold mb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Có lỗi xảy ra:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Title --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Thông tin đăng ký hỗ trợ Ban tổ chức</h2>
                <p class="text-gray-500">Hãy lựa chọn vai trò phù hợp để cùng ban tổ chức vận hành sự kiện hiệu quả nhất.</p>
            </div>

            <form action="{{ route('client.events.support.submit', $slug) }}" method="POST">
                @csrf

                {{-- Chọn hoạt động hỗ trợ --}}
                <div class="mb-8">
                    <label class="block font-semibold text-gray-700 mb-3">
                        Chọn hoạt động hỗ trợ <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="space-y-3">
                        @foreach($hoatdongs as $hoatdong)
                        <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition
                                    hover:border-indigo-400 hover:bg-indigo-50
                                    has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="mahoatdong" value="{{ $hoatdong->mahoatdong }}" 
                                   class="mt-1 text-indigo-600 focus:ring-indigo-500" required
                                   {{ old('mahoatdong') == $hoatdong->mahoatdong ? 'checked' : '' }}>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-2">{{ $hoatdong->tenhoatdong }}</h3>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar text-indigo-500"></i>
                                        <span>{{ $hoatdong->thoigianbatdau->format('d/m/Y H:i') }} - {{ $hoatdong->thoigianketthuc->format('H:i') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-red-500"></i>
                                        <span>{{ $hoatdong->diadiem }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-star text-yellow-500"></i>
                                        <span class="font-semibold text-green-600">+{{ $hoatdong->diemrenluyen }} điểm rèn luyện</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Thông tin sinh viên --}}
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                        Thông tin sinh viên
                    </h3>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                placeholder="Nguyễn Văn A">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Mã số sinh viên <span class="text-red-500">*</span></label>
                            <input type="text" name="student_code" value="{{ old('student_code') }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                placeholder="2024001234"
                                @blur="checkStudentCode($event.target.value)">
                            <p x-show="studentCodeError" x-text="studentCodeError" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Email sinh viên <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                placeholder="student@example.com">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                placeholder="0912345678">
                        </div>
                    </div>
                </div>

                {{-- Hướng dẫn --}}
                <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-blue-500"></i>
                        Lưu ý khi đăng ký
                    </h3>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 mt-1"></i>
                            <span>Bạn có thể đăng ký nhiều hoạt động hỗ trợ khác nhau</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 mt-1"></i>
                            <span>Vui lòng có mặt đúng giờ để điểm danh và nhận điểm rèn luyện</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 mt-1"></i>
                            <span>Không thể đăng ký sau khi hoạt động đã bắt đầu</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-green-600 mt-1"></i>
                            <span>Ban tổ chức sẽ liên hệ với bạn qua email để thông báo chi tiết nhiệm vụ</span>
                        </li>
                    </ul>
                </div>

                {{-- Submit Button --}}
                <div class="text-center">
                    <button type="submit"
                        class="bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md hover:shadow-xl transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        Đăng ký hỗ trợ
                    </button>
                </div>
            </form>
        </div>
    @endif
</section>

@push('scripts')
<script>
function supportForm() {
    return {
        studentCodeError: '',

        async checkStudentCode(code) {
            if (!code) {
                this.studentCodeError = '';
                return;
            }

            try {
                const response = await fetch('{{ route("client.events.check.student") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ student_code: code })
                });

                const data = await response.json();

                if (!data.exists) {
                    this.studentCodeError = 'Mã sinh viên không tồn tại trong hệ thống';
                } else {
                    this.studentCodeError = '';
                }
            } catch (error) {
                console.error('Error:', error);
                this.studentCodeError = 'Lỗi kiểm tra mã sinh viên';
            }
        }
    }
}
</script>
@endpush

@endsection