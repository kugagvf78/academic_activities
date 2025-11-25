@extends('layouts.client')

@section('title', 'Tạo Hoạt động Hỗ trợ')

@section('content')
<div class="container mx-auto px-6 py-8">
    {{-- Back button --}}
    <div class="mb-6">
        <a href="{{ route('giangvien.hoatdong.index') }}" 
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 font-semibold transition">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-800 mb-2">Tạo Hoạt động Hỗ trợ</h1>
        <p class="text-gray-600">Tạo hoạt động cổ vũ hoặc hỗ trợ kỹ thuật cho cuộc thi</p>
    </div>

    {{-- Thông báo lỗi --}}
    @if($errors->any())
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 text-xl mt-1"></i>
                <div class="flex-1">
                    <p class="font-bold text-red-800 mb-2">Có lỗi xảy ra:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('giangvien.hoatdong.store') }}" method="POST" class="max-w-4xl">
        @csrf

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 space-y-6">
            
            {{-- Tên hoạt động --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Tên hoạt động <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                    name="tenhoatdong" 
                    value="{{ old('tenhoatdong') }}"
                    placeholder="VD: Cổ vũ vòng chung kết" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition @error('tenhoatdong') border-red-500 @enderror">
                @error('tenhoatdong')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cuộc thi --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Cuộc thi <span class="text-red-500">*</span>
                </label>
                <select name="macuocthi" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition @error('macuocthi') border-red-500 @enderror">
                    <option value="">-- Chọn cuộc thi --</option>
                    @foreach($cuocthis as $ct)
                        <option value="{{ $ct->macuocthi }}" {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                            {{ $ct->tencuocthi }} ({{ $ct->thoigianbatdau->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('macuocthi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Loại hoạt động --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Loại hoạt động <span class="text-red-500">*</span>
                    </label>
                    <select name="loaihoatdong" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition @error('loaihoatdong') border-red-500 @enderror">
                        <option value="">-- Chọn loại --</option>
                        <option value="CoVu" {{ old('loaihoatdong') == 'CoVu' ? 'selected' : '' }}>Cổ vũ</option>
                        <option value="HoTroKyThuat" {{ old('loaihoatdong') == 'HoTroKyThuat' ? 'selected' : '' }}>Hỗ trợ Kỹ thuật</option>
                    </select>
                    @error('loaihoatdong')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Số lượng --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Số lượng <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                        name="soluong" 
                        value="{{ old('soluong', 50) }}"
                        min="1"
                        placeholder="Số lượng sinh viên" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition @error('soluong') border-red-500 @enderror">
                    @error('soluong')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Thời gian bắt đầu --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Thời gian bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                        name="thoigianbatdau" 
                        value="{{ old('thoigianbatdau') }}"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition @error('thoigianbatdau') border-red-500 @enderror">
                    @error('thoigianbatdau')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thời gian kết thúc --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Thời gian kết thúc <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                        name="thoigianketthuc" 
                        value="{{ old('thoigianketthuc') }}"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition @error('thoigianketthuc') border-red-500 @enderror">
                    @error('thoigianketthuc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Địa điểm --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Địa điểm
                    </label>
                    <input type="text" 
                        name="diadiem" 
                        value="{{ old('diadiem') }}"
                        placeholder="VD: Hội trường A" 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition">
                </div>

                {{-- Điểm rèn luyện --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Điểm rèn luyện
                    </label>
                    <input type="number" 
                        name="diemrenluyen" 
                        value="{{ old('diemrenluyen', 2) }}"
                        step="0.5"
                        min="0"
                        placeholder="VD: 2.0" 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition">
                </div>
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Mô tả
                </label>
                <textarea name="mota" 
                    rows="4"
                    placeholder="Mô tả chi tiết về hoạt động..."
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition resize-none">{{ old('mota') }}</textarea>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4 pt-6 border-t">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-bold hover:from-purple-700 hover:to-pink-700 transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>Tạo hoạt động
                </button>
                <a href="{{ route('giangvien.hoatdong.index') }}" 
                    class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">
                    Hủy
                </a>
            </div>
        </div>
    </form>
</div>
@endsection