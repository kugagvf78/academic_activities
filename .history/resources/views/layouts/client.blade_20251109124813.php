<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Hội thảo - Khoa CNTT</title>

    {{-- TailwindCSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#0ea5e9',
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.2);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background: #3b82f6;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }
    </style>

    @stack('styles')
</head>

<body class="font-inter bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- HEADER / NAVBAR --}}
    <header class="bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto flex justify-between items-center py-5 px-6">
            <a href="{{ route('client.home') }}" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Hội thảo CNTT</h1>
                    <p class="text-xs text-gray-500">ĐH Công Thương TP.HCM</p>
                </div>
            </a>

            <nav class="hidden md:flex space-x-10 font-medium">
                <a href="{{ route('client.home') }}" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-home mr-2"></i>Trang chủ
                </a>
                <a href="{{ route('client.events') }}" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-calendar-days mr-2"></i>Hội thảo
                </a>
                <a href="#" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-newspaper mr-2"></i>Tin tức
                </a>
                <a href="#contact" class="nav-link text-gray-700 hover:text-primary transition py-2">
                    <i class="fa-solid fa-envelope mr-2"></i>Liên hệ
                </a>
            </nav>

            <div class="flex items-center space-x-3">
                @auth
                    <div class="flex items-center space-x-3">
                        <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-lg">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary to-accent rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-white text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">User Name</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white hover:shadow-lg hover:scale-105 transition text-sm font-semibold">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-primary to-secondary text-white hover:shadow-xl hover:scale-105 transition text-sm font-semibold">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- MAIN --}}
    <main class="flex-1">
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
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-primary text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-accent rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Hội thảo CNTT</h3>
                            <p class="text-xs text-gray-400">ĐH Công Thương</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Nền tảng hỗ trợ tổ chức và quản lý hội thảo học thuật chuyên nghiệp
                    </p>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Liên kết nhanh</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-accent transition">Trang chủ</a></li>
                        <li><a href="#" class="hover:text-accent transition">Hội thảo</a></li>
                        <li><a href="#" class="hover:text-accent transition">Tin tức</a></li>
                        <li><a href="#" class="hover:text-accent transition">Liên hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Hỗ trợ</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-accent transition">Hướng dẫn sử dụng</a></li>
                        <li><a href="#" class="hover:text-accent transition">Câu hỏi thường gặp</a></li>
                        <li><a href="#" class="hover:text-accent transition">Chính sách</a></li>
                        <li><a href="#" class="hover:text-accent transition">Điều khoản</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Kết nối với chúng tôi</h4>
                    <div class="flex space-x-3 mb-4">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-accent rounded-lg flex items-center justify-center transition">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                    </div>
                    <p class="text-sm text-gray-400">
                        <i class="fa-solid fa-envelope mr-2"></i>
                        cntt@hutech.edu.vn
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-8 text-center">
                <p class="text-sm text-gray-400">
                    © {{ date('Y') }} <span class="font-semibold text-white">Khoa Công nghệ Thông tin</span> - Đại học Công Thương TP.HCM
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Hệ thống Quản lý Hội thảo Học thuật | Phát triển bởi Sinh viên CNTT
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>