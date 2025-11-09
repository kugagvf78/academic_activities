@extends('layouts.client')
@section('title', 'Trang chủ')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 text-white py-24 overflow-hidden">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Hệ thống Quản lý Hội thảo Học thuật
        </h1>
        <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
            Nền tảng hỗ trợ sinh viên và giảng viên Khoa CNTT trong việc tổ chức, tham gia và quản lý hội thảo học thuật.
        </p>
        <a href="{{ route('client.events') }}" class="bg-white text-primary font-semibold px-6 py-3 rounded-full shadow hover:bg-blue-100 transition">
            Xem danh sách Hội thảo
        </a>
    </div>

    <div class="absolute inset-0 bg-[url('https://source.unsplash.com/1600x800/?technology,conference')] bg-cover bg-center opacity-20"></div>
</section>

{{-- ABOUT SECTION --}}
<section class="container mx-auto py-20 px-6 text-center">
    <h2 class="text-3xl font-bold text-primary mb-6">Giới thiệu</h2>
    <p class="max-w-3xl mx-auto text-gray-600 mb-10 leading-relaxed">
        Hệ thống được phát triển với mục tiêu tạo ra môi trường học thuật năng động, nơi sinh viên và giảng viên
        có thể dễ dàng tổ chức, đăng ký và quản lý các buổi hội thảo khoa học. Qua đó, Khoa CNTT khuyến khích việc
        chia sẻ tri thức, phát triển kỹ năng nghiên cứu và hợp tác sáng tạo giữa các thành viên.
    </p>
    <div class="grid md:grid-cols-3 gap-8 mt-10">
        <div class="bg-white shadow-lg rounded-xl p-6 hover:-translate-y-2 transition">
            <i class="fa-solid fa-lightbulb text-primary text-3xl mb-4"></i>
            <h3 class="font-semibold text-lg mb-2">Phát triển tri thức</h3>
            <p class="text-gray-600 text-sm">Khám phá và học hỏi từ các chuyên gia hàng đầu trong lĩnh vực công nghệ.</p>
        </div>
        <div class="bg-white shadow-lg rounded-xl p-6 hover:-translate-y-2 transition">
            <i class="fa-solid fa-users text-primary text-3xl mb-4"></i>
            <h3 class="font-semibold text-lg mb-2">Kết nối học thuật</h3>
            <p class="text-gray-600 text-sm">Tạo cơ hội giao lưu, chia sẻ và hợp tác giữa sinh viên và giảng viên.</p>
        </div>
        <div class="bg-white shadow-lg rounded-xl p-6 hover:-translate-y-2 transition">
            <i class="fa-solid fa-award text-primary text-3xl mb-4"></i>
            <h3 class="font-semibold text-lg mb-2">Khẳng định năng lực</h3>
            <p class="text-gray-600 text-sm">Tham gia và đạt giải trong các hội thảo giúp sinh viên nổi bật hơn.</p>
        </div>
    </div>
</section>

{{-- CONTACT SECTION --}}
<section class="bg-blue-50 py-20">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-primary mb-8">Liên hệ với chúng tôi</h2>
        <form class="max-w-xl mx-auto bg-white shadow-lg rounded-xl p-8">
            <div class="mb-4">
                <input type="text" placeholder="Họ và tên" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="mb-4">
                <input type="email" placeholder="Email" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="mb-6">
                <textarea rows="4" placeholder="Nội dung" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition font-semibold">
                Gửi liên hệ
            </button>
        </form>
    </div>
</section>
@endsection