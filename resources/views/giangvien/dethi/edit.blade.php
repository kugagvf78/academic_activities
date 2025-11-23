@extends('layouts.client')

@section('title', 'Chỉnh sửa đề thi - ' . $dethi->tendethi)

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
                    <a href="{{ route('giangvien.dethi.show', $dethi->madethi) }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Chỉnh sửa đề thi</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    Chỉnh sửa đề thi
                </h1>
                <p class="text-cyan-100">{{ $dethi->tendethi }}</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <i class="fas fa-edit text-4xl mb-2"></i>
                        <div class="text-sm text-cyan-100">Cập nhật</div>
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
    <form action="{{ route('giangvien.dethi.update', $dethi->madethi) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
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
                        {{-- Mã đề thi (readonly) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Mã đề thi
                            </label>
                            <input type="text" 
                                value="{{ $dethi->madethi }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-600 cursor-not-allowed"
                                readonly>
                            <p class="mt-2 text-sm text-gray-500 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>
                                Mã đề thi không thể thay đổi
                            </p>
                        </div>

                        {{-- Tên đề thi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tên đề thi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                name="tendethi" 
                                value="{{ old('tendethi', $dethi->tendethi) }}"
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
                                    <option value="{{ $ct->macuocthi }}" 
                                        {{ old('macuocthi', $dethi->macuocthi) == $ct->macuocthi ? 'selected' : '' }}>
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
                                <option value="LyThuyet" {{ old('loaidethi', $dethi->loaidethi) == 'LyThuyet' ? 'selected' : '' }}>Lý thuyết</option>
                                <option value="ThucHanh" {{ old('loaidethi', $dethi->loaidethi) == 'ThucHanh' ? 'selected' : '' }}>Thực hành</option>
                                <option value="VietBao" {{ old('loaidethi', $dethi->loaidethi) == 'VietBao' ? 'selected' : '' }}>Viết báo</option>
                                <option value="Khac" {{ old('loaidethi', $dethi->loaidethi) == 'Khac' ? 'selected' : '' }}>Khác</option>
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
                                    value="{{ old('thoigianlambai', $dethi->thoigianlambai) }}"
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
                                    value="{{ old('diemtoida', $dethi->diemtoida) }}"
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
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-paperclip"></i>
                            File đề thi
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        @if($dethi->filedethi)
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                                        <div>
                                            <p class="font-semibold text-gray-800">File đề thi hiện tại</p>
                                            <p class="text-sm text-gray-600">{{ basename($dethi->filedethi) }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $dethi->filedethi) }}" 
                                        target="_blank"
                                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-sm font-semibold shadow-md hover:shadow-lg">
                                        <i class="fas fa-download mr-2"></i>Tải xuống
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                {{ $dethi->filedethi ? 'Thay đổi file đề thi (tùy chọn)' : 'Tải lên file đề thi (tùy chọn)' }}
                            </label>
                            <input type="file" 
                                name="file_dethi" 
                                accept=".pdf,.doc,.docx,.zip"
                                class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 hover:border-blue-400 transition @error('file_dethi') border-red-500 @enderror">
                            <div class="mt-3 p-4 bg-blue-50 rounded-xl">
                                <p class="text-sm text-gray-700 flex items-start gap-2">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                    <span>
                                        Định dạng: PDF, DOC, DOCX, ZIP. Tối đa 20MB
                                        @if($dethi->filedethi)
                                            <br>Tải file mới sẽ thay thế file hiện tại
                                        @endif
                                    </span>
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
            </div>

            {{-- Cột phải - Hành động & Thông tin --}}
            <div class="space-y-6">
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
                            Lưu thay đổi
                        </button>
                        
                        <a href="{{ route('giangvien.dethi.show', $dethi->madethi) }}" 
                            class="block w-full px-6 py-3.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg text-center">
                            <i class="fas fa-times mr-2"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </div>

                {{-- Cảnh báo --}}
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-300 rounded-2xl p-6 shadow-md">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mt-0.5"></i>
                        <div>
                            <h3 class="font-bold text-yellow-800 mb-3">Lưu ý quan trọng</h3>
                            <ul class="space-y-2 text-sm text-yellow-700">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs mt-1.5"></i>
                                    <span>Kiểm tra kỹ thông tin trước khi lưu</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs mt-1.5"></i>
                                    <span>Thay đổi có thể ảnh hưởng đến sinh viên</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs mt-1.5"></i>
                                    <span>File cũ sẽ bị xóa khi tải file mới</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Thông tin thêm --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Thông tin
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-user text-blue-500 text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Người tạo</span>
                                    <p class="font-bold text-gray-800 mt-1">{{ $dethi->nguoitao_ten }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-calendar text-blue-500 text-lg mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Ngày tạo</span>
                                    <p class="font-bold text-gray-800 mt-1">
                                        {{ \Carbon\Carbon::parse($dethi->ngaytao)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection