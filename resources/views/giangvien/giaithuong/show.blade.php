@extends('layouts.client')
@section('title', 'Cơ cấu giải thưởng - ' . $cuocthi->tencuocthi)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-yellow-600 to-orange-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Cơ cấu giải thưởng</span>
        </div>
        
        <h1 class="text-4xl font-black mb-3">
            <i class="fas fa-trophy mr-3"></i>{{ $cuocthi->tencuocthi }}
        </h1>
        
        <div class="flex flex-wrap items-center gap-4 text-yellow-100">
            <div class="flex items-center gap-2">
                <i class="far fa-calendar-alt"></i>
                <span>{{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('d/m/Y') }}</span>
            </div>
            <span class="text-yellow-200">→</span>
            <div class="flex items-center gap-2">
                <i class="far fa-calendar-check"></i>
                <span>{{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('d/m/Y') }}</span>
            </div>
            <span class="text-yellow-200">|</span>
            <div class="flex items-center gap-2">
                <i class="fas fa-building"></i>
                <span>{{ $cuocthi->bomon->tenbomon ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- THỐNG KÊ TỔNG QUAN --}}
<section class="container mx-auto px-6 -mt-12 relative z-20 mb-8">
    <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-amber-100 to-yellow-100 p-3 rounded-xl">
                    <i class="fas fa-trophy text-2xl text-amber-600"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1">{{ $tongGiai }}</div>
            <div class="text-sm font-medium text-gray-600">Loại giải thưởng</div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-blue-100 to-cyan-100 p-3 rounded-xl">
                    <i class="fas fa-ticket-alt text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1">{{ $tongSlot }}</div>
            <div class="text-sm font-medium text-gray-600">Tổng slot giải</div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-emerald-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-emerald-100 to-green-100 p-3 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-emerald-600"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1">{{ $tongDaGan }}</div>
            <div class="text-sm font-medium text-gray-600">Đã gán giải</div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-3 rounded-xl">
                    <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-gray-800 mb-1">{{ number_format($tongTienThuong) }}</div>
            <div class="text-sm font-medium text-gray-600">Tổng tiền thưởng (VNĐ)</div>
        </div>
    </div>
</section>

{{-- ACTIONS --}}
@if($laTruongBoMon)
<section class="container mx-auto px-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Quản lý cơ cấu giải thưởng</h3>
                <p class="text-sm text-gray-600">Thêm mới hoặc chỉnh sửa các loại giải thưởng cho cuộc thi</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('giangvien.giaithuong.thongke', $cuocthi->macuocthi) }}" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-chart-bar mr-2"></i>Thống kê
                </a>
                <a href="{{ route('giangvien.giaithuong.create', $cuocthi->macuocthi) }}" 
                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>Thêm giải thưởng
                </a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- DANH SÁCH CƠ CẤU GIẢI THƯỞNG --}}
