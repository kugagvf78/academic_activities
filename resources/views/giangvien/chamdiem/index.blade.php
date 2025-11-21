@extends('layouts.client')
@section('title', 'Danh sách bài cần chấm')

@section('content')
{{-- HERO SECTION - ĐÃ ĐẸP --}}
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
                    Danh sách bài cần chấm
                </h1>
                <p class="text-cyan-100">Chấm điểm bài thi của sinh viên</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $ketquaList->total() }}</div>
                        <div class="text-sm text-cyan-100">Bài chưa chấm</div>
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

{{-- FILTER & STATS --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
        <form method="GET" action="{{ route('giangvien.chamdiem.index') }}" class="grid lg:grid-cols-4 md:grid-cols-2 gap-4">
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm theo mã sinh viên, tên sinh viên..." 
                    class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div>
                <select name="cuocthi" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Tất cả cuộc thi</option>
                    @foreach($ketquaList->unique('baithi.dethi.cuocthi.macuocthi') as $item)
                        @if($item->baithi && $item->baithi->dethi && $item->baithi->dethi->cuocthi)
                        <option value="{{ $item->baithi->dethi->cuocthi->macuocthi }}" 
                            {{ request('cuocthi') == $item->baithi->dethi->cuocthi->macuocthi ? 'selected' : '' }}>
                            {{ $item->baithi->dethi->cuocthi->tencuocthi }}
                        </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-blue-700 hover:to-cyan-600 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>
                @if(request()->hasAny(['search', 'cuocthi']))
                <a href="{{ route('giangvien.chamdiem.index') }}" 
                    class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                    <i class="fas fa-rotate-right"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</section>

{{-- DANH SÁCH BÀI THI --}}
<section class="container mx-auto px-6 py-12">
    @if($ketquaList->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            {{-- Header - đổi từ tím sang xanh dương nhạt --}}
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
                <div class="grid lg:grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                    <div class="lg:col-span-1">STT</div>
                    <div class="lg:col-span-2">Sinh viên</div>
                    <div class="lg:col-span-3">Cuộc thi</div>
                    <div class="lg:col-span-2">Đề thi</div>
                    <div class="lg:col-span-2">Thời gian nộp</div>
                    <div class="lg:col-span-2 text-center">Thao tác</div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($ketquaList as $index => $ketqua)
                <div class="px-6 py-4 hover:bg-blue-50/50 transition group">
                    <div class="grid lg:grid-cols-12 gap-4 items-center">
                        <div class="lg:col-span-1">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full font-semibold text-sm">
                                {{ $ketquaList->firstItem() + $index }}
                            </span>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                    {{ strtoupper(substr($ketqua->baithi->sinhvien->nguoiDung->hoten ?? 'SV', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $ketqua->baithi->sinhvien->nguoiDung->hoten ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $ketqua->baithi->sinhvien->masinhvien ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-3">
                            <div class="font-medium text-gray-800 line-clamp-1">
                                {{ $ketqua->baithi->dethi->cuocthi->tencuocthi ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-calendar text-blue-500 mr-1"></i>
                                {{ $ketqua->baithi->dethi->cuocthi->thoigianbatdau ? \Carbon\Carbon::parse($ketqua->baithi->dethi->cuocthi->thoigianbatdau)->format('d/m/Y') : 'N/A' }}
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="text-gray-700">{{ $ketqua->baithi->dethi->tendethi ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500 mt-1">
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium">
                                    {{ $ketqua->baithi->dethi->loaidethi ?? 'Tự luận' }}
                                </span>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="text-gray-700">
                                <i class="far fa-clock text-gray-400 mr-1"></i>
                                {{ $ketqua->created_at ? \Carbon\Carbon::parse($ketqua->created_at)->format('H:i d/m/Y') : 'N/A' }}
                            </div>
                        </div>

                        <div class="lg:col-span-2 flex justify-center gap-2">
                            <a href="{{ route('giangvien.chamdiem.show', $ketqua->maketqua) }}" 
                                class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2 shadow-md hover:shadow-lg transform hover:scale-105">
                                <i class="fas fa-pen"></i>
                                <span>Chấm điểm</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            {{ $ketquaList->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-clipboard-check text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Không có bài thi nào cần chấm</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'cuocthi']))
                        Không tìm thấy bài thi nào phù hợp với bộ lọc của bạn.
                    @else
                        Hiện tại chưa có bài thi nào cần chấm điểm.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'cuocthi']))
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