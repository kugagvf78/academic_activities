@extends('layouts.client')
@section('title', 'Trang chủ')

@section('content')
<section class="bg-gradient-to-r from-blue-600 to-blue-400 text-white py-24 text-center">
    <h1 class="text-4xl font-bold mb-4">Hệ thống Quản lý Hội thảo Học thuật</h1>
    <p class="text-lg mb-8">Kết nối – Chia sẻ – Phát triển tri thức cùng Khoa Công nghệ Thông tin</p>
    <a href="{{ route('client.events') }}" class="btn-outline bg-white text-blue-700 hover:bg-blue-100">Xem các hội thảo</a>
</section>

<section class="container mx-auto py-16 px-4 text-center">
    <h2 class="text-2xl font-bold text-blue-700 mb-6">Vì sao nên tham gia hội thảo?</h2>
    <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 shadow-md rounded-xl">
            <i class="fa-solid fa-lightbulb text-blue-600 text-3xl mb-3"></i>
            <h3 class="font-semibold mb-2">Mở rộng kiến thức</h3>
            <p>Tiếp cận các xu hướng công nghệ mới và học hỏi từ các chuyên gia hàng đầu.</p>
        </div>
        <div class="bg-white p-6 shadow-md rounded-xl">
            <i class="fa-solid fa-users text-blue-600 text-3xl mb-3"></i>
            <h3 class="font-semibold mb-2">Kết nối cộng đồng</h3>
            <p>Giao lưu, trao đổi kinh nghiệm và hợp tác nghiên cứu với sinh viên – giảng viên.</p>
        </div>
        <div class="bg-white p-6 shadow-md rounded-xl">
            <i class="fa-solid fa-award text-blue-600 text-3xl mb-3"></i>
            <h3 class="font-semibold mb-2">Khẳng định bản thân</h3>
            <p>Tham gia và đạt thành tích trong các hội thảo là cơ hội ghi dấu ấn học thuật.</p>
        </div>
    </div>
</section>
@endsection
