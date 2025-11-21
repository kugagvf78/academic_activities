@extends('layouts.client')

@section('title', 'Tạo cuộc thi mới')

@section('content')
<section class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('giangvien.cuocthi.index') }}" 
                class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Tạo cuộc thi mới</h1>
        </div>
        <p class="text-gray-600 ml-10">Điền thông tin để tạo cuộc thi mới</p>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-lg shadow-md p-8">
        <form method="POST" action="{{ route('giangvien.cuocthi.store') }}" class="space-y-6">
            @csrf

            {{-- Tên cuộc thi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tên cuộc thi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="tencuocthi" value="{{ old('tencuocthi') }}" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="VD: Cuộc thi Lập trình 2024">
                @error('tencuocthi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Loại cuộc thi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Loại cuộc thi <span class="text-red-500">*</span>
                </label>
                <select name="loaicuocthi" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn loại cuộc thi --</option>
                    
                    <option value="CuocThi" {{ old('loaicuocthi') == 'CuocThi' ? 'selected' : '' }}>
                        Cuộc thi
                    </option>
                    
                    <option value="HoiThao" {{ old('loaicuocthi') == 'HoiThao' ? 'selected' : '' }}>
                        Hội thảo
                    </option>
                </select>
                @error('loaicuocthi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Mô tả cuộc thi
                </label>
                <textarea name="mota" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Mô tả chi tiết về cuộc thi...">{{ old('mota') }}</textarea>
                @error('mota')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mục đích --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Mục đích
                </label>
                <textarea name="mucdich" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Mục đích tổ chức cuộc thi...">{{ old('mucdich') }}</textarea>
                @error('mucdich')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Đối tượng tham gia --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Đối tượng tham gia
                </label>
                <input type="text" name="doituongthamgia" value="{{ old('doituongthamgia') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="VD: Sinh viên năm 2, 3, 4">
                @error('doituongthamgia')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Thời gian --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Thời gian bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="thoigianbatdau" value="{{ old('thoigianbatdau') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('thoigianbatdau')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Thời gian kết thúc <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="thoigianketthuc" value="{{ old('thoigianketthuc') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('thoigianketthuc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Địa điểm --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Địa điểm
                </label>
                <input type="text" name="diadiem" value="{{ old('diadiem') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="VD: Hội trường A, Tòa nhà B">
                @error('diadiem')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hình thức tham gia --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Hình thức tham gia
                </label>
                <select name="hinhthucthamgia"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn hình thức --</option>
                    <option value="CaNhan" {{ old('hinhthucthamgia') == 'CaNhan' ? 'selected' : '' }}>Cá nhân</option>
                    <option value="DoiNhom" {{ old('hinhthucthamgia') == 'DoiNhom' ? 'selected' : '' }}>Đội nhóm</option>
                    <option value="CaHai" {{ old('hinhthucthamgia') == 'CaHai' ? 'selected' : '' }}>Cả hai</option>
                </select>
                @error('hinhthucthamgia')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số lượng thành viên (nếu đội nhóm) --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Số lượng thành viên/đội (nếu có)
                </label>
                <input type="number" name="soluongthanhvien" value="{{ old('soluongthanhvien') }}"
                    min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="VD: 3">
                @error('soluongthanhvien')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dự trù kinh phí --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Dự trù kinh phí (VNĐ)
                </label>
                <input type="number" name="dutrukinhphi" value="{{ old('dutrukinhphi') }}"
                    min="0" step="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="VD: 50000000">
                @error('dutrukinhphi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-4 pt-4">
                <button type="submit" 
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Tạo cuộc thi
                </button>
                <a href="{{ route('giangvien.cuocthi.index') }}" 
                    class="px-8 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</section>
@endsection