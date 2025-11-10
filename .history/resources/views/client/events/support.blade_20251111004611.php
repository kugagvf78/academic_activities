@extends('layouts.client')
@section('title', 'Đăng ký hỗ trợ ban tổ chức')

@section('content')
<section class="container mx-auto px-6 py-16">
    <h1 class="text-3xl font-bold mb-6 text-blue-700">
        Đăng ký hỗ trợ ban tổ chức – {{ ucfirst($slug) }}
    </h1>

    <div class="bg-white rounded-2xl shadow p-8 max-w-2xl mx-auto space-y-6">
        <p class="text-gray-600">
            Hãy đăng ký tham gia hỗ trợ ban tổ chức cuộc thi để góp phần vào sự thành công của sự kiện!
        </p>

        <form class="space-y-5">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Họ tên sinh viên</label>
                <input type="text" class="w-full border rounded-lg p-3" placeholder="VD: Nguyễn Văn A">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Lớp</label>
                <input type="text" class="w-full border rounded-lg p-3" placeholder="VD: DHCNTT17A">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Vai trò hỗ trợ mong muốn</label>
                <select class="w-full border rounded-lg p-3">
                    <option value="">-- Chọn vai trò --</option>
                    <option value="le-tan">🎀 Lễ tân / Đón khách</option>
                    <option value="truyen-thong">📸 Truyền thông / Ghi hình</option>
                    <option value="ky-thuat">💻 Kỹ thuật / Thiết bị</option>
                    <option value="hau-can">🎯 Hậu cần / Chuẩn bị sân khấu</option>
                    <option value="mc">🎤 MC / Dẫn chương trình</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Ghi chú thêm</label>
                <textarea class="w-full border rounded-lg p-3" rows="3" placeholder="Ví dụ: Em có kinh nghiệm làm MC trong CLB..."></textarea>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-600 transition">
                    Gửi đăng ký hỗ trợ
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
