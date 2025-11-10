@extends('layouts.client')
@section('title', 'Danh sách Cuộc thi Học thuật')

@section('content')

{{-- 🎓 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-24 overflow-hidden">
    {{-- Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    {{-- Floating shapes --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-300/20 rounded-full blur-3xl animate-pulse delay-700"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            {{-- Badge --}}
            <div class="inline-block mb-6">
                <span class="bg-white/15 text-white px-4 py-2 rounded-full text-sm font-semibold backdrop-blur-sm border border-white/20">
                    <i class="fas fa-trophy mr-2 text-cyan-200"></i>Cuộc thi Học thuật
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-5xl md:text-6xl font-black mb-6 tracking-tight">
                Đấu trường
                <span class="bg-gradient-to-r from-cyan-200 via-blue-200 to-cyan-100 bg-clip-text text-transparent">
                    Tri thức & Sáng tạo
                </span>
            </h1>

            <p class="text-lg md:text-xl text-blue-100 leading-relaxed max-w-3xl mx-auto mb-8">
                Nơi sinh viên Công nghệ Thông tin thể hiện tài năng, khám phá đam mê và kiến tạo tương lai công nghệ.
            </p>

            {{-- Stats --}}
            <div class="flex flex-wrap justify-center gap-8 mt-12">
                <div class="text-center">
                    <div class="text-4xl font-bold text-white mb-1">15+</div>
                    <div class="text-blue-100 text-sm">Cuộc thi / năm</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-white mb-1">500+</div>
                    <div class="text-blue-100 text-sm">Sinh viên tham gia</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-white mb-1">50+</div>
                    <div class="text-blue-100 text-sm">Dự án xuất sắc</div>
                </div>
            </div>
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

            {{-- 🔍 Ô tìm kiếm --}}
            <div class="col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Tìm kiếm cuộc thi..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            {{-- ⚙️ Trạng thái --}}
            <div>
                <select class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả trạng thái</option>
                    <option value="open">Đang mở đăng ký</option>
                    <option value="ongoing">Đang thi</option>
                    <option value="ended">Đã kết thúc</option>
                </select>
            </div>
            {{-- Lĩnh vực --}}
            <x-form.select
                name="category"
                :options="[
            'programming' => 'Lập trình',
            'ai' => 'AI & ML',
            'security' => 'An toàn thông tin',
            'web' => 'Web Development',
        ]"
                selected="{{ request('category') }}"
                placeholder="Tất cả lĩnh vực" />


            {{-- 📅 Từ ngày --}}
            <div>
                <input type="date"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- 📅 Đến ngày --}}
            <div>
                <input type="date"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- 🧩 Nút lọc & xóa --}}
            <div class="flex gap-3">
                <button type="button"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-semibold flex items-center justify-center gap-2 transition">
                    <i class="fa-solid fa-filter"></i> <span>Lọc</span>
                </button>

                <button type="reset"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-xl font-semibold flex items-center justify-center gap-2 transition">
                    <i class="fa-solid fa-rotate-left"></i> <span>Xóa</span>
                </button>
            </div>
        </form>
    </div>
</section>


{{-- 🎯 EVENTS GRID --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
        @for ($i = 1; $i <= 6; $i++)
            <article class="group bg-white rounded-2xl shadow-md hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-500 hover:-translate-y-3">
            <div class="relative overflow-hidden h-56">
                <img src="{{asset('images/home/banner1.png')}}?coding,technology,competition,{{ $i }}"
                    alt="Cuộc thi {{ $i }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>

                <div class="absolute top-4 left-4">
                    <span class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg backdrop-blur-sm flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        Đang mở đăng ký
                    </span>
                </div>

                <div class="absolute top-4 right-4">
                    <div class="bg-white/90 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
                        <i class="fas fa-trophy text-yellow-500"></i>
                        <span>{{ rand(10, 50) }}M</span>
                    </div>
                </div>

                <div class="absolute bottom-4 left-4">
                    <span class="bg-white/90 backdrop-blur-sm text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ ['Lập trình', 'AI & ML', 'Web Dev', 'Data Science', 'Security'][rand(0, 4)] }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <h3 class="font-bold text-xl text-gray-800 group-hover:text-blue-600 transition mb-3 line-clamp-2">
                    Cuộc thi Lập trình Sáng tạo {{ $i }}: {{ ['Khoa học Dữ liệu', 'AI', 'Ứng dụng Web', 'Mobile App', 'IoT Smart'][rand(0, 4)] }}
                </h3>

                <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                    Thách thức xây dựng giải pháp công nghệ sáng tạo, ứng dụng thực tế để giải quyết các vấn đề xã hội và cải thiện chất lượng cuộc sống.
                </p>

                <div class="flex items-center gap-4 mb-4 text-sm text-gray-500">
                    <div class="flex items-center gap-1.5"><i class="far fa-calendar text-blue-500"></i><span>{{ rand(15, 30) }}/{{ rand(3, 12) }}/2025</span></div>
                    <div class="flex items-center gap-1.5"><i class="far fa-clock text-cyan-500"></i><span>{{ rand(30, 90) }} ngày</span></div>
                    <div class="flex items-center gap-1.5"><i class="far fa-user text-purple-500"></i><span>{{ rand(50, 200) }}+</span></div>
                </div>

                <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white text-xs font-bold">IT</div>
                        <span class="text-xs text-gray-600 font-medium">Khoa CNTT</span>
                    </div>

                    <a href="#"
                        class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-xl transition-all inline-flex items-center gap-2">
                        <span>Chi tiết</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            </article>
            @endfor
    </div>

    {{-- 📄 PAGINATION --}}
    @if($events->hasPages())
    <div class="mt-16 mx-5">
        {!! $events->appends(request()->query())->links('pagination.custom') !!}
    </div>
    @else
    {{-- 🔸 EMPTY STATE --}}
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
        <div class="max-w-md mx-auto">
            {{-- Icon --}}
            <div class="mb-6 relative">
                <div class="absolute inset-0 bg-blue-100 rounded-full blur-2xl opacity-50"></div>
                <i class="fas fa-calendar-xmark text-8xl text-gray-300 relative"></i>
            </div>

            {{-- Message --}}
            <h4 class="text-2xl font-bold text-gray-700 mb-3">Không tìm thấy cuộc thi</h4>
            <p class="text-gray-500 mb-8 leading-relaxed">
                @if(request('search') || request('status') || request('category'))
                Không có cuộc thi nào phù hợp với tiêu chí tìm kiếm của bạn.<br>
                Hãy thử điều chỉnh bộ lọc hoặc từ khóa.
                @else
                Hiện tại chưa có cuộc thi nào được công bố.<br>
                Vui lòng quay lại sau hoặc theo dõi fanpage của khoa.
                @endif
            </p>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('client.events') }}"
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

{{-- 💡 CTA --}}
<section class="bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-500 py-16 text-white text-center">
    <div class="container mx-auto px-6">
        <h3 class="text-3xl font-bold mb-4">Bạn có ý tưởng xuất sắc?</h3>
        <p class="text-blue-100 mb-8 text-lg">Đừng bỏ lỡ cơ hội thể hiện tài năng và giành giải thưởng hấp dẫn!</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="#" class="bg-white text-blue-700 px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2">
                <i class="fas fa-rocket"></i> <span>Đăng ký ngay</span>
            </a>
            <a href="#" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2 border border-white/30">
                <i class="fas fa-calendar-plus"></i> <span>Xem lịch thi đấu</span>
            </a>
        </div>
    </div>
</section>

@endsection