@extends('layouts.client')
@section('title', 'Kết quả Cuộc thi Học thuật')

@section('content')

{{-- 🏆 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-24 overflow-hidden">
    {{-- Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    {{-- Floating shapes --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-300/20 rounded-full blur-3xl animate-pulse delay-700"></div>

    <div class="container mx-auto px-6 relative z-10 text-center">
        <div class="max-w-3xl mx-auto">
            {{-- Badge --}}
            <div class="inline-block mb-6">
                <span class="bg-white/15 text-white px-4 py-2 rounded-full text-sm font-semibold backdrop-blur-sm border border-white/20">
                    <i class="fas fa-medal mr-2 text-yellow-300"></i>Kết quả Cuộc thi Học thuật
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-5xl md:text-6xl font-black mb-6 tracking-tight">
                Vinh danh
                <span class="bg-gradient-to-r from-yellow-200 via-white to-yellow-100 bg-clip-text text-transparent">
                    Tài năng & Thành tích
                </span>
            </h1>

            <p class="text-lg md:text-xl text-blue-100 leading-relaxed max-w-2xl mx-auto mb-8">
                Tổng hợp kết quả, giải thưởng và những gương mặt xuất sắc nhất trong các cuộc thi học thuật Khoa CNTT.
            </p>
        </div>
    </div>

    {{-- Wave divider --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white" />
        </svg>
    </div>
</section>


{{-- 🔍 FILTER BAR --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
        <form method="GET" action="{{ route('client.results.index') }}" class="grid lg:grid-cols-4 md:grid-cols-3 grid-cols-1 gap-4 items-end">

            {{-- Tên cuộc thi --}}
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tên cuộc thi..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            {{-- Năm tổ chức --}}
            <div>
                <select name="year" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Tất cả năm</option>
                    @if(isset($years))
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                Năm {{ $year }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Hình thức --}}
            <div>
                <select name="type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Hình thức thi</option>
                    <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>Thi cá nhân</option>
                    <option value="team" {{ request('type') == 'team' ? 'selected' : '' }}>Thi nhóm</option>
                </select>
            </div>

            {{-- Nút lọc & reset --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold transition-all inline-flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Lọc</span>
                </button>

                @if(request()->hasAny(['search', 'year', 'type']))
                <a href="{{ route('client.results.index') }}"
                    class="text-blue-600 text-lg hover:text-blue-700 font-medium">
                    <i class="fa-solid fa-rotate"></i>
                </a>
                @endif
            </div>

        </form>
    </div>
</section>


{{-- 🏆 RESULTS GRID --}}
<section class="container mx-auto px-6 py-16">
    @if($results->count() > 0)
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
        @foreach ($results as $item)
        <article class="group bg-white rounded-2xl shadow-md hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-500 hover:-translate-y-3">
            <div class="relative overflow-hidden h-56">
                <img src="{{asset('images/home/banner1.png')}}" alt="Kết quả {{ $item->tencuocthi }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
                <div class="absolute top-4 left-4">
                    <span class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                        <i class="fas fa-trophy"></i> 
                        {{ $item->soluonggiai ?? 0 }} giải thưởng
                    </span>
                </div>
                
                {{-- Loại cuộc thi --}}
                @if($item->loaicuocthi)
                <div class="absolute top-4 right-4">
                    <span class="bg-white/90 backdrop-blur-sm text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $item->loaicuocthi }}
                    </span>
                </div>
                @endif
            </div>

            <div class="p-6">
                <h3 class="font-bold text-xl text-gray-800 group-hover:text-blue-600 transition mb-3 line-clamp-2">
                    {{ $item->tencuocthi }}
                </h3>

                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    Cuộc thi học thuật về {{ strtolower($item->loaicuocthi ?? 'công nghệ thông tin') }} — nơi sinh viên thể hiện tư duy và sáng tạo trong lĩnh vực công nghệ.
                </p>

                <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-blue-500"></i>
                        <span>{{ $item->date }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users text-purple-500"></i>
                        <span>{{ $item->soluongthamgia ?? 0 }}+ thí sinh</span>
                    </div>
                </div>

                {{-- Winner info --}}
                @if($item->winner && $item->winner !== 'Chưa công bố')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-crown text-yellow-500"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">Giải Nhất</p>
                            <p class="font-semibold text-gray-800 text-sm line-clamp-1">{{ $item->winner }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ substr($item->tenbomon ?? 'IT', 0, 2) }}
                        </div>
                        <span class="text-xs text-gray-600 font-medium line-clamp-1">{{ $item->tenbomon ?? 'Khoa CNTT' }}</span>
                    </div>

                    <a href="{{ route('client.results.show', $item->macuocthi) }}"
                        class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-xl transition-all inline-flex items-center gap-2">
                        <span>Xem chi tiết</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    {{-- 📄 PAGINATION --}}
    @if($results->hasPages())
    <div class="mt-16">
        {{ $results->links() }}
    </div>
    @endif

    @else
    {{-- EMPTY STATE --}}
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
        <div class="max-w-md mx-auto">
            <div class="mb-6 relative">
                <div class="absolute inset-0 bg-blue-100 rounded-full blur-2xl opacity-50"></div>
                <i class="fas fa-trophy text-8xl text-gray-300 relative"></i>
            </div>
            <h4 class="text-2xl font-bold text-gray-700 mb-3">Không có kết quả nào</h4>
            <p class="text-gray-500 mb-8 leading-relaxed">
                @if(request('search') || request('year') || request('type'))
                Không có cuộc thi nào phù hợp với bộ lọc của bạn.<br>
                Hãy thử điều chỉnh tiêu chí tìm kiếm.
                @else
                Hiện tại chưa có cuộc thi nào được công bố kết quả.<br>
                Hãy quay lại sau hoặc theo dõi fanpage của khoa.
                @endif
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('client.results.index') }}"
                    class="inline-flex items-center bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                    <i class="fas fa-rotate-right mr-2"></i>Làm mới trang
                </a>
                <a href="{{ route('client.events.index') }}"
                    class="inline-flex items-center bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border border-gray-200">
                    <i class="fas fa-calendar mr-2"></i>Xem cuộc thi
                </a>
            </div>
        </div>
    </div>
    @endif

</section>


{{-- 💡 CTA --}}
<section class="bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-500 py-16 text-white text-center">
    <div class="container mx-auto px-6">
        <h3 class="text-3xl font-bold mb-4">Bạn đã sẵn sàng cho mùa thi mới?</h3>
        <p class="text-blue-100 mb-8 text-lg">Theo dõi ngay để không bỏ lỡ các cuộc thi học thuật sắp tới!</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('client.events.index') }}" class="bg-white text-blue-700 px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2">
                <i class="fas fa-rocket"></i> <span>Khám phá cuộc thi</span>
            </a>
            <a href="{{ route('client.home') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2 border border-white/30">
                <i class="fas fa-home"></i> <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</section>

@endsection