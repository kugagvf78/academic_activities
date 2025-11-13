@extends('layouts.client')
@section('title', 'Đăng ký cổ vũ của tôi')

@section('content')

{{-- HEADER SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-20 overflow-hidden">
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Đăng ký cổ vũ của tôi</h1>
        <p class="text-blue-100 text-lg">Quản lý các hoạt động cổ vũ bạn đã đăng ký</p>
    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z" />
        </svg>
    </div>
</section>

{{-- NOTIFICATIONS --}}
@if(session('success'))
<div class="container mx-auto px-6 pt-6">
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check mr-3 text-xl"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="container mx-auto px-6 pt-6">
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-3 text-xl"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

{{-- CONTENT SECTION --}}
<section class="container mx-auto px-6 py-16">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('profile.index') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại hồ sơ</span>
        </a>
    </div>

    @if($registrations->isEmpty())
        {{-- Empty State --}}
        <div class="max-w-2xl mx-auto text-center py-16">
            <div class="bg-white rounded-2xl shadow-lg p-12 border border-gray-100">
                <i class="fa-solid fa-calendar-xmark text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Chưa có đăng ký cổ vũ</h3>
                <p class="text-gray-600 mb-6">Bạn chưa đăng ký hoạt động cổ vũ nào.</p>
                <a href="{{ route('client.events.index') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold px-6 py-3 rounded-xl shadow-md hover:shadow-xl transition">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Khám phá sự kiện
                </a>
            </div>
        </div>
    @else
        {{-- List of Registrations --}}
        <div class="max-w-5xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Danh sách đăng ký ({{ $registrations->count() }})</h2>
            </div>

            <div class="space-y-4">
                @foreach($registrations as $reg)
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $reg->tenhoatdong }}</h3>
                                <p class="text-gray-600 text-sm mb-2">
                                    <i class="fa-solid fa-trophy text-blue-500 mr-2"></i>
                                    {{ $reg->tencuocthi }}
                                </p>
                            </div>

                            {{-- Status Badge --}}
                            <span class="px-4 py-2 rounded-full text-sm font-semibold
                                {{ $reg->statusColor === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $reg->statusColor === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $reg->statusColor === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ $reg->statusLabel }}
                            </span>
                        </div>

                        {{-- Details --}}
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-calendar text-blue-500 mt-1"></i>
                                <div>
                                    <p class="text-gray-600 text-sm">Thời gian</p>
                                    <p class="font-semibold text-gray-800">
                                        {{ $reg->thoigianbatdau->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        đến {{ $reg->thoigianketthuc->format('H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot text-red-500 mt-1"></i>
                                <div>
                                    <p class="text-gray-600 text-sm">Địa điểm</p>
                                    <p class="font-semibold text-gray-800">{{ $reg->diadiem }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-star text-yellow-500 mt-1"></i>
                                <div>
                                    <p class="text-gray-600 text-sm">Điểm rèn luyện</p>
                                    <p class="font-bold text-green-600 text-lg">+{{ $reg->diemrenluyen }} điểm</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-clock text-purple-500 mt-1"></i>
                                <div>
                                    <p class="text-gray-600 text-sm">Ngày đăng ký</p>
                                    <p class="font-semibold text-gray-800">{{ $reg->ngaydangky->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Attendance Status --}}
                        <div class="border-t border-gray-100 pt-4 mt-4">
                            @if($reg->diemdanhqr)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                    <span class="font-semibold">Đã điểm danh</span>
                                    <span class="text-sm text-gray-500 ml-2">
                                        ({{ $reg->thoigiandiemdanh->format('d/m/Y H:i') }})
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i class="fa-solid fa-clock text-lg"></i>
                                        <span>Chưa điểm danh</span>
                                    </div>

                                    {{-- Cancel Button --}}
                                    @if($reg->canCancel)
                                        <form action="{{ route('profile.cheer.cancel', $reg->madangkyhoatdong) }}" 
                                              method="POST"
                                              onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-lg transition inline-flex items-center gap-2">
                                                <i class="fa-solid fa-xmark"></i>
                                                Hủy đăng ký
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Warning for upcoming events --}}
                        @if($reg->status === 'upcoming' && !$reg->canCancel && !$reg->diemdanhqr)
                            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Không thể hủy đăng ký trong vòng 24 giờ trước sự kiện
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Info Box --}}
        <div class="max-w-5xl mx-auto mt-8">
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-blue-500"></i>
                    Lưu ý
                </h3>
                <ul class="space-y-2 text-gray-700 text-sm">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-green-600 mt-1"></i>
                        <span>Bạn có thể hủy đăng ký trước 24 giờ bắt đầu sự kiện</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-green-600 mt-1"></i>
                        <span>Không thể hủy đăng ký đã điểm danh</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-green-600 mt-1"></i>
                        <span>Điểm rèn luyện chỉ được cộng sau khi điểm danh thành công</span>
                    </li>
                </ul>
            </div>
        </div>
    @endif
</section>

@endsection