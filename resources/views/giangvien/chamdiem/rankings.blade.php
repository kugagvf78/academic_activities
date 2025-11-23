@extends('layouts.client')
@section('title', 'Bảng xếp hạng - ' . ($cuocthi->tencuocthi ?? 'Cuộc thi'))

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-purple-700 via-purple-600 to-pink-500 text-white py-16 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.chamdiem.show-cuocthi', $cuocthi->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách bài thi
            </a>
        </div>
        <div class="flex items-center gap-4 mb-2">
            <i class="fas fa-trophy text-5xl"></i>
            <div>
                <h1 class="text-3xl font-black">Bảng xếp hạng</h1>
                <p class="text-purple-100">{{ $cuocthi->tencuocthi ?? 'Cuộc thi' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- STATS BAR --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-purple-100 p-6">
        <div class="grid md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $rankings->count() }}</div>
                <div class="text-sm text-gray-600">Thí sinh tham gia</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600">{{ $rankings->where('giaithuong', 'Giải Nhất')->count() }}</div>
                <div class="text-sm text-gray-600">Giải Nhất</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-600">{{ $rankings->where('giaithuong', 'Giải Nhì')->count() }}</div>
                <div class="text-sm text-gray-600">Giải Nhì</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">{{ $rankings->where('giaithuong', 'Giải Ba')->count() }}</div>
                <div class="text-sm text-gray-600">Giải Ba</div>
            </div>
        </div>
    </div>
</section>

{{-- BẢNG XẾP HẠNG --}}
<section class="container mx-auto px-6 py-12">
    @if($rankings->count() > 0)
        {{-- TOP 3 --}}
        @php
            $top3 = $rankings->whereIn('xephang', [1, 2, 3])->sortBy('xephang');
        @endphp
        
        @if($top3->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🏆 Top 3 Xuất Sắc</h2>
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @foreach($top3 as $item)
                    @php
                        $podiumConfig = [
                            1 => ['height' => 'h-64', 'bg' => 'from-yellow-400 to-yellow-600', 'icon' => 'fa-crown', 'position' => 'top-0'],
                            2 => ['height' => 'h-56', 'bg' => 'from-gray-300 to-gray-500', 'icon' => 'fa-medal', 'position' => 'top-8'],
                            3 => ['height' => 'h-48', 'bg' => 'from-orange-400 to-orange-600', 'icon' => 'fa-medal', 'position' => 'top-16'],
                        ];
                        $config = $podiumConfig[$item->xephang] ?? $podiumConfig[3];
                        
                        $tenThiSinh = $item->loaidangky == 'CaNhan' ? $item->ten_sinhvien : $item->tendoithi;
                        $maThiSinh = $item->loaidangky == 'CaNhan' ? $item->masinhvien : $item->madoithi;
                    @endphp
                    
                    <div class="relative {{ $config['position'] }}">
                        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border-4 border-{{ $item->xephang == 1 ? 'yellow' : ($item->xephang == 2 ? 'gray' : 'orange') }}-400">
                            {{-- Rank Badge --}}
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                                <div class="w-16 h-16 bg-gradient-to-br {{ $config['bg'] }} rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white">
                                    <div class="text-center">
                                        <i class="fas {{ $config['icon'] }} text-xl"></i>
                                        <div class="text-xs font-bold">#{{ $item->xephang }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Content --}}
                            <div class="pt-16 pb-6 px-6 text-center">
                                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br {{ $config['bg'] }} rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                                    @if($item->loaidangky == 'CaNhan')
                                        {{ strtoupper(substr($tenThiSinh ?? 'SV', 0, 2)) }}
                                    @else
                                        <i class="fas fa-users"></i>
                                    @endif
                                </div>
                                <h3 class="font-bold text-lg text-gray-800 mb-1">{{ $tenThiSinh }}</h3>
                                <p class="text-sm text-gray-500 mb-4">{{ $maThiSinh }}</p>
                                
                                <div class="bg-gradient-to-br {{ $config['bg'] }} text-white rounded-xl py-3 px-4 mb-3">
                                    <div class="text-3xl font-black">{{ $item->diem }}</div>
                                    <div class="text-xs opacity-90">điểm</div>
                                </div>
                                
                                <div class="bg-{{ $item->xephang == 1 ? 'yellow' : ($item->xephang == 2 ? 'gray' : 'orange') }}-50 rounded-lg py-2 px-3">
                                    <div class="font-bold text-{{ $item->xephang == 1 ? 'yellow' : ($item->xephang == 2 ? 'gray' : 'orange') }}-700">
                                        {{ $item->giaithuong }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- BẢNG ĐẦY ĐỦ --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-800">
                    <i class="fas fa-list-ol mr-2"></i>Bảng xếp hạng đầy đủ
                </h3>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($rankings as $index => $item)
                    @php
                        $tenThiSinh = $item->loaidangky == 'CaNhan' ? $item->ten_sinhvien : $item->tendoithi;
                        $maThiSinh = $item->loaidangky == 'CaNhan' ? $item->masinhvien : $item->madoithi;
                        
                        $rankBgColor = match($item->xephang) {
                            1 => 'bg-yellow-50 border-l-4 border-yellow-500',
                            2 => 'bg-gray-50 border-l-4 border-gray-500',
                            3 => 'bg-orange-50 border-l-4 border-orange-500',
                            default => 'hover:bg-purple-50/30'
                        };
                    @endphp
                    
                    <div class="px-6 py-4 transition {{ $rankBgColor }}">
                        <div class="flex items-center gap-4">
                            {{-- Hạng --}}
                            <div class="w-16 flex-shrink-0">
                                @if($item->xephang <= 3)
                                    <div class="w-12 h-12 bg-gradient-to-br 
                                        {{ $item->xephang == 1 ? 'from-yellow-400 to-yellow-600' : '' }}
                                        {{ $item->xephang == 2 ? 'from-gray-300 to-gray-500' : '' }}
                                        {{ $item->xephang == 3 ? 'from-orange-400 to-orange-600' : '' }}
                                        rounded-full flex items-center justify-center text-white font-bold text-xl shadow-md">
                                        {{ $item->xephang }}
                                    </div>
                                @else
                                    <div class="text-2xl font-bold text-gray-400 text-center">
                                        {{ $item->xephang }}
                                    </div>
                                @endif
                            </div>

                            {{-- Thông tin --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                        @if($item->loaidangky == 'CaNhan')
                                            {{ strtoupper(substr($tenThiSinh ?? 'SV', 0, 2)) }}
                                        @else
                                            <i class="fas fa-users"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-gray-800 truncate">{{ $tenThiSinh }}</div>
                                        <div class="text-sm text-gray-500">{{ $maThiSinh }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Điểm --}}
                            <div class="text-right mr-6">
                                <div class="text-2xl font-black text-purple-600">{{ $item->diem }}</div>
                                <div class="text-xs text-gray-500">điểm</div>
                            </div>

                            {{-- Giải thưởng --}}
                            <div class="w-40 flex-shrink-0">
                                @if($item->giaithuong)
                                    @php
                                        $awardConfig = [
                                            'Giải Nhất' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-trophy'],
                                            'Giải Nhì' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-medal'],
                                            'Giải Ba' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'fa-medal'],
                                            'Giải Khuyến Khích' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-award'],
                                        ];
                                        $award = $awardConfig[$item->giaithuong] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-award'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 {{ $award['bg'] }} {{ $award['text'] }} px-3 py-2 rounded-lg font-semibold">
                                        <i class="fas {{ $award['icon'] }}"></i>
                                        <span>{{ $item->giaithuong }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <i class="fas fa-trophy text-8xl text-gray-300 mb-4"></i>
            <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có kết quả</h4>
            <p class="text-gray-500">Cuộc thi chưa có bài thi nào được chấm điểm.</p>
        </div>
    @endif
</section>

@endsection