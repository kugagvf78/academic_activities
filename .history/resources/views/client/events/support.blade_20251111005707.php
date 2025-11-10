@extends('layouts.client')
@section('title', 'Đăng ký hỗ trợ Ban tổ chức')

@section('content')

{{-- ⚙️ HEADER SECTION --}}

<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">

    <div class="mb-6">
        <a href="{{ route('client.events.show', $slug) }}"
            class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại chi tiết cuộc thi
        </a>
    </div>
    <div class="container mx-auto px-6 text-center relative z-10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Đăng ký hỗ trợ Ban tổ chức</h1>
        <p class="text-blue-100 text-lg">Trở thành một phần của đội ngũ tổ chức và cùng tạo nên thành công cho cuộc thi 💪</p>
    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z" />
        </svg>
    </div>
</section>

{{-- 🧾 FORM SECTION --}}
<section class="container mx-auto px-6 py-16">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-10">

        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Thông tin đăng ký hỗ trợ</h2>
            <p class="text-gray-500">Hãy lựa chọn vai trò phù hợp để cùng ban tổ chức vận hành sự kiện hiệu quả nhất.</p>
        </div>

        <form>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-gray-600 text-sm mb-1">Họ và tên</label>
                    <input type="text" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm mb-1">Mã số sinh viên</label>
                    <input type="text" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm mb-1">Lớp</label>
                    <input type="text" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm mb-1">Email</label>
                    <input type="email" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-2">Vai trò hỗ trợ mong muốn</label>
                <select class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn vai trò --</option>
                    <option value="le-tan">🎀 Lễ tân / Đón khách</option>
                    <option value="ky-thuat">💻 Kỹ thuật / Hỗ trợ thiết bị</option>
                    <option value="truyen-thong">📸 Truyền thông / Ghi hình</option>
                    <option value="hau-can">🎯 Hậu cần / Chuẩn bị sân khấu</option>
                    <option value="mc">🎤 MC / Dẫn chương trình</option>
                </select>
            </div>

            <div class="mb-8">
                <label class="block text-gray-600 text-sm mb-1">Ghi chú thêm (nếu có)</label>
                <textarea rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>


            <div class="text-center">
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md hover:shadow-xl transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Gửi đăng ký hỗ trợ
                </button>
            </div>
        </form>
    </div>
</section>
@endsection