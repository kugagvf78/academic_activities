@extends('layouts.client')
@section('title', 'Tin tức & Thông báo')

@section('content')
@php
    // Fake paginator tạm thời để test giao diện
    $news = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9);
@endphp

{{-- 🌟 HEADER SECTION - Modern Style --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-28 pb-32 overflow-hidden">
    {{-- Pattern Layer --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0"
            style="background-image: radial-gradient(circle at 25% 80%, #fff 1px, transparent 1px),
                                            radial-gradient(circle at 75% 20%, #fff 1px, transparent 1px);
                    background-size: 40px 40px;">
        </div>
    </div>

    {{-- Wave Effect --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
            <path fill="#fff" fill-opacity="1"
                d="M0,64L60,80C120,96,240,128,360,154.7C480,181,600,203,720,181.3C840,160,960,96,1080,80C1200,64,1320,96,1380,112L1440,128V200H1380C1320,200,1200,200,1080,200C960,200,840,200,720,200C600,200,480,200,360,200C240,200,120,200,60,200H0Z">
            </path>
        </svg>
    </div>

    {{-- Title Card --}}
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto bg-white/30 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-10 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-newspaper text-3xl text-white"></i>
                </div>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">Tin tức & Thông báo</h1>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                Nơi cập nhật những thông tin, hoạt động và sự kiện học thuật mới nhất của Khoa Công nghệ Thông tin.
            </p>
        </div>
    </div>
</section>

{{-- 🗞️ MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16 ">

    {{-- 🗞️ FILTER BAR --}}
    <div class="flex flex-wrap items-center justify-between mb-10 gap-4">

        <div class="flex items-center gap-3">
            <label class="text-gray-600 font-medium">Danh mục:</label>
            <x-form.select
                name="sort"
                placeholder="Chọn thứ tự"
                :options="[
            'newest' => 'Mới nhất',
            'oldest' => 'Cũ nhất',
            'featured' => 'Nổi bật'
        ]"
                selected="newest"
                class="w-48" />
        </div>


        <div class="flex items-center gap-3">
            <label class="text-gray-600 font-medium">Danh mục:</label>
            <x-form.select
                name="category"
                placeholder="Chọn loại tin"
                :options="[
            'all' => 'Tất cả',
            'contest' => 'Cuộc thi học thuật',
            'seminar' => 'Hội thảo',
            'announcement' => 'Thông báo chung'
        ]"
                selected="all"
                class="w-56" />

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
                    <a href="#"
                        class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                        Xem thêm <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    {{-- 📄 PAGINATION --}}
    @if($news->hasPages())
    <div class="mt-16 mx-5">
        {!! $news->appends(request()->query())->links('pagination.custom') !!}
    </div>
    @else
    {{-- 🔸 EMPTY STATE --}}
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
        <div class="max-w-md mx-auto">
            {{-- Icon --}}
            <div class="mb-6 relative">
                <div class="absolute inset-0 bg-blue-100 rounded-full blur-2xl opacity-50"></div>
                <i class="fas fa-newspaper text-8xl text-gray-300 relative"></i>
            </div>

            {{-- Message --}}
            <h4 class="text-2xl font-bold text-gray-700 mb-3">Không tìm thấy bài viết</h4>
            <p class="text-gray-500 mb-8 leading-relaxed">
                @if(request('search') || request('category') || request('sort'))
                Không có tin tức hoặc thông báo nào phù hợp với tiêu chí tìm kiếm.<br>
                Hãy thử điều chỉnh bộ lọc hoặc từ khóa.
                @else
                Hiện tại chưa có tin tức hoặc thông báo nào được đăng.<br>
                Vui lòng quay lại sau hoặc theo dõi fanpage của khoa.
                @endif
            </p>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('client.news.index') }}"
                    class="inline-flex items-center bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                    <i class="fas fa-rotate-right mr-2"></i>
                    Làm mới trang
                </a>
                <a href="#"
                    class="inline-flex items-center bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border border-gray-200">
                    <i class="fas fa-bell mr-2"></i>
                    Nhận thông báo
                </a>
            </div>
        </div>
    </div>
    @endif


</section>

@endsection