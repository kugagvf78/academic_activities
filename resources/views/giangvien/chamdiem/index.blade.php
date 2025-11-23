@extends('layouts.client')
@section('title', 'Danh sách cuộc thi cần chấm')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-24 overflow-hidden">
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
                    <span class="text-white/90 text-sm">Chấm điểm thi</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    Danh sách cuộc thi cần chấm
                </h1>
                <p class="text-cyan-100">Chấm điểm bài thi của sinh viên theo cuộc thi</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $cuocthiList->total() }}</div>
                        <div class="text-sm text-cyan-100">Cuộc thi</div>
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
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
        <form method="GET" action="{{ route('giangvien.chamdiem.index') }}" class="grid lg:grid-cols-4 md:grid-cols-2 gap-4">
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm theo tên cuộc thi..." 
                    class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div>
                <select name="trangthai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Approved" {{ request('trangthai') == 'Approved' ? 'selected' : '' }}>Đã phê duyệt</option>
                    <option value="InProgress" {{ request('trangthai') == 'InProgress' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="Completed" {{ request('trangthai') == 'Completed' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-blue-700 hover:to-cyan-600 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>
                @if(request()->hasAny(['search', 'trangthai']))
                <a href="{{ route('giangvien.chamdiem.index') }}" 
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
                $tongBai = $cuocthi->tong_baithi ?? 0;
                $daCham = $cuocthi->da_cham ?? 0;
                $chuaCham = $cuocthi->chua_cham ?? 0;
                $progress = $tongBai > 0 ? ($daCham / $tongBai) * 100 : 0;
                
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
            @endphp
            
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 group">
                {{-- Header với gradient và trang trí --}}
                <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600 px-6 py-6">
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
                        
                        <div class="flex items-center gap-4 text-cyan-50 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-alt"></i>
                                <span>{{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('d/m/Y') }}</span>
                            </div>
                            <span class="text-cyan-200">→</span>
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-check"></i>
                                <span>{{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Thống kê với card nhỏ có màu sắc --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 text-center border border-gray-200 hover:shadow-md transition">
                            <div class="text-3xl font-black text-gray-800 mb-1">{{ $tongBai }}</div>
                            <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Tổng bài</div>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl p-4 text-center border border-emerald-200 hover:shadow-md transition">
                            <div class="text-3xl font-black text-emerald-700 mb-1">{{ $daCham }}</div>
                            <div class="text-xs font-medium text-emerald-700 uppercase tracking-wide">Đã chấm</div>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl p-4 text-center border border-orange-200 hover:shadow-md transition">
                            <div class="text-3xl font-black text-orange-700 mb-1">{{ $chuaCham }}</div>
                            <div class="text-xs font-medium text-orange-700 uppercase tracking-wide">Chưa chấm</div>
                        </div>
                    </div>

                    {{-- Progress section với thiết kế đẹp hơn --}}
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-5 border border-blue-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-gray-700">Tiến độ chấm điểm</span>
                            <span class="text-lg font-bold text-blue-600">{{ number_format($progress, 1) }}%</span>
                        </div>
                        
                        <div class="relative bg-gray-200 rounded-full h-4 overflow-hidden shadow-inner">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 via-green-500 to-emerald-600 h-full rounded-full transition-all duration-700 ease-out shadow-lg" 
                                style="width: {{ $progress }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-2.5">
                            <span class="text-xs font-medium text-gray-600">
                                Đã chấm <span class="font-bold text-emerald-600">{{ $daCham }}</span> / <span class="font-bold text-gray-700">{{ $tongBai }}</span> bài thi
                            </span>
                        </div>
                    </div>

                    {{-- Action button --}}
                    <a href="{{ route('giangvien.chamdiem.show-cuocthi', $cuocthi->macuocthi) }}" 
                        class="block w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-3.5 rounded-xl font-bold transition-all duration-300 text-center shadow-lg hover:shadow-xl group-hover:scale-[1.02] transform">
                        <div class="flex items-center justify-center gap-3">
                            <i class="fas fa-clipboard-list text-lg"></i>
                            <span>Xem danh sách bài thi</span>
                            <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </a>
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
                    <i class="fas fa-clipboard-check text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Không có cuộc thi nào</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'trangthai']))
                        Không tìm thấy cuộc thi nào phù hợp với bộ lọc của bạn.
                    @else
                        Hiện tại chưa có cuộc thi nào cần chấm điểm.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'trangthai']))
                    <a href="{{ route('giangvien.chamdiem.index') }}" 
                        class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
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