@extends('layouts.client')
@section('title', 'Kết quả Cuộc thi')

@section('content')
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-20">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">{{ $result->title }}</h1>
        <p class="text-blue-100 text-lg">Ngày tổ chức: {{ $result->date }}</p>
    </div>
</section>

<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-3 gap-10">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-10">
            {{-- Tổng quan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Tổng quan cuộc thi</h2>
                <p class="text-gray-700 leading-relaxed">
                    Cuộc thi đã thu hút hơn 150 sinh viên tham gia với nhiều ý tưởng thiết kế cơ sở dữ liệu sáng tạo.
                    Dưới đây là kết quả và bảng xếp hạng của các vòng thi.
                </p>
            </div>

            {{-- Kết quả theo vòng --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Kết quả từng vòng</h2>
                <div class="space-y-4">
                    @foreach($result->rounds as $round)
                    <div class="border border-gray-100 p-4 rounded-lg flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-layer-group text-blue-600"></i>
                            <span class="font-medium text-gray-700">{{ $round['name'] }}</span>
                        </div>
                        <span class="text-sm text-gray-600">🏆 {{ $round['winner'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Top 3 --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Top 3 Chung cuộc</h2>
                <div class="space-y-6">
                    @foreach($result->top3 as $t)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $t['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $t['rank'] }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-blue-600">{{ $t['score'] }} điểm</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Thông tin nhanh</h3>
                <ul class="space-y-3 text-sm text-gray-700">
                    <li><i class="fa-regular fa-calendar text-blue-500 mr-2"></i><strong>Ngày:</strong> {{ $result->date }}</li>
                    <li><i class="fa-solid fa-users text-cyan-500 mr-2"></i><strong>Thí sinh:</strong> 150+</li>
                    <li><i class="fa-solid fa-medal text-yellow-500 mr-2"></i><strong>Giải Nhất:</strong> {{ $result->top3[0]['name'] }}</li>
                </ul>
            </div>

            <div class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-bold mb-3">Xem các cuộc thi khác</h3>
                <p class="text-blue-100 mb-4 text-sm">Khám phá thêm các sân chơi học thuật dành cho sinh viên CNTT!</p>
                <a href="{{ route('client.events.index') }}" class="inline-block bg-white text-blue-700 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                    Xem tất cả
                </a>
            </div>
        </aside>
    </div>
</section>
@endsection
