    @extends('layouts.client')
    @section('title', 'Đăng ký cổ vũ')

    @section('content')
    <section class="container mx-auto px-6 py-16">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">Đăng ký cổ vũ sự kiện</h1>

        <div class="bg-white rounded-2xl shadow p-8 space-y-6 max-w-2xl">
            <p class="text-gray-600">Hãy đăng ký tham dự cổ vũ cho cuộc thi <strong>{{ $slug }}</strong> của Khoa CNTT.</p>

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
                    <label class="block text-gray-700 font-semibold mb-2">Vai trò tham dự</label>
                    <select class="w-full border rounded-lg p-3">
                        <option value="co-vu">Cổ vũ viên</option>
                        <option value="to-chuc">Hỗ trợ ban tổ chức</option>
                    </select>
                </div>

                <div class="text-right">
                    <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-600 transition">
                        Xác nhận đăng ký
                    </button>
                </div>
            </form>
        </div>
    </section>
    @endsection
