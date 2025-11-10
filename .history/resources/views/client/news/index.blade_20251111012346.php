@extends('layouts.client')
@section('title', 'Tin tức & Thông báo')

@section('content')

{{-- 🌟 HEADER SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 30% 70%, #fff 1px, transparent 1px), radial-gradient(circle at 70% 30%, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Tin tức & Thông báo</h1>
        <p class="text-blue-100 text-lg">Cập nhật nhanh nhất các thông báo, hoạt động và sự kiện học thuật của khoa 🎓</p>
    </div>
</section>

{{-- 🗞️ MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16">
    
    {{-- FILTER BAR --}}
    <div class="flex flex-wrap items-center justify-between mb-10 gap-4">
        <div class="flex items-center gap-3">
            <label class="text-gray-600 font-medium">Sắp xếp:</label>
            <select class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option>Mới nhất</option>
                <option>Cũ nhất</option>
                <option>Nổi bật</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            <label class="text-gray-600 font-medium">Danh mục:</label>
            <select class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option>Tất cả</option>
                <option>Cuộc thi học thuật</option>
                <option>Hội thảo</option>
                <option>Thông báo chung</option>
            </select>
        </div>
    </div>

    {{-- NEWS GRID --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach (range(1,6) as $i)
        <article class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition overflow-hidden group">
            <div class="relative overflow-hidden">
                <img src="https://picsum.photos/600/400?random={{ $i }}" alt="News image" 
                     class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                    {{ ['Cuộc thi','Thông báo','Hội thảo'][($i % 3)] }}
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition">
                    {{ ['AI Innovation Contest 2025','Lịch họp Ban Học Thuật','Web Development Challenge','Hội thảo Blockchain Ứng dụng','Thông báo đăng ký thi học kỳ','Seminar AI & Data Science'][$i-1] ?? 'Tin tức học thuật' }}
                </h2>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    Đây là đoạn mô tả ngắn gọn về nội dung bài viết. Cập nhật nhanh thông tin mới nhất từ khoa CNTT, bao gồm lịch trình, quy định và kết quả các hoạt động.
                </p>

                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <i class="far fa-calendar text-blue-500"></i>
                        <span>{{ now()->subDays($i)->format('d/m/Y') }}</span>
                    </div>
                    <a href="{{ route('client.news.show', $i) }}" 
                       class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                        Xem thêm <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    <div class="mt-12 flex justify-center">
        <nav class="flex gap-2">
            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white font-semibold">1</button>
            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">2</button>
            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">3</button>
            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        </nav>
    </div>

</section>

@endsection
