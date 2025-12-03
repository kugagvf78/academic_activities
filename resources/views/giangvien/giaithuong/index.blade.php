@extends('layouts.client')
@section('title', 'Quản lý Giải thưởng')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-yellow-600 to-orange-500 text-white py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.profile.index') }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Quản lý giải thưởng</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    <i class="fas fa-trophy mr-3"></i>Quản lý Giải thưởng
                </h1>
                <p class="text-yellow-100">
                    @if($laTruongBoMon)
                        Quản lý cơ cấu giải thưởng cho các cuộc thi của bộ môn
                    @else
                        Xem thông tin giải thưởng các cuộc thi
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $cuocthiList->total() }}</div>
                        <div class="text-sm text-yellow-100">Cuộc thi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- FILTER --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
        <form method="GET" action="{{ route('giangvien.giaithuong.index') }}" class="grid lg:grid-cols-5 md:grid-cols-2 gap-4">
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm theo tên cuộc thi..." 
                    class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
            </div>

            <div>
                <select name="trangthai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Approved" {{ request('trangthai') == 'Approved' ? 'selected' : '' }}>Đã phê duyệt</option>
                    <option value="InProgress" {{ request('trangthai') == 'InProgress' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="Completed" {{ request('trangthai') == 'Completed' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>

            <div>
                <select name="nam" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                    <option value="">Tất cả năm</option>
                    @foreach($namList as $nam)
                        <option value="{{ $nam }}" {{ request('nam') == $nam ? 'selected' : '' }}>{{ $nam }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-amber-600 to-orange-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-amber-700 hover:to-orange-600 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>
                @if(request()->hasAny(['search', 'trangthai', 'nam']))
                <a href="{{ route('giangvien.giaithuong.index') }}" 
                    class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                    <i class="fas fa-rotate-right"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</section>

{{-- DANH SÁCH CUỘC THI --}}
<section class="container mx-auto px-6 py-12">
    @if($cuocthiList->count() > 0)
        <div class="grid lg:grid-cols-2 gap-8">
            @foreach ($cuocthiList as $cuocthi)
            @php
                $statusConfig = [
                    'Approved' => [
                        'text' => 'Đã phê duyệt', 
                        'bg' => 'bg-blue-50',
                        'text_color' => 'text-blue-700',
                        'border' => 'border-blue-200',
                        'icon' => 'fa-check-circle'
                    ],
                    'InProgress' => [
                        'text' => 'Đang diễn ra', 
                        'bg' => 'bg-green-50',
                        'text_color' => 'text-green-700',
                        'border' => 'border-green-200',
                        'icon' => 'fa-play-circle'
                    ],
                    'Completed' => [
                        'text' => 'Đã kết thúc', 
                        'bg' => 'bg-gray-50',
                        'text_color' => 'text-gray-700',
                        'border' => 'border-gray-200',
                        'icon' => 'fa-flag-checkered'
                    ],
                ];
                $status = $statusConfig[$cuocthi->trangthai] ?? [
                    'text' => $cuocthi->trangthai, 
                    'bg' => 'bg-gray-50',
                    'text_color' => 'text-gray-700',
                    'border' => 'border-gray-200',
                    'icon' => 'fa-circle'
                ];

                $tongCoCau = $cuocthi->tong_cocau ?? 0;
                $tongSlot = $cuocthi->tong_slot ?? 0;
                $daGan = $cuocthi->da_gan ?? 0;
            @endphp
            
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 group">
                {{-- Header --}}
                <div class="relative bg-gradient-to-br from-amber-600 via-yellow-600 to-orange-600 px-6 py-6">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>
                    
                    <div class="relative">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <h3 class="text-white font-bold text-xl leading-tight flex-1">
                                {{ $cuocthi->tencuocthi }}
                            </h3>
                            <span class="{{$status['bg']}} {{$status['text_color']}} {{$status['border']}} border px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap flex items-center gap-1.5 shadow-sm">
                                <i class="fas {{$status['icon']}} text-xs"></i>
                                {{ $status['text'] }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4 text-yellow-50 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-alt"></i>
                                <span>{{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('d/m/Y') }}</span>
                            </div>
                            <span class="text-yellow-200">→</span>
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-check"></i>
                                <span>{{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Thống kê --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-4 text-center border border-amber-200 hover:shadow-md transition">
                            <div class="text-3xl font-black text-amber-700 mb-1">{{ $tongCoCau }}</div>
                            <div class="text-xs font-medium text-amber-700 uppercase tracking-wide">Loại giải</div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-100 rounded-xl p-4 text-center border border-blue-200 hover:shadow-md transition">
                            <div class="text-3xl font-black text-blue-700 mb-1">{{ $tongSlot }}</div>
                            <div class="text-xs font-medium text-blue-700 uppercase tracking-wide">Tổng slot</div>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl p-4 text-center border border-emerald-200 hover:shadow-md transition">
                            <div class="text-3xl font-black text-emerald-700 mb-1">{{ $daGan }}</div>
                            <div class="text-xs font-medium text-emerald-700 uppercase tracking-wide">Đã gán</div>
                        </div>
                    </div>

                    {{-- Thông tin bộ môn --}}
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex items-center gap-3 text-sm">
                            <i class="fas fa-building text-blue-600"></i>
                            <span class="font-semibold text-gray-700">Bộ môn:</span>
                            <span class="text-gray-600">{{ $cuocthi->bomon->tenbomon ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <a href="{{ route('giangvien.giaithuong.show', $cuocthi->macuocthi) }}" 
                            class="flex-1 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-6 py-3.5 rounded-xl font-bold transition-all duration-300 text-center shadow-lg hover:shadow-xl group-hover:scale-[1.02] transform">
                            <div class="flex items-center justify-center gap-3">
                                <i class="fas fa-trophy text-lg"></i>
                                <span>Xem cơ cấu giải</span>
                                <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>

                        @if($laTruongBoMon)
                        <a href="{{ route('giangvien.giaithuong.create', $cuocthi->macuocthi) }}" 
                            class="px-4 py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl"
                            title="Thêm giải thưởng">
                            <i class="fas fa-plus"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $cuocthiList->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-trophy text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Không có cuộc thi nào</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'trangthai', 'nam']))
                        Không tìm thấy cuộc thi nào phù hợp với bộ lọc của bạn.
                    @else
                        Hiện tại chưa có cuộc thi nào có cơ cấu giải thưởng.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'trangthai', 'nam']))
                    <a href="{{ route('giangvien.giaithuong.index') }}" 
                        class="bg-gradient-to-r from-amber-600 to-orange-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-rotate-right mr-2"></i>Xóa bộ lọc
                    </a>
                    @endif
                    <a href="{{ route('giangvien.profile.index') }}" 
                        class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border border-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    @endif
</section>

@endsection