@extends('layouts.client')
@section('title', 'Kết quả Cuộc thi')

@section('content')

{{-- 🏆 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">

    {{-- Decorative glow circles --}}
    <div class="absolute -top-20 -left-20 w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>

    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight drop-shadow-md">
            {{ $result->title }}
        </h1>
        <p class="text-blue-100 text-lg mb-2 flex justify-center items-center gap-2">
            <i class="fa-regular fa-calendar text-white/90"></i>
            <span>Ngày tổ chức: {{ $result->date }}</span>
        </p>
        <p class="text-blue-100 text-sm italic mt-2">
            Vinh danh những gương mặt xuất sắc nhất trong lĩnh vực học thuật Khoa CNTT
        </p>
    </div>

    {{-- Wave transition --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z" />
        </svg>
    </div>
</section>

{{-- 🧾 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-3 gap-10">

        {{-- LEFT: Main content --}}
        <div class="lg:col-span-2 space-y-10">
            {{-- 🗂 Tổng quan --}}
            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-500 text-white rounded-lg flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-info-circle"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Tổng quan Cuộc thi</h2>
                </div>
                <p class="text-gray-700 leading-relaxed">
                    Cuộc thi học thuật đã thu hút hơn <strong>150 sinh viên</strong> đến từ các ngành CNTT, Khoa học dữ liệu, và An toàn thông tin.
                    Đây là sân chơi giúp sinh viên thể hiện tư duy phân tích, năng lực giải quyết vấn đề, và tinh thần sáng tạo trong công nghệ.
                </p>
                <p class="text-gray-700 mt-4 leading-relaxed">
                    Dưới đây là kết quả chi tiết của từng vòng thi và bảng xếp hạng chung cuộc.
                </p>
            </article>

            {{-- 📊 Kết quả từng vòng --}}
            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-500 text-white rounded-lg flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Kết quả từng vòng</h2>
                </div>
                <div class="space-y-4">
                    @foreach($result->rounds as $round)
                    <div class="border border-gray-100 p-5 rounded-xl flex items-center justify-between bg-gray-50 hover:bg-blue-50 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-circle-check text-blue-600"></i>
                            <span class="font-semibold text-gray-800">{{ $round['name'] }}</span>
                        </div>
                        <span class="text-sm text-gray-700"><i class="fa-solid fa-trophy text-yellow-500 mr-1"></i>{{ $round['winner'] }}</span>
                    </div>
                    @endforeach
                </div>
            </article>

            {{-- 🏅 Top 3 --}}
            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 text-white rounded-lg flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Top 3 Chung cuộc</h2>
                </div>

                <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 text-center">
                    @foreach($result->top3 as $t)
                    <div class="relative bg-gradient-to-b from-white to-blue-50 border border-gray-100 rounded-2xl p-6 hover:shadow-md transition">
                        {{-- Huy chương --}}
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            @if($loop->iteration == 1)
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-crown text-white"></i>
                            </div>
                            @elseif($loop->iteration == 2)
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-medal text-white"></i>
                            </div>
                            @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-amber-600 flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-award text-white"></i>
                            </div>
                            @endif
                        </div>

                        <div class="pt-8">
                            <h3 class="font-semibold text-gray-800 mt-3">{{ $t['name'] }}</h3>
                            <p class="text-sm text-gray-500 mb-2">{{ $t['rank'] }}</p>
                            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-semibold">
                                {{ $t['score'] }} điểm
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </article>
        </div>

        {{-- RIGHT: Sidebar --}}
        <aside class="space-y-6">
            <div class="sticky top-28">
                {{-- Thông tin nhanh --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Thông tin nhanh</h3>
                    <ul class="space-y-4 text-sm text-gray-700">
                        <li class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-blue-500"></i>
                            <span><strong>Ngày:</strong> {{ $result->date }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-cyan-500"></i>
                            <span><strong>Thí sinh:</strong> 150+</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-medal text-yellow-500"></i>
                            <span><strong>Giải Nhất:</strong> {{ $result->top3[0]['name'] }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Gợi ý khác --}}
                <div class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white p-6 rounded-2xl shadow-md">
                    <h3 class="text-lg font-bold mb-3">Xem các cuộc thi khác</h3>
                    <p class="text-blue-100 mb-4 text-sm">Khám phá thêm các sân chơi học thuật dành cho sinh viên Khoa CNTT!</p>
                    <a href="{{ route('client.events.index') }}" class="inline-block bg-white text-blue-700 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                        <i class="fa-solid fa-arrow-right mr-2"></i>Xem tất cả
                    </a>
                </div>
            </div>
        </aside>

    </div>
</section>

@endsection