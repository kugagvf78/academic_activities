@extends('layouts.client')
@section('title', 'Tạo Ban mới')

@section('content')
{{-- 🎯 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-purple-700 via-indigo-600 to-blue-500 text-white py-12 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Tạo ban mới</span>
        </div>
        <h1 class="text-3xl font-black">
            <i class="fas fa-user-plus mr-3"></i>
            Tạo Ban mới cho: {{ $cuocThi->tencuocthi }}
        </h1>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 60L60 55C120 50 240 40 360 35C480 30 600 30 720 32.5C840 35 960 40 1080 42.5C1200 45 1320 45 1380 45L1440 45V60H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- 📝 FORM TẠO BAN --}}
<section class="container mx-auto px-6 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Thông tin ban tổ chức</h2>
                        <p class="text-sm text-gray-600">Điền thông tin chi tiết cho ban mới</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('giangvien.phancong.ban.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="macuocthi" value="{{ $cuocThi->macuocthi }}">

                <div class="space-y-6">
                    {{-- Tên ban --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-purple-500 mr-2"></i>Tên ban
                            <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="tenban" 
                            id="tenban"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition @error('tenban') border-red-500 @enderror"
                            required>
                            <option value="">-- Chọn tên ban --</option>
                            <option value="Ban Học thuật" {{ old('tenban') == 'Ban Học thuật' ? 'selected' : '' }}>Ban Học thuật</option>
                            <option value="Ban Tổ chức" {{ old('tenban') == 'Ban Tổ chức' ? 'selected' : '' }}>Ban Tổ chức</option>
                            <option value="Ban Chuyên môn" {{ old('tenban') == 'Ban Chuyên môn' ? 'selected' : '' }}>Ban Chuyên môn</option>
                            <option value="Ban Giám khảo" {{ old('tenban') == 'Ban Giám khảo' ? 'selected' : '' }}>Ban Giám khảo</option>
                            <option value="Ban Đề thi" {{ old('tenban') == 'Ban Đề thi' ? 'selected' : '' }}>Ban Đề thi</option>
                            <option value="Ban Giám khảo Sơ khảo" {{ old('tenban') == 'Ban Giám khảo Sơ khảo' ? 'selected' : '' }}>Ban Giám khảo Sơ khảo</option>
                            <option value="Ban Giám khảo Chung kết" {{ old('tenban') == 'Ban Giám khảo Chung kết' ? 'selected' : '' }}>Ban Giám khảo Chung kết</option>
                            <option value="Ban Hậu cần" {{ old('tenban') == 'Ban Hậu cần' ? 'selected' : '' }}>Ban Hậu cần</option>
                            <option value="khac">Khác (Nhập tên tùy chỉnh)</option>
                        </select>
                        @error('tenban')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input tùy chỉnh (ẩn mặc định) --}}
                    <div id="customBanName" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-pencil-alt text-purple-500 mr-2"></i>Nhập tên ban tùy chỉnh
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="tenban_custom" 
                            id="tenban_custom"
                            value="{{ old('tenban_custom') }}"
                            placeholder="VD: Ban Kỹ thuật, Ban Truyền thông..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                    </div>

                    {{-- Mô tả --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-purple-500 mr-2"></i>Mô tả
                        </label>
                        <textarea 
                            name="mota" 
                            rows="5"
                            placeholder="Mô tả nhiệm vụ và trách nhiệm của ban..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition @error('mota') border-red-500 @enderror">{{ old('mota') }}</textarea>
                        @error('mota')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Thông tin cuộc thi --}}
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-purple-600"></i>
                            Thông tin cuộc thi
                        </h3>
                        <div class="grid md:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-trophy text-purple-500 mt-0.5"></i>
                                <div>
                                    <span class="text-gray-600">Tên cuộc thi:</span>
                                    <p class="font-medium text-gray-800">{{ $cuocThi->tencuocthi }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="far fa-calendar text-purple-500 mt-0.5"></i>
                                <div>
                                    <span class="text-gray-600">Thời gian:</span>
                                    <p class="font-medium text-gray-800">
                                        {{ $cuocThi->thoigianbatdau ? \Carbon\Carbon::parse($cuocThi->thoigianbatdau)->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Tạo ban
                    </button>
                    <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" 
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                        <i class="fas fa-times mr-2"></i>Hủy
                    </a>
                </div>
            </form>
        </div>

        {{-- Gợi ý --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">
                <i class="fas fa-lightbulb"></i>Danh sách tên ban có sẵn
            </h4>
            <div class="grid md:grid-cols-3 gap-2 text-sm">
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Học thuật
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Tổ chức
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Giám khảo
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Chuyên môn
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Giám khảo Sơ khảo
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Giám khảo Chung kết
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Đề thi
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Hậu cần Hackathon
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                    <i class="fas fa-check text-blue-500"></i>Ban Hậu cần
                </div>
            </div>
        </div>
    </div>
</section>

{{-- JavaScript để xử lý hiển thị input tùy chỉnh --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectBan = document.getElementById('tenban');
    const customBanDiv = document.getElementById('customBanName');
    const customBanInput = document.getElementById('tenban_custom');

    selectBan.addEventListener('change', function() {
        if (this.value === 'khac') {
            customBanDiv.classList.remove('hidden');
            customBanInput.required = true;
            selectBan.removeAttribute('name'); // Bỏ name của select
            customBanInput.setAttribute('name', 'tenban'); // Thêm name cho input custom
        } else {
            customBanDiv.classList.add('hidden');
            customBanInput.required = false;
            customBanInput.removeAttribute('name'); // Bỏ name của input custom
            selectBan.setAttribute('name', 'tenban'); // Thêm lại name cho select
        }
    });
});
</script>

@endsection