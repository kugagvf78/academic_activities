@extends('layouts.client')

@section('title', 'Tạo đề thi mới')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.dethi.index') }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Tạo đề thi</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    Tạo đề thi mới
                </h1>
                <p class="text-cyan-100">Điền thông tin để tạo đề thi cho cuộc thi</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <i class="fas fa-file-alt text-4xl mb-2"></i>
                        <div class="text-sm text-cyan-100">Đề thi mới</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section class="container mx-auto px-6 -mt-8 relative z-20 pb-12">
    {{-- Thông báo lỗi --}}
    @if($errors->any())
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-2xl mt-0.5"></i>
                <div class="flex-1">
                    <strong class="font-bold text-lg">Có lỗi xảy ra!</strong>
                    <ul class="mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle text-xs mt-1.5"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('giangvien.dethi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Cột trái - Thông tin chính --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Thông tin cơ bản --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-file-alt"></i>
                            Thông tin cơ bản
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        {{-- Tên đề thi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tên đề thi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                name="tendethi" 
                                value="{{ old('tendethi') }}"
                                placeholder="Nhập tên đề thi..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('tendethi') border-red-500 @enderror"
                                required>
                            @error('tendethi')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Cuộc thi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Cuộc thi <span class="text-red-500">*</span>
                            </label>
                            <select name="macuocthi" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('macuocthi') border-red-500 @enderror"
                                required>
                                <option value="">-- Chọn cuộc thi --</option>
                                @foreach($cuocthiList as $ct)
                                    <option value="{{ $ct->macuocthi }}" {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                        {{ $ct->tencuocthi }} ({{ $ct->loaicuocthi }})
                                    </option>
                                @endforeach
                            </select>
                            @error('macuocthi')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Loại đề thi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại đề thi <span class="text-red-500">*</span>
                            </label>
                            <select name="loaidethi" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('loaidethi') border-red-500 @enderror"
                                required>
                                <option value="">-- Chọn loại đề --</option>
                                <option value="LyThuyet" {{ old('loaidethi') == 'LyThuyet' ? 'selected' : '' }}>Lý thuyết</option>
                                <option value="ThucHanh" {{ old('loaidethi') == 'ThucHanh' ? 'selected' : '' }}>Thực hành</option>
                                <option value="VietBao" {{ old('loaidethi') == 'VietBao' ? 'selected' : '' }}>Viết báo</option>
                                <option value="Khac" {{ old('loaidethi') == 'Khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('loaidethi')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            {{-- Thời gian làm bài --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Thời gian làm bài (phút) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                    name="thoigianlambai" 
                                    value="{{ old('thoigianlambai', 60) }}"
                                    min="1" 
                                    max="999"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('thoigianlambai') border-red-500 @enderror"
                                    required>
                                @error('thoigianlambai')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Điểm tối đa --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Điểm tối đa <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                    name="diemtoida" 
                                    value="{{ old('diemtoida', 10) }}"
                                    min="0" 
                                    max="100"
                                    step="0.1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('diemtoida') border-red-500 @enderror"
                                    required>
                                @error('diemtoida')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File đính kèm --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-paperclip"></i>
                            File đề thi
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Tải lên file đề thi (tùy chọn)
                        </label>
                        <input type="file" 
                            name="file_dethi" 
                            accept=".pdf,.doc,.docx,.zip"
                            class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 hover:border-blue-400 transition @error('file_dethi') border-red-500 @enderror">
                        <div class="mt-3 p-4 bg-blue-50 rounded-xl">
                            <p class="text-sm text-gray-700 flex items-start gap-2">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <span>Định dạng: PDF, DOC, DOCX, ZIP. Tối đa 20MB</span>
                            </p>
                        </div>
                        @error('file_dethi')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Cột phải - Trạng thái & Hành động --}}
            <div class="space-y-6">
                {{-- Trạng thái --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-toggle-on"></i>
                            Trạng thái
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Trạng thái đề thi <span class="text-red-500">*</span>
                        </label>
                        <select name="trangthai" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('trangthai') border-red-500 @enderror"
                            required>
                            <option value="Draft" {{ old('trangthai', 'Draft') == 'Draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="Active" {{ old('trangthai') == 'Active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="Archived" {{ old('trangthai') == 'Archived' ? 'selected' : '' }}>Đã lưu trữ</option>
                        </select>
                        @error('trangthai')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>{{ $message }}
                            </p>
                        @enderror
                        
                        <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <p class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                <i class="fas fa-lightbulb text-yellow-500"></i>
                                Ghi chú:
                            </p>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs text-gray-400 mt-1.5"></i>
                                    <div><span class="font-semibold">Nháp:</span> Đề thi chưa công khai</div>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs text-gray-400 mt-1.5"></i>
                                    <div><span class="font-semibold">Hoạt động:</span> Sinh viên có thể làm bài</div>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs text-gray-400 mt-1.5"></i>
                                    <div><span class="font-semibold">Lưu trữ:</span> Đã kết thúc, chỉ xem</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Hành động --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cog text-blue-600"></i>
                        Hành động
                    </h2>
                    
                    <div class="space-y-3">
                        <button type="submit" 
                            class="w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Tạo đề thi
                        </button>
                        
                        <a href="{{ route('giangvien.dethi.index') }}" 
                            class="block w-full px-6 py-3.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg text-center">
                            <i class="fas fa-times mr-2"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </div>

                {{-- Hướng dẫn --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 shadow-md">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-yellow-500 text-xl"></i>
                        Hướng dẫn
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Chọn cuộc thi đã được phê duyệt</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Đặt tên rõ ràng cho đề thi</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Thiết lập thời gian hợp lý</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Upload file đề nếu cần thiết</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection