@extends('layouts.client')
@section('title', 'Trang chủ')

@section('content')
{{-- HERO SECTION --}}
        <section class="relative bg-gradient-to-br from-primary via-secondary to-accent gradient-animate text-white py-32 overflow-hidden">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white/5 rounded-full blur-2xl animate-float" style="animation-delay: 4s;"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-block mb-6">
                        <span class="glass-effect px-6 py-2 rounded-full text-sm font-semibold inline-flex items-center space-x-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span>Đang hoạt động</span>
                        </span>
                    </div>
                    
                    <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
                        Hệ thống Quản lý <br>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-cyan-100">
                            Hội thảo Học thuật
                        </span>
                    </h1>
                    
                    <p class="text-xl text-blue-50 mb-10 max-w-3xl mx-auto leading-relaxed font-light">
                        Nền tảng toàn diện hỗ trợ sinh viên và giảng viên Khoa CNTT trong việc tổ chức, 
                        tham gia và quản lý các hoạt động hội thảo học thuật chuyên nghiệp
                    </p>
                    
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('client.events') }}" class="group px-8 py-4 bg-white text-primary font-bold rounded-full shadow-2xl hover:shadow-3xl hover:scale-105 transition inline-flex items-center space-x-2">
                            <span>Khám phá Hội thảo</span>
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                        </a>
                        <a href="#about" class="px-8 py-4 glass-effect text-white font-semibold rounded-full hover:bg-white/20 transition inline-flex items-center space-x-2">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Tìm hiểu thêm</span>
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8 mt-16 max-w-2xl mx-auto">
                        <div class="glass-effect rounded-2xl p-6 card-hover">
                            <div class="text-4xl font-bold mb-2">150+</div>
                            <div class="text-blue-100 text-sm">Hội thảo</div>
                        </div>
                        <div class="glass-effect rounded-2xl p-6 card-hover">
                            <div class="text-4xl font-bold mb-2">2,500+</div>
                            <div class="text-blue-100 text-sm">Sinh viên</div>
                        </div>
                        <div class="glass-effect rounded-2xl p-6 card-hover">
                            <div class="text-4xl font-bold mb-2">80+</div>
                            <div class="text-blue-100 text-sm">Giảng viên</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ABOUT SECTION --}}
        <section id="about" class="container mx-auto py-24 px-6">
            <div class="text-center mb-16">
                <span class="text-accent font-semibold text-sm uppercase tracking-wider">Về chúng tôi</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-3 mb-6">
                    Nền tảng <span class="text-gradient">Học thuật Hiện đại</span>
                </h2>
                <p class="max-w-3xl mx-auto text-gray-600 text-lg leading-relaxed">
                    Hệ thống được phát triển với mục tiêu tạo ra môi trường học thuật năng động, 
                    nơi sinh viên và giảng viên có thể dễ dàng tổ chức, đăng ký và quản lý các buổi hội thảo khoa học.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mt-16">
                <div class="group bg-white shadow-xl rounded-2xl p-8 card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fa-solid fa-lightbulb text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Phát triển Tri thức</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Khám phá và học hỏi từ các chuyên gia hàng đầu trong lĩnh vực công nghệ thông tin và khoa học máy tính.
                    </p>
                </div>

                <div class="group bg-white shadow-xl rounded-2xl p-8 card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fa-solid fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Kết nối Học thuật</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Tạo cơ hội giao lưu, chia sẻ kinh nghiệm và hợp tác nghiên cứu giữa sinh viên và giảng viên.
                    </p>
                </div>

                <div class="group bg-white shadow-xl rounded-2xl p-8 card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fa-solid fa-award text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Khẳng định Năng lực</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Tham gia và đạt giải trong các hội thảo giúp sinh viên khẳng định năng lực và nổi bật hơn.
                    </p>
                </div>
            </div>

            {{-- Features Grid --}}
            <div class="mt-20 grid md:grid-cols-2 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 border border-blue-100">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow">
                            <i class="fa-solid fa-calendar-check text-primary text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2 text-gray-800">Quản lý Linh hoạt</h4>
                            <p class="text-gray-600">Dễ dàng đăng ký, theo dõi và quản lý lịch trình hội thảo của bạn</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 border border-cyan-100">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow">
                            <i class="fa-solid fa-bell text-accent text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2 text-gray-800">Thông báo Thông minh</h4>
                            <p class="text-gray-600">Nhận thông báo tự động về các sự kiện và cập nhật quan trọng</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-8 border border-indigo-100">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow">
                            <i class="fa-solid fa-chart-line text-primary text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2 text-gray-800">Theo dõi Tiến độ</h4>
                            <p class="text-gray-600">Xem thống kê và đánh giá hoạt động tham gia của bạn</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow">
                            <i class="fa-solid fa-certificate text-accent text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2 text-gray-800">Chứng nhận Điện tử</h4>
                            <p class="text-gray-600">Nhận chứng chỉ tham dự và thành tích ngay trên hệ thống</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- CONTACT SECTION --}}
        <section id="contact" class="bg-gradient-to-br from-gray-50 to-blue-50 py-24">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="text-accent font-semibold text-sm uppercase tracking-wider">Liên hệ</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-3 mb-6">
                        Kết nối với <span class="text-gradient">Chúng tôi</span>
                    </h2>
                    <p class="max-w-2xl mx-auto text-gray-600 text-lg">
                        Có câu hỏi hoặc cần hỗ trợ? Chúng tôi luôn sẵn sàng lắng nghe và giúp đỡ bạn
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-12 max-w-6xl mx-auto">
                    <div class="space-y-8">
                        <div class="flex items-start space-x-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-2 text-gray-800">Địa chỉ</h4>
                                <p class="text-gray-600">Khoa Công nghệ Thông tin<br>Đại học Công Thương TP.HCM<br>140 Lê Trọng Tấn, Tây Thạnh, Tân Phú, TP.HCM</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i class="fa-solid fa-phone text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-2 text-gray-800">Điện thoại</h4>
                                <p class="text-gray-600">+84 (28) 3816 5673<br>+84 (28) 3816 5674</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i class="fa-solid fa-envelope text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-2 text-gray-800">Email</h4>
                                <p class="text-gray-600">cntt@hutech.edu.vn<br>hoithao.cntt@hutech.edu.vn</p>
                            </div>
                        </div>
                    </div>

                    <form class="bg-white shadow-2xl rounded-2xl p-8 border border-gray-100">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên</label>
                                <input type="text" placeholder="Nhập họ và tên của bạn" 
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" placeholder="example@email.com" 
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại</label>
                                <input type="tel" placeholder="0912 345 678" 
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung</label>
                                <textarea rows="4" placeholder="Nhập nội dung bạn muốn gửi..." 
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none"></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white px-6 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition font-bold text-lg">
                                <i class="fa-solid fa-paper-plane mr-2"></i>
                                Gửi liên hệ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

</section>
@endsection