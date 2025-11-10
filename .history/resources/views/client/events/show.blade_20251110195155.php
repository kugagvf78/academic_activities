@extends('layouts.client')
@section('title', 'Chi tiết Cuộc thi Học thuật')

@section('content')
<section class="relative bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-500 text-white py-20">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-5xl font-extrabold mb-4">Database Design Challenge 2025</h1>
        <p class="text-lg text-blue-100 max-w-3xl mx-auto leading-relaxed">
            Cuộc thi thiết kế cơ sở dữ liệu dành cho sinh viên Khoa Công nghệ Thông tin – nơi thể hiện tư duy, sáng tạo và kỹ năng mô hình hóa dữ liệu chuyên nghiệp.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="#" class="bg-white text-blue-700 px-8 py-3 rounded-xl font-semibold shadow-md hover:shadow-xl transition">Đăng ký tham gia</a>
            <a href="#" class="bg-white/20 border border-white/30 px-8 py-3 rounded-xl font-semibold hover:bg-white/30 transition">Tải thông báo</a>
        </div>
    </div>
</section>

{{-- 🔹 Thông tin chi tiết --}}
<section class="container mx-auto px-6 py-16 grid lg:grid-cols-3 gap-12">
    {{-- Nội dung chính --}}
    <div class="lg:col-span-2 space-y-10">
        {{-- Giới thiệu --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Giới thiệu</h2>
            <p class="text-gray-600 leading-relaxed">
                Cuộc thi học thuật “Database Design Challenge” là sân chơi học thuật giúp sinh viên vận dụng kiến thức về mô hình hóa, chuẩn hóa và tối ưu hóa cơ sở dữ liệu vào thực tiễn.
                Sự kiện được tổ chức bởi Khoa Công nghệ Thông tin – Trường Đại học Công Thương TP.HCM, với sự tham gia của các giảng viên và chuyên gia đến từ doanh nghiệp.
            </p>
        </div>

        {{-- Mục tiêu và yêu cầu --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Mục tiêu & Yêu cầu</h2>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Tạo cơ hội để sinh viên rèn luyện kỹ năng thiết kế và phân tích cơ sở dữ liệu.</li>
                <li>Phát hiện và bồi dưỡng sinh viên có năng khiếu, đam mê về CSDL.</li>
                <li>Đảm bảo cuộc thi diễn ra khách quan, minh bạch và tiết kiệm.</li>
            </ul>
        </div>

        {{-- Thời gian & địa điểm --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Thời gian & Địa điểm</h2>
            <div class="bg-blue-50 border-l-4 border-blue-600 p-5 rounded-lg">
                <p class="mb-2"><strong>📅 Vòng Sơ khảo:</strong> 7h45 - 8h45, Chủ nhật ngày 07/12/2025 (Phòng B205, B401, B502)</p>
                <p class="mb-2"><strong>💻 Vòng Chung kết:</strong> 13h30 - 14h30, cùng ngày (Phòng A204, A209)</p>
                <p><strong>🎓 Đối tượng:</strong> Sinh viên năm 2 và năm 3 các ngành CNTT, ATTT, KH Dữ liệu.</p>
            </div>
        </div>

        {{-- Vòng thi --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Cấu trúc cuộc thi</h2>
            <div class="space-y-6">
                <div class="border rounded-xl p-6 hover:shadow-md transition">
                    <h3 class="font-semibold text-blue-700 text-lg mb-2">Vòng Sơ khảo</h3>
                    <p class="text-gray-600">Thi trắc nghiệm lý thuyết về mô hình dữ liệu, chuẩn hóa, SQL cơ bản. Hình thức cá nhân.</p>
                </div>
                <div class="border rounded-xl p-6 hover:shadow-md transition">
                    <h3 class="font-semibold text-blue-700 text-lg mb-2">Vòng Chung kết</h3>
                    <p class="text-gray-600">Thi thực hành thiết kế CSDL trên máy tính (PowerDesigner, SQL Server). Thí sinh làm việc nhóm.</p>
                </div>
            </div>
        </div>

        {{-- Giải thưởng --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Giải thưởng</h2>
            <ul class="space-y-3 text-gray-600">
                <li><i class="fa-solid fa-medal text-yellow-500 mr-2"></i>Giải Nhất: 1.000.000đ + Giấy khen</li>
                <li><i class="fa-solid fa-medal text-gray-400 mr-2"></i>Giải Nhì: 700.000đ + Giấy khen</li>
                <li><i class="fa-solid fa-medal text-amber-600 mr-2"></i>Giải Ba: 500.000đ + Giấy khen</li>
            </ul>
        </div>

        {{-- Ban giám khảo (rút gọn) --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Ban Giám khảo & Diễn giả</h2>
            <p class="text-gray-600 mb-4">
                Cuộc thi có sự tham gia chấm điểm và phản biện của các giảng viên Khoa CNTT cùng đại diện doanh nghiệp:
            </p>
            <ul class="text-gray-700 space-y-1">
                <li><strong>ThS. Nguyễn Thị Thanh Thủy</strong> – Trưởng Ban Giám khảo</li>
                <li><strong>Hồ Văn Lực</strong> – Giám đốc Công ty CP Tin học Đại Phát</li>
                <li><strong>Nguyễn Thanh Tài</strong> – Lead Consultant, Amaris Consulting</li>
            </ul>
        </div>
    </div>

    {{-- Sidebar --}}
    <aside class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 h-fit space-y-6">
        <h3 class="text-lg font-bold text-blue-700">Thông tin nhanh</h3>
        <ul class="text-gray-700 text-sm space-y-3">
            <li><i class="fa-regular fa-calendar text-blue-500 mr-2"></i><strong>Ngày:</strong> 07/12/2025</li>
            <li><i class="fa-regular fa-clock text-blue-500 mr-2"></i><strong>Giờ:</strong> 7h45 - 16h30</li>
            <li><i class="fa-solid fa-location-dot text-blue-500 mr-2"></i><strong>Địa điểm:</strong> Khu A & B - HUIT</li>
            <li><i class="fa-solid fa-user-tie text-blue-500 mr-2"></i><strong>Người phụ trách:</strong> ThS. Nguyễn Văn Lễ</li>
            <li><i class="fa-solid fa-users text-blue-500 mr-2"></i><strong>Đối tượng:</strong> Sinh viên CNTT (Năm 2 - 3)</li>
        </ul>

        <div class="pt-4 border-t border-gray-200">
            <a href="#" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold shadow">
                Đăng ký ngay
            </a>
        </div>
    </aside>
</section>

{{-- 💡 CTA --}}
<section class="bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-500 py-16 text-white text-center">
    <div class="container mx-auto px-6">
        <h3 class="text-3xl font-bold mb-4">Bạn đã sẵn sàng tham gia thử thách?</h3>
        <p class="text-blue-100 mb-8 text-lg">Tham gia để học hỏi, rèn luyện và khẳng định năng lực thiết kế cơ sở dữ liệu của bạn!</p>
        <a href="#" class="bg-white text-blue-700 px-10 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center gap-2">
            <i class="fa-solid fa-rocket"></i> Đăng ký ngay
        </a>
    </div>
</section>
@endsection