<section class="container mx-auto px-6 pb-12">
    @if($cocauList->count() > 0)
        <div class="space-y-6">
            @foreach ($cocauList as $index => $cocau)
            @php
                $percentage = $cocau->chophepdonghang ? 100 : ($cocau->soluong > 0 ? ($cocau->da_gan / $cocau->soluong) * 100 : 0);
                $colorClass = $percentage >= 100 ? 'emerald' : ($percentage >= 50 ? 'blue' : 'amber');
            @endphp
            
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-6 mb-6">
                        {{-- Left: Thông tin giải --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="bg-gradient-to-br from-amber-500 to-yellow-500 text-white w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl shadow-lg">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-black text-gray-800 mb-1">{{ $cocau->tengiai }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                        @if($cocau->chophepdonghang)
                                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-semibold border border-purple-200">
                                                <i class="fas fa-infinity mr-1"></i>Không giới hạn số lượng
                                            </span>
                                        @else
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold border border-blue-200">
                                                <i class="fas fa-users mr-1"></i>{{ $cocau->soluong }} slot
                                            </span>
                                        @endif
                                        
                                        @if($cocau->tienthuong > 0)
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold border border-green-200">
                                                <i class="fas fa-money-bill-wave mr-1"></i>{{ number_format($cocau->tienthuong) }} VNĐ
                                            </span>
                                        @endif
                                        
                                        @if($cocau->giaykhen)
                                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full font-semibold border border-amber-200">
                                                <i class="fas fa-certificate mr-1"></i>Giấy khen
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($cocau->ghichu)
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                <p class="text-sm text-gray-700">
                                    <i class="fas fa-info-circle text-gray-400 mr-2"></i>{{ $cocau->ghichu }}
                                </p>
                            </div>
                            @endif
                        </div>

                        {{-- Right: Thống kê --}}
                        <div class="text-right">
                            <div class="bg-gradient-to-br from-{{$colorClass}}-50 to-{{$colorClass}}-100 rounded-2xl p-6 border border-{{$colorClass}}-200 min-w-[200px]">
                                <div class="text-4xl font-black text-{{$colorClass}}-700 mb-2">
                                    {{ $cocau->da_gan }}
                                    @if(!$cocau->chophepdonghang)
                                        <span class="text-xl text-{{$colorClass}}-500">/ {{ $cocau->soluong }}</span>
                                    @endif
                                </div>
                                <div class="text-sm font-semibold text-{{$colorClass}}-700 uppercase tracking-wide">
                                    Đã gán
                                </div>
                                
                                @if(!$cocau->chophepdonghang)
                                <div class="mt-3 pt-3 border-t border-{{$colorClass}}-200">
                                    <div class="text-xs text-{{$colorClass}}-600 mb-1">Còn lại</div>
                                    <div class="text-2xl font-black text-{{$colorClass}}-700">{{ $cocau->con_lai }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Progress bar (chỉ hiển thị nếu không phải đồng hạng) --}}
                    @if(!$cocau->chophepdonghang)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Tiến độ gán giải</span>
                            <span class="text-sm font-bold text-{{$colorClass}}-600">{{ number_format($percentage, 1) }}%</span>
                        </div>
                        <div class="relative bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-{{$colorClass}}-400 to-{{$colorClass}}-600 h-full rounded-full transition-all duration-700 ease-out" 
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Chi tiết phân loại --}}
                    <div class="grid md:grid-cols-2 gap-3 mb-4">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center">
                            <div class="text-2xl font-black text-green-700 mb-1">{{ $cocau->approved ?? 0 }}</div>
                            <div class="text-xs font-semibold text-green-700 uppercase">Đã gán</div>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-center">
                            <div class="text-2xl font-black text-blue-700 mb-1">{{ $cocau->da_gan }}</div>
                            <div class="text-xs font-semibold text-blue-700 uppercase">Tổng cộng</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('giangvien.giaithuong.gangiai', $cocau->macocau) }}" 
                            class="flex-1 min-w-[180px] bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-5 py-3 rounded-xl font-semibold transition shadow-lg hover:shadow-xl text-center">
                            <i class="fas fa-list mr-2"></i>Xem danh sách ({{ $cocau->da_gan }})
                        </a>

                        @if($laTruongBoMon)
                        <a href="{{ route('giangvien.giaithuong.danh-sach-gan-giai', $cocau->macocau) }}" 
                            class="px-5 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-user-plus mr-2"></i>Gán giải
                        </a>

                        <a href="{{ route('giangvien.giaithuong.edit', $cocau->macocau) }}" 
                            class="px-5 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-edit mr-2"></i>Sửa
                        </a>

                        <form action="{{ route('giangvien.giaithuong.destroy', $cocau->macocau) }}" 
                            method="POST" 
                            class="inline-block"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa cơ cấu giải thưởng này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="px-5 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl"
                                {{ $cocau->da_gan > 0 ? 'disabled' : '' }}>
                                <i class="fas fa-trash mr-2"></i>Xóa
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-trophy text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có cơ cấu giải thưởng</h4>
                <p class="text-gray-500 mb-8">
                    Cuộc thi này chưa có cơ cấu giải thưởng nào được thiết lập.
                    @if($laTruongBoMon)
                        Hãy thêm giải thưởng để bắt đầu.
                    @endif
                </p>
                @if($laTruongBoMon)
                <a href="{{ route('giangvien.giaithuong.create', $cuocthi->macuocthi) }}" 
                    class="inline-block bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Thêm giải thưởng đầu tiên
                </a>
                @endif
            </div>
        </div>
    @endif
</section>

@endsection