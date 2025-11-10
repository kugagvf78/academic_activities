@extends('layouts.client')
@section('title', 'Kết quả Cuộc thi Học thuật')

@section('content')
@php
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// Tạo dữ liệu giả cho 27 kết quả
$fakeResults = collect(range(1, 27))->map(function ($i) {
    return (object)[
        'id' => $i,
        'title' => "Database Design Challenge #$i",
        'date' => now()->subDays($i)->format('d/m/Y'),
        'winner' => 'Nguyễn Văn A',
        'image' => "https://source.unsplash.com/600x400/?trophy,competition,$i",
    ];
});

// Lấy trang hiện tại
$page = request()->get('page', 1);
$perPage = 6;

// Cắt dữ liệu theo trang
$itemsForCurrentPage = $fakeResults->slice(($page - 1) * $perPage, $perPage)->values();

// Tạo paginator giả
$results = new LengthAwarePaginator(
    $itemsForCurrentPage,
    $fakeResults->count(),
    $perPage,
    $page,
    ['path' => request()->url(), 'query' => request()->query()]
);
@endphp


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
        <form class="grid lg:grid-cols-4 md:grid-cols-3 grid-cols-1 gap-4 items-end">

            {{-- Tên cuộc thi --}}
            <div class="col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Tìm kiếm theo tên cuộc thi..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            {{-- Năm tổ chức --}}
            <x-form.select
                name="year"
                :options="[
                    '2025' => 'Năm 2025',
                    '2024' => 'Năm 2024',
                    '2023' => 'Năm 2023',
                ]"
                placeholder="Tất cả năm" />

            {{-- Trạng thái --}}
            <x-form.select
                name="type"
                :options="[
                    'individual' => 'Thi cá nhân',
                    'team' => 'Thi nhóm',
                ]"
                placeholder="Hình thức thi" />

            {{-- Nút lọc --}}
            <x-ui.button type="submit" label="Lọc" icon="fa-filter" color="blue" />
        </form>
    </div>
</section>


{{-- 🏆 RESULTS GRID --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
        @for ($i = 1; $i <= 6; $i++)
            <article class="group bg-white rounded-2xl shadow-md hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-500 hover:-translate-y-3">
                <div class="relative overflow-hidden h-56">
                    <img src="{{asset('images/home/banner1.png')}}"
                        alt="Kết quả {{ $i }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>

                    <div class="absolute top-4 left-4">
                        <span class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                            <i class="fas fa-trophy"></i> Giải thưởng
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="font-bold text-xl text-gray-800 group-hover:text-blue-600 transition mb-3 line-clamp-2">
                        Database Design Challenge {{ 2025 - $i }}
                    </h3>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        Cuộc thi học thuật về thiết kế cơ sở dữ liệu – nơi sinh viên thể hiện tư duy và sáng tạo trong lĩnh vực công nghệ thông tin.
                    </p>

                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-blue-500"></i>
                            <span>{{ rand(1,28) }}/12/{{ 2025 - $i }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-crown text-yellow-500"></i>
                            <span>Nguyễn Văn A</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-white text-xs font-bold">IT</div>
                            <span class="text-xs text-gray-600 font-medium">Khoa CNTT</span>
                        </div>

                        <a href="{{ route('client.results.show', 4) }}"
                            class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-xl transition-all inline-flex items-center gap-2">
                            <span>Xem chi tiết</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        @endfor
    </div>

    {{-- 📄 PAGINATION --}}
    <div class="mt-16 mx-5 text-center">
        <nav class="inline-flex items-center gap-2">
            <a href="#" class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">1</a>
            <a href="#" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600">2</a>
            <a href="#" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600">3</a>
            <span class="px-4 py-2 text-gray-400">...</span>
            <a href="#" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600">Tiếp</a>
        </nav>
    </div>
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
            <a href="#" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2 border border-white/30">
                <i class="fas fa-bell"></i> <span>Nhận thông báo kết quả</span>
            </a>
        </div>
    </div>
</section>

@endsection
