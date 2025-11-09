@extends('layouts.client')
@section('title', 'Danh sách Hội thảo')

@section('content')
<section class="bg-gradient-to-r from-blue-600 to-blue-400 text-white py-20 text-center">
    <h2 class="text-4xl font-bold mb-2">Danh sách Hội thảo Học thuật</h2>
    <p class="text-blue-100">Khám phá các buổi hội thảo nổi bật trong Khoa Công nghệ Thông tin</p>
</section>

<section class="container mx-auto px-6 py-16">
    <div class="grid md:grid-cols-3 gap-10">
        @for ($i = 1; $i <= 6; $i++)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition overflow-hidden">
                <img src="https://source.unsplash.com/600x400/?technology,conference,{{ $i }}" class="w-full h-48 object-cover" alt="Hội thảo {{ $i }}">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-primary mb-2">Hội thảo công nghệ {{ $i }}</h3>
                    <p class="text-gray-600 mb-4 text-sm">
                        Chủ đề: Ứng dụng AI và phát triển phần mềm trong giáo dục hiện đại.
                    </p>
                    <button class="bg-primary text-white px-5 py-2 rounded-lg hover:bg-secondary transition text-sm">
                        Xem chi tiết
                    </button>
                </div>
            </div>
        @endfor
    </div>
</section>
@endsection
