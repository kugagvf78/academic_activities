@extends('layouts.client')
@section('title', 'Hoạt động đã đăng ký')

@section('content')

{{-- Header --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white pt-24 pb-28 overflow-hidden">
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Hoạt động của tôi</h1>
        <p class="text-indigo-100 text-lg">Quản lý các hoạt động cổ vũ bạn đã đăng ký tham gia</p>
        
        @if($sinhvien)
        <div class="mt-6 inline-flex items-center gap-3 bg-white/20 backdrop-blur-sm px-6 py-3 rounded-full">
            <i class="fa-solid fa-user-circle text-2xl"></i>
            <div class="text-left">
                <p class="font-semibold">{{ $sinhvien->hoten }}</p>
                <p class="text-sm text-indigo-200">{{ $sinhvien->masinhvien }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z" />
        </svg>
    </div>
</section>

{{-- Alert Messages --}}
@if(session('success'))
<div class="container mx-auto px-6 pt-6">
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check mr-3 text-xl"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="container mx-auto px-6 pt-6">
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-3 text-xl"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

{{-- Content --}}
<section class="container mx-auto px-6 py-16">
    
    @if($registrations->isEmpty())
        {{-- Empty State --}}
        <div class="max-w-2xl mx-auto text-center py-16">
            <div class="bg-white rounded-2xl shadow-lg p-12 border border-gray-100">
                <i class="fa-solid fa-calendar-xmark text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Chưa có hoạt động nào</h3>
                <p class="text-gray-500 mb-6">Bạn chưa đăng ký tham gia hoạt động cổ vũ nào.</p>
                <a href="{{ route('client.events.index') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold px-6 py-3 rounded-xl shadow-md hover:shadow-xl transition">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Khám phá sự kiện
                </a>
            </div>
        </div>
    @else
        {{-- Statistics --}}
        <div class="max-w-6xl mx-auto mb-8">
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Tổng hoạt động</p>
                            <p class="text-3xl font-bold">{{ $registrations->total() }}</p>
                        </div>
                        <i class="fa-solid fa-calendar-check text-4xl opacity-30"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm mb-1">Đã điểm danh</p>
                            <p class="text-3xl font-bold">{{ $registrations->where('diemdanhqr', true)->count() }}</p>
                        </div>
                        <i class="fa-solid fa-check-double text-4xl opacity-30"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm mb-1">Điểm rèn luyện</p>
                            <p class="text-3xl font-bold">
                                +{{ $registrations->where('diemdanhqr', true)->sum(function($reg) {
                                    return $reg->hoatdong->diemrenluyen ?? 0;
                                }) }}
                            </p>
                        </div>
                        <i class="fa-solid fa-star text-4xl opacity-30"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- List Activities --}}
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($registrations as $reg)
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition border border-gray-100 overflow-hidden">
                    {{-- Header card --}}
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-bold text-lg flex-1 pr-2">{{ $reg->hoatdong->tenhoatdong }}</h3>
                            @if($reg->diemdanhqr)
                                <span class="bg-white/30 backdrop-blur-sm px-2 py-1 rounded text-xs font-semibold whitespace-nowrap">
                                    <i class="fa-solid fa-check"></i> Đã điểm danh
                                </span>
                            @endif
                        </div>
                        <p class="text-blue-100 text-sm">{{ $reg->hoatdong->cuocthi->tencuocthi }}</p>
                    </div>

                    {{-- Body card --}}
                    <div class="p-5">
                        <div class="space-y-3 mb-5">
                            <div class="flex items-start gap-3 text-sm">
                                <i class="fa-solid fa-calendar text-blue-500 mt-1 w-5"></i>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-xs mb-1">Thời gian</p>
                                    <p class="font-semibold text-gray-800">
                                        {{ $reg->hoatdong->thoigianbatdau->format('d/m/Y') }}
                                    </p>
                                    <p class="text-gray-500 text-xs">
                                        {{ $reg->hoatdong->thoigianbatdau->format('H:i') }} - 
                                        {{ $reg->hoatdong->thoigianketthuc->format('H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-sm">
                                <i class="fa-solid fa-location-dot text-red-500 mt-1 w-5"></i>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-xs mb-1">Địa điểm</p>
                                    <p class="font-semibold text-gray-800">{{ $reg->hoatdong->diadiem }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-sm">
                                <i class="fa-solid fa-star text-yellow-500 mt-1 w-5"></i>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-xs mb-1">Điểm rèn luyện</p>
                                    <p class="font-semibold text-green-600">+{{ $reg->hoatdong->diemrenluyen }} điểm</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 text-sm">
                                <i class="fa-solid fa-clock text-purple-500 mt-1 w-5"></i>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-xs mb-1">Đăng ký lúc</p>
                                    <p class="font-semibold text-gray-800">
                                        {{ $reg->ngaydangky->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Status badge --}}
                        <div class="mb-4">
                            @if($reg->diemdanhqr)
                                @if($reg->thoigiandiemdanh)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fa-solid fa-circle-check text-green-600"></i>
                                        <span class="font-semibold text-green-800 text-sm">Đã điểm danh</span>
                                    </div>
                                    <p class="text-green-600 text-xs">
                                        {{ $reg->thoigiandiemdanh->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                @else
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <i class="fa-solid fa-circle-check text-green-600 text-lg mb-1"></i>
                                    <p class="font-semibold text-green-800 text-sm">Đã điểm danh</p>
                                </div>
                                @endif
                            @else
                                @php
                                    $isPast = $reg->hoatdong->thoigianketthuc < now();
                                    $isOngoing = $reg->hoatdong->thoigianbatdau <= now() && $reg->hoatdong->thoigianketthuc >= now();
                                    $isFuture = $reg->hoatdong->thoigianbatdau > now();
                                @endphp

                                @if($isPast)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-history text-gray-400 text-lg mb-1"></i>
                                        <p class="font-semibold text-gray-600 text-sm">Đã kết thúc</p>
                                        <p class="text-gray-500 text-xs">Chưa điểm danh</p>
                                    </div>
                                @elseif($isOngoing)
                                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-spinner fa-spin text-orange-500 text-lg mb-1"></i>
                                        <p class="font-semibold text-orange-800 text-sm">Đang diễn ra</p>
                                        <p class="text-orange-600 text-xs">Vui lòng điểm danh</p>
                                    </div>
                                @else
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-clock text-blue-500 text-lg mb-1"></i>
                                        <p class="font-semibold text-blue-800 text-sm">Chờ điểm danh</p>
                                        <p class="text-blue-600 text-xs">
                                            Còn {{ $reg->hoatdong->thoigianbatdau->diffForHumans() }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            @if(!$reg->diemdanhqr && $reg->hoatdong->thoigianbatdau > now())
                                @php
                                    $hoursUntilStart = now()->diffInHours($reg->hoatdong->thoigianbatdau, false);
                                    $canCancel = $hoursUntilStart >= 24;
                                @endphp

                                @if($canCancel)
                                    <form action="{{ route('client.account.cancel-registration', $reg->madangkyhoatdong) }}" 
                                          method="POST" 
                                          class="flex-1"
                                          onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-4 py-2 rounded-lg transition border border-red-200 text-sm">
                                            <i class="fa-solid fa-xmark"></i>
                                            Hủy đăng ký
                                        </button>
                                    </form>
                                @else
                                    <div class="flex-1 bg-gray-100 text-gray-400 font-semibold px-4 py-2 rounded-lg border border-gray-200 text-sm text-center cursor-not-allowed">
                                        <i class="fa-solid fa-lock"></i>
                                        Không thể hủy
                                    </div>
                                @endif
                            @endif
                            
                            <a href="{{ route('client.events.show', $reg->hoatdong->cuocthi->slug) }}" 
                               class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold px-4 py-2 rounded-lg transition border border-blue-200 text-center text-sm">
                                <i class="fa-solid fa-eye"></i>
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $registrations->links() }}
            </div>
        </div>
    @endif

    {{-- Action button --}}
    <div class="text-center mt-12">
        <a href="{{ route('client.events.index') }}" 
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md hover:shadow-xl transition">
            <i class="fa-solid fa-calendar-plus"></i>
            Khám phá thêm sự kiện
        </a>
    </div>
</section>

@endsection