@extends('layouts.client')
@section('title', 'Chi tiết Cuộc thi Học thuật')

@section('content')

{{-- 🎓 HERO SECTION - Thanh lịch & Chuyên nghiệp --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-24 overflow-hidden">
    {{-- Subtle pattern overlay --}}
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: linear-gradient(30deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(150deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(30deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff), linear-gradient(150deg, #ffffff 12%, transparent 12.5%, transparent 87%, #ffffff 87.5%, #ffffff); background-size: 80px 140px; background-position: 0 0, 0 0, 40px 70px, 40px 70px;"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">
            <ol class="flex items-center gap-2 text-blue-200">
                <li><a href="#" class="hover:text-white transition">Trang chủ</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="#" class="hover:text-white transition">Cuộc thi học thuật</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-white">Database Design Challenge 2025</li>
            </ol>
        </nav>

        <div class="max-w-4xl">
            {{-- Status badge --}}
            <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 px-4 py-2 rounded-lg text-sm font-semibold mb-6">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                Đang mở đăng ký
            </div>

            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                Database Design Challenge 2025
            </h1>

            {{-- Description --}}
            <p class="text-xl text-blue-100 leading-relaxed mb-8">
                Cuộc thi thiết kế cơ sở dữ liệu dành cho sinh viên Khoa Công nghệ Thông tin – nơi thể hiện tư duy, sáng tạo và kỹ năng mô hình hóa dữ liệu chuyên nghiệp.
            </p>

            {{-- Meta info --}}
            <div class="flex flex-wrap gap-6 mb-8 text-blue-100">
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar"></i>
                    <span>07/12/2025</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="far fa-clock"></i>
                    <span>7h45 - 16h30</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Trường Đại học Công Thương TP.HCM</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    <span>150+ sinh viên đăng ký</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-wrap gap-4">
                <a href="#" class="bg-white text-blue-900 px-8 py-3.5 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:bg-blue-50 transition inline-flex items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký tham gia</span>
                </a>
                <a href="#" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-white/20 transition inline-flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    <span>Tải thông báo</span>
                </a>
                <button class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-4 py-3.5 rounded-lg font-semibold hover:bg-white/20 transition">
                    <i class="fas fa-share-nodes"></i>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- 📋 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-16">
    <div class="grid lg:grid-cols-3 gap-10">

        {{-- Left column - Main content --}}
        <div class="lg:col-span-2 space-y-12">

            {{-- Giới thiệu --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Giới thiệu chung</h2>
                </div>
                <div class="prose prose-blue max-w-none">
                    <p class="text-gray-700 leading-relaxed">
                        Cuộc thi học thuật <strong>"Database Design Challenge"</strong> là sân chơi học thuật giúp sinh viên vận dụng kiến thức về mô hình hóa, chuẩn hóa và tối ưu hóa cơ sở dữ liệu vào thực tiễn.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Sự kiện được tổ chức bởi <strong>Khoa Công nghệ Thông tin – Trường Đại học Công Thương TP.HCM</strong>, với sự tham gia của các giảng viên và chuyên gia đến từ doanh nghiệp, nhằm tạo cơ hội cho sinh viên thể hiện năng lực và tiếp cận với thực tế nghề nghiệp.
                    </p>
                </div>
            </article>

            {{-- Mục tiêu & Yêu cầu --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullseye text-emerald-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Mục tiêu & Yêu cầu</h2>
                </div>
                <ul class="space-y-3">
                    <li class="flex gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 mt-1"></i>
                        <span>Tạo cơ hội để sinh viên rèn luyện kỹ năng thiết kế và phân tích cơ sở dữ liệu.</span>
                    </li>
                    <li class="flex gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 mt-1"></i>
                        <span>Phát hiện và bồi dưỡng sinh viên có năng khiếu, đam mê về CSDL.</span>
                    </li>
                    <li class="flex gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 mt-1"></i>
                        <span>Đảm bảo cuộc thi diễn ra khách quan, minh bạch và chuyên nghiệp.</span>
                    </li>
                    <li class="flex gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 mt-1"></i>
                        <span>Kết nối sinh viên với các chuyên gia trong lĩnh vực dữ liệu.</span>
                    </li>
                </ul>
            </article>

            {{-- Thời gian & Địa điểm --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-amber-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Thời gian & Địa điểm</h2>
                </div>

                <div class="space-y-4">
                    <div class="bg-blue-50 border-l-4 border-blue-600 p-5 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-clock text-blue-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Vòng Sơ khảo</p>
                                <p class="text-gray-700 text-sm">7h45 - 8h45, Chủ nhật ngày 07/12/2025</p>
                                <p class="text-gray-600 text-sm mt-1">Địa điểm: Phòng B205, B401, B502</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 border-l-4 border-purple-600 p-5 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-trophy text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Vòng Chung kết</p>
                                <p class="text-gray-700 text-sm">13h30 - 14h30, cùng ngày</p>
                                <p class="text-gray-600 text-sm mt-1">Địa điểm: Phòng A204, A209</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 border-l-4 border-gray-400 p-5 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user-graduate text-gray-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">Đối tượng tham gia</p>
                                <p class="text-gray-700 text-sm">Sinh viên năm 2 và năm 3 các ngành CNTT, ATTT, Khoa học Dữ liệu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Cấu trúc cuộc thi --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-layer-group text-indigo-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Cấu trúc cuộc thi</h2>
                </div>

                <div class="space-y-5">
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold">01</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Vòng Sơ khảo</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Thi trắc nghiệm lý thuyết về mô hình dữ liệu, chuẩn hóa, SQL cơ bản. Hình thức thi cá nhân, thời gian 60 phút.
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full">Trắc nghiệm</span>
                                    <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full">Cá nhân</span>
                                    <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full">60 phút</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-purple-300 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">02</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Vòng Chung kết</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Thi thực hành thiết kế CSDL trên máy tính (PowerDesigner, SQL Server). Thí sinh làm việc theo nhóm 2-3 người.
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1 rounded-full">Thực hành</span>
                                    <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1 rounded-full">Nhóm</span>
                                    <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1 rounded-full">90 phút</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Giải thưởng --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-award text-yellow-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Giải thưởng</h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-lg">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                            <i class="fas fa-trophy text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-800 text-lg">Giải Nhất</p>
                            <p class="text-gray-600 text-sm">1 giải</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-amber-600 text-xl">1.000.000đ</p>
                            <p class="text-gray-500 text-xs">+ Giấy khen</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-200 rounded-lg">
                        <div class="w-14 h-14 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                            <i class="fas fa-medal text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-800 text-lg">Giải Nhì</p>
                            <p class="text-gray-600 text-sm">1 giải</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-600 text-xl">700.000đ</p>
                            <p class="text-gray-500 text-xs">+ Giấy khen</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                            <i class="fas fa-award text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-800 text-lg">Giải Ba</p>
                            <p class="text-gray-600 text-sm">1 giải</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-orange-600 text-xl">500.000đ</p>
                            <p class="text-gray-500 text-xs">+ Giấy khen</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-sm text-gray-700 flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <span>Tất cả thí sinh vào vòng chung kết đều nhận Giấy chứng nhận tham gia.</span>
                    </p>
                </div>
            </article>

            {{-- Ban giám khảo --}}
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-teal-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Ban Giám khảo & Diễn giả</h2>
                </div>

                <p class="text-gray-700 mb-6 leading-relaxed">
                    Cuộc thi có sự tham gia chấm điểm và phản biện của các giảng viên Khoa CNTT cùng đại diện doanh nghiệp:
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">ThS. Nguyễn Thị Thanh Thủy</p>
                            <p class="text-sm text-gray-600">Trưởng Ban Giám khảo</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Hồ Văn Lực</p>
                            <p class="text-sm text-gray-600">Giám đốc Công ty CP Tin học Đại Phát</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Nguyễn Thanh Tài</p>
                            <p class="text-sm text-gray-600">Lead Consultant, Amaris Consulting</p>
                        </div>
                    </div>
                </div>
            </article>

        </div>

        {{-- Right column - Sidebar --}}
        <aside class="space-y-6">

            {{-- Quick info card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-28">
                <h3 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Thông tin nhanh</h3>

                <ul class="space-y-4 mb-6">
                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="far fa-calendar text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Ngày tổ chức</p>
                            <p class="font-semibold text-gray-800">07/12/2025</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="far fa-clock text-emerald-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Thời gian</p>
                            <p class="font-semibold text-gray-800">7h45 - 16h30</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-amber-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Địa điểm</p>
                            <p class="font-semibold text-gray-800">Khu A & B - HUIT</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-tie text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Người phụ trách</p>
                            <p class="font-semibold text-gray-800">ThS. Nguyễn Văn Lễ</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3 text-sm">
                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-indigo-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-0.5">Đối tượng</p>
                            <p class="font-semibold text-gray-800">SV CNTT (Năm 2-3)</p>
                        </div>
                    </li>
                </ul>

                <div class="pt-5 border-t border-gray-100 space-y-3">
                    <a href="#" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold shadow-sm hover:shadow transition">
                        Đăng ký ngay
                    </a>
                    <a href="#" class="w-full block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold transition">
                        Liên hệ BTC
                    </a>
                </div>
            </div>

            <!-- {{-- Related events --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-3 border-b border-gray-100">Cuộc thi liên quan</h3>

                <div class="space-y-4">
                    <a href="#" class="block group">
                        <div class="flex gap-3">
                            <img src="https://source.unsplash.com/100x100/?programming"
                                alt="Event"
                                class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                            <div>
                                <p class="font-semibold text-sm text-gray-800 group-hover:text-blue-600 transition line-clamp-2 mb-1">
                                    Web Development Contest 2025
                                </p>
                                <p class="text-xs text-gray-500">15/01/2025</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="block group">
                        <div class="flex gap-3">
                            <img src="https://source.unsplash.com/100x100/?ai,technology"
                                alt="Event"
                                class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                            <div>
                                <p class="font-semibold text-sm text-gray-800 group-hover:text-blue-600 transition line-clamp-2 mb-1">
                                    AI Innovation Challenge
                                </p>
                                <p class="text-xs text-gray-500">22/02/2025</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div> -->

        </aside>
    </div>
    @endsection