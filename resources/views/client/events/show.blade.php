@extends('layouts.client')
@section('title', $event->tencuocthi)

@section('content')

{{-- 🎓 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-24 overflow-hidden">
    {{-- Subtle pattern overlay --}}
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: linear-gradient(30deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(150deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(30deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(150deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff); background-size: 80px 140px; background-position: 0 0, 0 0, 40px 70px, 40px 70px;"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl">
            {{-- Status badge --}}
            <div class="inline-flex items-center gap-2 bg-{{ $event->status_color }}-500/20 border-2 border-{{ $event->status_color }}-500 text-{{ $event->status_color }}-500 px-4 py-2 rounded-3xl text-sm font-semibold mb-6">
                <span class="w-2 h-2 bg-{{ $event->status_color }}-400 rounded-full animate-pulse"></span>
                {{ $event->status_label }}
            </div>

            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                {{ $event->tencuocthi }}
            </h1>

            {{-- Description --}}
            <p class="text-xl text-blue-100 leading-relaxed mb-8">
                {{ $event->mota ?? $event->mucdich ?? 'Cuộc thi học thuật dành cho sinh viên Khoa Công nghệ Thông tin' }}
            </p>

            {{-- Meta info --}}
            <div class="flex flex-wrap gap-6 mb-8 text-blue-100">
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar"></i>
                    <span>{{ \Carbon\Carbon::parse($event->thoigianbatdau)->format('d/m/Y') }}</span>
                </div>
                @if($event->thoigianbatdau && $event->thoigianketthuc)
                <div class="flex items-center gap-2">
                    <i class="far fa-clock"></i>
                    <span>{{ \Carbon\Carbon::parse($event->thoigianbatdau)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->thoigianketthuc)->format('H:i') }}</span>
                </div>
                @endif
                @if($event->diadiem)
                <div class="flex items-center gap-2">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $event->diadiem }}</span>
                </div>
                @endif
                <div class="flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    <span>{{ $event->soluongdangky ?? 0 }}+ sinh viên đăng ký</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-wrap gap-4">
                @if($event->can_register)
                    {{-- Đăng ký dự thi - FIXED: Dùng macuocthi --}}
                    <a href="{{ route('client.events.register', $event->macuocthi) }}"
                        class="bg-white text-blue-900 px-8 py-3.5 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:bg-blue-50 transition inline-flex items-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Đăng ký dự thi</span>
                    </a>

                    {{-- Đăng ký cổ vũ - Dùng slug --}}
                    <a href="{{ route('client.events.cheer', $event->slug) }}"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-white/20 transition inline-flex items-center gap-2">
                        <i class="fas fa-hands-clapping"></i>
                        <span>Đăng ký cổ vũ</span>
                    </a>

                    {{-- Đăng ký hỗ trợ - FIXED: Dùng macuocthi --}}
                    <a href="{{ route('client.events.support', $event->macuocthi) }}"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-white/20 transition inline-flex items-center gap-2">
                        <i class="fas fa-people-carry-box"></i>
                        <span>Đăng ký hỗ trợ</span>
                    </a>
                @else
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-3.5 rounded-lg font-semibold inline-flex items-center gap-2">
                        <i class="fas fa-lock"></i>
                        <span>Cuộc thi không nhận đăng ký</span>
                    </div>
                @endif

                <button onclick="window.print()" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-4 py-3.5 rounded-lg font-semibold hover:bg-white/20 transition">
                    <i class="fas fa-share-nodes"></i>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- 📋 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-3 gap-10">

        {{-- Left column - Main content --}}
        <div class="lg:col-span-2 space-y-12">

            {{-- Giới thiệu --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Giới thiệu chung</h2>
                </div>
                <div class="prose prose-blue max-w-none">
                    @if($event->mota)
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $event->mota }}</p>
                    @endif
                    
                    @if($event->mucdich && $event->mucdich != $event->mota)
                        <p class="text-gray-700 leading-relaxed mt-4 whitespace-pre-line">{{ $event->mucdich }}</p>
                    @endif

                    @if(!$event->mota && !$event->mucdich)
                        <p class="text-gray-500 italic">Chưa có thông tin giới thiệu.</p>
                    @endif
                </div>
            </article>

            {{-- Mục tiêu & Yêu cầu --}}
            @if($event->doituongthamgia || $event->mucdich)
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullseye text-emerald-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Đối tượng & Yêu cầu</h2>
                </div>
                
                @if($event->doituongthamgia)
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Đối tượng tham gia:</h3>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $event->doituongthamgia }}</p>
                </div>
                @endif

                @if($event->hinhthucthamgia)
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Hình thức tham gia:</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $event->hinhthucthamgia }}</p>
                </div>
                @endif

                @if($event->soluongthanhvien)
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Số lượng thành viên:</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $event->soluongthanhvien }} người/đội</p>
                </div>
                @endif
            </article>
            @endif

            {{-- Thời gian & Địa điểm --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-amber-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Thời gian & Địa điểm</h2>
                </div>

                <div class="space-y-4">
                    <div class="bg-blue-50 border-l-4 border-blue-600 p-5 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-clock text-blue-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Thời gian tổ chức</p>
                                <p class="text-gray-700 text-sm">
                                    Từ {{ \Carbon\Carbon::parse($event->thoigianbatdau)->format('H:i, d/m/Y') }}
                                    đến {{ \Carbon\Carbon::parse($event->thoigianketthuc)->format('H:i, d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($event->diadiem)
                    <div class="bg-purple-50 border-l-4 border-purple-600 p-5 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Địa điểm</p>
                                <p class="text-gray-700 text-sm">{{ $event->diadiem }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($event->doituongthamgia)
                    <div class="bg-gray-50 border-l-4 border-gray-400 p-5 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user-graduate text-gray-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Đối tượng tham gia</p>
                                <p class="text-gray-700 text-sm">{{ $event->doituongthamgia }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </article>

            {{-- Cấu trúc cuộc thi - Vòng thi --}}
            @if($vongthi && $vongthi->count() > 0)
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-layer-group text-indigo-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Cấu trúc cuộc thi</h2>
                </div>

                <div class="space-y-5">
                    @foreach($vongthi as $index => $vong)
                    @php
                        $colors = ['blue', 'purple', 'emerald', 'amber', 'rose'];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-{{ $color }}-300 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-{{ $color }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-{{ $color }}-600 font-bold">{{ str_pad($vong->thutu ?? ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $vong->tenvongthi }}</h3>
                                
                                {{-- Kiểm tra các cột có thể có trong bảng vongthi --}}
                                @if(isset($vong->mota) && $vong->mota)
                                <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ $vong->mota }}</p>
                                @elseif(isset($vong->noidung) && $vong->noidung)
                                <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ $vong->noidung }}</p>
                                @endif
                                
                                <div class="flex flex-wrap gap-2">
                                    @if(isset($vong->thoigianbatdau) && $vong->thoigianbatdau)
                                    <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full">
                                        <i class="far fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($vong->thoigianbatdau)->format('d/m/Y H:i') }}
                                    </span>
                                    @endif
                                    @if(isset($vong->thoigianketthuc) && $vong->thoigianketthuc)
                                    <span class="bg-green-50 text-green-700 text-xs px-3 py-1 rounded-full">
                                        <i class="far fa-calendar-check mr-1"></i>{{ \Carbon\Carbon::parse($vong->thoigianketthuc)->format('d/m/Y H:i') }}
                                    </span>
                                    @endif
                                    @if(isset($vong->diadiem) && $vong->diadiem)
                                    <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1 rounded-full">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $vong->diadiem }}
                                    </span>
                                    @endif
                                    @if(isset($vong->hinhthuc) && $vong->hinhthuc)
                                    <span class="bg-cyan-50 text-cyan-700 text-xs px-3 py-1 rounded-full">
                                        <i class="fas fa-file-alt mr-1"></i>{{ $vong->hinhthuc }}
                                    </span>
                                    @endif
                                    <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">
                                        <i class="fas fa-list-ol mr-1"></i>Vòng {{ $vong->thutu ?? ($index + 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </article>
            @endif

            {{-- Giải thưởng --}}
            @if($event->dutrukinhphi)
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-award text-yellow-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Giải thưởng</h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-lg">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                            <i class="fas fa-trophy text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-800 text-lg">Tổng giá trị giải thưởng</p>
                            <p class="text-gray-600 text-sm">Dự kiến phân bổ cho các giải</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-amber-600 text-xl">{{ number_format($event->dutrukinhphi) }}đ</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-sm text-gray-700 flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <span>Tất cả thí sinh vào vòng chung kết đều nhận Giấy chứng nhận tham gia.</span>
                    </p>
                </div>
            </article>
            @endif

            {{-- Kế hoạch cuộc thi --}}
            @if($kehoach)
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-cyan-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Kế hoạch tổ chức</h2>
                </div>

                @if(isset($kehoach->mota) && $kehoach->mota)
                <div class="prose prose-blue max-w-none mb-4">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $kehoach->mota }}</p>
                </div>
                @endif

                @if(isset($kehoach->trangthai) && $kehoach->trangthai)
                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg
                    {{ $kehoach->trangthai == 'Approved' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                    <i class="fas fa-{{ $kehoach->trangthai == 'Approved' ? 'check-circle' : 'clock' }}"></i>
                    <span class="font-semibold">{{ $kehoach->trangthai == 'Approved' ? 'Đã duyệt' : 'Chờ duyệt' }}</span>
                </div>
                @else
                <p class="text-gray-500 italic text-sm">Kế hoạch đang được xây dựng</p>
                @endif
            </article>
            @endif

            {{-- Ban tổ chức --}}
            @if($bantochuc && $bantochuc->count() > 0)
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-teal-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Ban tổ chức</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($bantochuc as $ban)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $ban->tenban }}</p>
                                @if(isset($ban->motaban) && $ban->motaban)
                                <p class="text-sm text-gray-600 mt-1">{{ $ban->motaban }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-user-group mr-1"></i>{{ $ban->sothanhvien ?? 0 }} thành viên
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </article>
            @endif

        </div>

        {{-- Right column - Sidebar --}}
        <aside class="space-y-6">

            {{-- Quick info card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-28">
                <h3 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Thông tin nhanh</h3>

                <ul class="space-y-4 mb-6">
                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="far fa-calendar text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Ngày tổ chức</p>
                            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($event->thoigianbatdau)->format('d/m/Y') }}</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="far fa-clock text-emerald-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Thời gian</p>
                            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($event->thoigianbatdau)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->thoigianketthuc)->format('H:i') }}</p>
                        </div>
                    </li>

                    @if($event->diadiem)
                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-amber-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Địa điểm</p>
                            <p class="font-semibold text-gray-800">{{ $event->diadiem }}</p>
                        </div>
                    </li>
                    @endif

                    @if($event->tenbomon)
                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Đơn vị tổ chức</p>
                            <p class="font-semibold text-gray-800">{{ $event->tenbomon }}</p>
                        </div>
                    </li>
                    @endif

                    @if($event->doituongthamgia)
                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-indigo-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Đối tượng</p>
                            <p class="font-semibold text-gray-800 line-clamp-2">{{ $event->doituongthamgia }}</p>
                        </div>
                    </li>
                    @endif

                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-check text-rose-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Đã đăng ký</p>
                            <p class="font-semibold text-gray-800">{{ $event->soluongdangky ?? 0 }} sinh viên</p>
                        </div>
                    </li>

                    @if($event->soluongdoi)
                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-cyan-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-group text-cyan-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Số đội thi</p>
                            <p class="font-semibold text-gray-800">{{ $event->soluongdoi }} đội</p>
                        </div>
                    </li>
                    @endif
                </ul>

                <div class="pt-5 border-t border-gray-100 space-y-3">
                    @if($event->can_register)
                    {{-- FIXED: Dùng macuocthi --}}
                    <a href="{{ route('client.events.register', $event->macuocthi) }}" 
                        class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold shadow-sm hover:shadow transition">
                        Đăng ký ngay
                    </a>
                    @else
                    <div class="w-full block text-center bg-gray-300 text-gray-600 py-3 rounded-lg font-semibold cursor-not-allowed">
                        Không nhận đăng ký
                    </div>
                    @endif
                    <a href="{{ route('client.events.index') }}" 
                        class="w-full block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition">
                        Xem cuộc thi khác
                    </a>
                </div>
            </div>

        </aside>
    </div>
</section>

@endsection