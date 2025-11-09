@extends('layouts.client')
@section('title', 'Danh sách Cuộc thi Học thuật')
@php
    // Tạo danh sách giả lập tạm thời để test giao diện pagination + danh sách
    $events = collect(range(1, 18))->map(function ($i) {
        return (object) [
            'id' => $i,
            'title' => "Cuộc thi học thuật CNTT #$i",
            'description' => "Chủ đề: Ứng dụng trí tuệ nhân tạo và công nghệ phần mềm trong giáo dục hiện đại.",
            'image' => "https://source.unsplash.com/600x400/?technology,ai,conference,$i",
            'status' => ['Đang mở', 'Sắp diễn ra', 'Đã kết thúc'][array_rand([0,1,2])],
        ];
    })->paginate(6);
@endphp

@section('content')

{{-- 🌊 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-600 via-cyan-600 to-blue-400 text-white py-20 text-center overflow-hidden">
    <div class="absolute inset-0 opacity-20"
        style="background-image: radial-gradient(circle at 20% 80%, #fff 1px, transparent 1px), radial-gradient(circle at 80% 20%, #fff 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    <div class="relative z-10">
        <h2 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight drop-shadow-md">
            Danh sách <span class="text-yellow-300">Cuộc thi Học thuật</span>
        </h2>
        <p class="text-blue-100 text-lg max-w-2xl mx-auto leading-relaxed">
            Khám phá các cuộc thi học thuật nổi bật của Khoa Công nghệ Thông tin – nơi sinh viên thể hiện đam mê và sáng tạo.
        </p>
    </div>
</section>

{{-- 🎯 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-10">
        @for ($i = 1; $i <= 6; $i++)
            <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-2">

            {{-- Ảnh minh họa --}}
            <div class="relative overflow-hidden">
                <img src="https://source.unsplash.com/600x400/?coding,technology,{{ $i }}"
                    alt="Cuộc thi {{ $i }}"
                    class="w-full h-52 object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-all"></div>
                <span class="absolute top-3 left-3 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                    Mở đăng ký
                </span>
            </div>

            {{-- Nội dung --}}
            <div class="p-6">
                <h3 class="font-bold text-lg text-blue-700 group-hover:text-cyan-600 transition mb-2">
                    Cuộc thi Lập trình sáng tạo {{ $i }}
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    Chủ đề: Ứng dụng công nghệ mới trong giải pháp thực tế — khám phá và thể hiện năng lực tư duy của bạn!
                </p>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-blue-600"></i>
                        <span>Tháng {{ rand(3, 12) }}, 2025</span>
                    </div>
                    <a href="#"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition inline-flex items-center gap-2">
                        <span>Xem chi tiết</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
    </div>
    @endfor
    </div>

    {{-- 🔹 Pagination --}}
    @if($events->hasPages())
    <div class="mt-12">
        <div class="flex justify-center">
            <nav aria-label="Event pagination" class="pagination-wrapper">
                {!! $events->appends(request()->query())->links('pagination.custom') !!}
            </nav>
        </div>
    </div>
    @else
    {{-- 🔸 Nếu không có dữ liệu --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="mb-6">
            <i class="fas fa-calendar-xmark text-6xl text-gray-300"></i>
        </div>
        <h4 class="text-xl font-semibold text-gray-600 mb-3">Không tìm thấy cuộc thi nào</h4>
        <p class="text-gray-500 mb-6">
            @if(request('search') || request('status') || request('category'))
            Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm
            @else
            Hiện tại chưa có cuộc thi nào được đăng trong hệ thống
            @endif
        </p>
        <a href="{{ route('client.events') }}"
            class="inline-flex items-center bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition">
            <i class="fas fa-rotate-right mr-2"></i>
            Làm mới trang
        </a>
    </div>
    @endif

</section>

@endsection