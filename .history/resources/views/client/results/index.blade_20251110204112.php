@extends('layouts.client')
@section('title', 'Kết quả Cuộc thi Học thuật')

@section('content')
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-20">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Kết quả các Cuộc thi Học thuật</h1>
        <p class="text-blue-100 text-lg max-w-3xl mx-auto">Tổng hợp thành tích và kết quả của các cuộc thi học thuật nổi bật tại Khoa Công nghệ Thông tin.</p>
    </div>
</section>

<section class="container mx-auto px-6 py-16">
    <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-8">
        @foreach ($results as $result)
        <article class="group bg-white border border-gray-100 rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden">
            <div class="h-48 overflow-hidden">
                <img src="{{ $result->image }}" alt="{{ $result->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="p-6">
                <h3 class="font-bold text-lg text-gray-800 group-hover:text-blue-600 transition mb-2">
                    {{ $result->title }}
                </h3>
                <p class="text-gray-500 text-sm mb-3"><i class="fa-regular fa-calendar text-blue-500 mr-1"></i>{{ $result->date }}</p>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span><i class="fa-solid fa-users text-cyan-500 mr-1"></i>{{ $result->participants }} người tham gia</span>
                    <span><i class="fa-solid fa-crown text-yellow-500 mr-1"></i>Quán quân: <strong>{{ $result->winner }}</strong></span>
                </div>
                <div class="mt-5">
                    <a href="{{ route('client.results.show', $result->id) }}"
                        class="inline-flex items-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:shadow-lg transition">
                        Xem chi tiết
                        <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endsection
