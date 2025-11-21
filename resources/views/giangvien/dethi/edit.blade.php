@extends('layouts.client')

@section('title', 'Chỉnh sửa đề thi - ' . $dethi->tendethi)

@section('content')
<section class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('giangvien.dethi.show', $dethi->madethi) }}" 
                class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Chỉnh sửa đề thi</h1>
        </div>
        <p class="text-gray-600 ml-8">Cập nhật thông tin đề thi: {{ $dethi->tendethi }}</p>
    </div>

    {{-- Thông báo lỗi --}}
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Có lỗi xảy ra!</strong>
            <ul class="mt-2 ml-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-600"></i>
                        Thông tin cơ bản
                    </h2>
                    
                    <div class="space-y-4">
                        {{-- Mã đề thi (readonly) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Mã đề thi
                            </label>
                            <input type="text" 
                                value="{{ $dethi->madethi }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly>
                            <p class="mt-1 text-sm text-gray-500">
                                <i class="fas fa-info-circle"></i>
                                Mã đề thi không thể thay đổi
                            </p>
                        </div>

                        {{-- Tên đề thi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tên đề thi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                name="tendethi" 
                                value="{{ old('tendethi', $dethi->tendethi) }}"
                                placeholder="Nhập tên đề thi..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tendethi') border-red-500 @enderror"
                                required>
                            @error('tendethi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cuộc thi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cuộc thi <span class="text-red-500">*</span>
                            </label>
                            <select name="macuocthi" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('macuocthi') border-red-500 @enderror"
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
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Loại đề thi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Loại đề thi <span class="text-red-500">*</span>
                            </label>
                            <select name="loaidethi" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('loaidethi') border-red-500 @enderror"
                                required>
                                <option value="">-- Chọn loại đề --</option>
                                <option value="LyThuyet" {{ old('loaidethi', $dethi->loaidethi) == 'LyThuyet' ? 'selected' : '' }}>Lý thuyết</option>
                                <option value="ThucHanh" {{ old('loaidethi', $dethi->loaidethi) == 'ThucHanh' ? 'selected' : '' }}>Thực hành</option>
                                <option value="VietBao" {{ old('loaidethi', $dethi->loaidethi) == 'VietBao' ? 'selected' : '' }}>Viết báo</option>
                                <option value="Khac" {{ old('loaidethi', $dethi->loaidethi) == 'Khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('loaidethi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            {{-- Thời gian làm bài --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Thời gian làm bài (phút) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                    name="thoigianlambai" 
                                    value="{{ old('thoigianlambai', $dethi->thoigianlambai) }}"
                                    min="1" 
                                    max="999"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('thoigianlambai') border-red-500 @enderror"
                                    required>
                                @error('thoigianlambai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Điểm tối đa --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Điểm tối đa <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                    name="diemtoida" 
                                    value="{{ old('diemtoida', $dethi->diemtoida) }}"
                                    min="0" 
                                    max="100"
                                    step="0.1"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('diemtoida') border-red-500 @enderror"
                                    required>
                                @error('diemtoida')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File đính kèm --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-paperclip text-blue-600"></i>
                        File đề thi
                    </h2>
                    
                    {{-- File hiện tại --}}
                    @if($dethi->filedethi)
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    <div>
                                        <p class="font-medium text-gray-800">File đề thi hiện tại</p>
                                        <p class="text-sm text-gray-600">{{ basename($dethi->filedethi) }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $dethi->filedethi) }}" 
                                    target="_blank"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                    <i class="fas fa-download mr-2"></i>Tải xuống
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $dethi->filedethi ? 'Thay đổi file đề thi (tùy chọn)' : 'Tải lên file đề thi (tùy chọn)' }}
                        </label>
                        <input type="file" 
                            name="file_dethi" 
                            accept=".pdf,.doc,.docx,.zip"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('file_dethi') border-red-500 @enderror">
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i>
                            Định dạng: PDF, DOC, DOCX, ZIP. Tối đa 20MB
                            @if($dethi->filedethi)
                                <br>Tải file mới sẽ thay thế file hiện tại
                            @endif
                        </p>
                        @error('file_dethi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Cột phải - Trạng thái & Hành động --}}
            <div class="space-y-6">
                {{-- Trạng thái --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        Trạng thái
                    </h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Trạng thái đề thi <span class="text-red-500">*</span>
                        </label>
                        <select name="trangthai" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('trangthai') border-red-500 @enderror"
                            required>
                            <option value="Draft" {{ old('trangthai', $dethi->trangthai) == 'Draft' ? 'selected' : '' }}>Nháp</option>
                            <option value="Active" {{ old('trangthai', $dethi->trangthai) == 'Active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="Archived" {{ old('trangthai', $dethi->trangthai) == 'Archived' ? 'selected' : '' }}>Đã lưu trữ</option>
                        </select>
                        @error('trangthai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-3 p-3 bg-blue-50 rounded-lg text-sm text-gray-700">
                            <p class="font-medium mb-1">Ghi chú:</p>
                            <ul class="space-y-1 text-xs">
                                <li><span class="font-semibold">Nháp:</span> Đề thi chưa công khai</li>
                                <li><span class="font-semibold">Hoạt động:</span> Sinh viên có thể làm bài</li>
                                <li><span class="font-semibold">Lưu trữ:</span> Đã kết thúc, chỉ xem</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Hành động --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cog text-blue-600"></i>
                        Hành động
                    </h2>
                    
                    <div class="space-y-3">
                        <button type="submit" 
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Lưu thay đổi
                        </button>
                        
                        <a href="{{ route('giangvien.dethi.show', $dethi->madethi) }}" 
                            class="w-full px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </div>

                {{-- Thông tin --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-yellow-800 mb-2">Lưu ý quan trọng</h3>
                            <ul class="space-y-1 text-sm text-yellow-700">
                                <li>• Kiểm tra kỹ thông tin trước khi lưu</li>
                                <li>• Thay đổi có thể ảnh hưởng đến sinh viên</li>
                                <li>• File cũ sẽ bị xóa khi tải file mới</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Thông tin thêm --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Thông tin
                    </h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-user text-gray-400 mt-0.5"></i>
                            <div>
                                <span class="text-gray-500">Người tạo:</span>
                                <p class="font-medium text-gray-800"><strong>{{ $dethi->nguoitao_ten }}</strong></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fas fa-calendar text-gray-400 mt-0.5"></i>
                            <div>
                                <span class="text-gray-500">Ngày tạo:</span>
                                <p class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($dethi->ngaytao)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection