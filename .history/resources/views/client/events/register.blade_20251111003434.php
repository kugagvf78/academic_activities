@extends('layouts.client')
@section('title', 'Đăng ký tham gia cuộc thi')

@section('content')

{{-- 🏆 HEADER SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Đăng ký tham gia cuộc thi</h1>
        <p class="text-blue-100 text-lg">Tham gia ngay để khẳng định bản lĩnh và chinh phục đỉnh cao tri thức 🎓</p>
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
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-10"
         x-data="{ type: 'individual', members: [{ name: '', student_code: '', email: '' }] }">

        {{-- Title --}}
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Thông tin đăng ký cuộc thi</h2>
            <p class="text-gray-500">Vui lòng điền đầy đủ thông tin để hoàn tất đăng ký.</p>
        </div>

        <form>
            {{-- 🔹 Thông tin cuộc thi --}}
            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-2">Tên cuộc thi</label>
                <input type="text" value="Database Design Challenge 2025" readonly
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-600 font-medium">
            </div>

            {{-- 🔹 Hình thức tham gia --}}
            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-3">Hình thức thi</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="individual" x-model="type"
                            class="text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-medium">Cá nhân</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="team" x-model="type"
                            class="text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-medium">Theo nhóm</span>
                    </label>
                </div>
            </div>

            {{-- 🔹 Thông tin thí sinh chính --}}
            <div class="mb-10">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                    Thông tin thí sinh chính
                </h3>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Họ và tên</label>
                        <input type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Mã số sinh viên</label>
                        <input type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Email sinh viên</label>
                        <input type="email" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Số điện thoại</label>
                        <input type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- 🔹 Thành viên nhóm (ẩn nếu cá nhân) --}}
            <template x-if="type === 'team'">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                        Thành viên nhóm
                    </h3>

                    <template x-for="(member, index) in members" :key="index">
                        <div class="grid md:grid-cols-3 gap-5 mb-5">
                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Họ và tên</label>
                                <input type="text" x-model="member.name"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Mã SV</label>
                                <input type="text" x-model="member.student_code"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-sm mb-1">Email</label>
                                <input type="email" x-model="member.email"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </template>

                    {{-- Add member button --}}
                    <div class="flex justify-between items-center mt-4">
                        <button type="button"
                            @click="members.push({ name: '', student_code: '', email: '' })"
                            class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            <i class="fa-solid fa-user-plus mr-1"></i>Thêm thành viên
                        </button>

                        <button type="button"
                            @click="if (members.length > 1) members.pop()"
                            class="text-red-600 hover:text-red-700 text-sm font-semibold">
                            <i class="fa-solid fa-user-minus mr-1"></i>Xóa thành viên
                        </button>
                    </div>
                </div>
            </template>

            {{-- 🔹 Ghi chú --}}
            <div class="mt-10">
                <label class="block text-gray-600 text-sm mb-1">Ghi chú (nếu có)</label>
                <textarea rows="3"
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            {{-- 🔹 Submit --}}
            <div class="mt-10 text-center">
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md hover:shadow-xl transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Gửi đăng ký
                </button>
            </div>
        </form>
    </div>
</section>

@endsection
