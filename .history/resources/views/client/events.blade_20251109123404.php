@extends('layouts.client')
@section('title', 'Danh sách Hội thảo')

@section('content')
<section class="container mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-blue-700 mb-8 text-center">Danh sách Hội thảo Học thuật</h2>
    <div class="grid md:grid-cols-3 gap-8">
        @for ($i = 1; $i <= 6; $i++)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">
                <img src="https://source.unsplash.com/600x400/?conference,technology,{{ $i }}" class="w-full h-48 object-cover" alt="">
                <div class="p-5">
                    <h3 class="font-semibold text-lg text-blue-700 mb-2">Hội thảo công nghệ {{ $i }}</h3>
                    <p class="text-gray-600 mb-4">Chủ đề: Xu hướng công nghệ mới trong phát triển phần mềm và AI.</p>
                    <a href="#" class="btn-primary text-sm">Xem chi tiết</a>
                </div>
            </div>
        @endfor
    </div>
</section>
@endsection
