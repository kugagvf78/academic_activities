@extends('layouts.client')
@section('title', 'Chỉnh sửa Ban')

@section('content')
{{-- 🎯 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-orange-500 to-red-500 text-white py-12 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Chỉnh sửa ban</span>
        </div>
        <h1 class="text-3xl font-black">
            <i class="fas fa-edit mr-3"></i>
            Chỉnh sửa: {{ $ban->tenban }}
        </h1>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 60L60 55C120 50 240 40 360 35C480 30 600 30 720 32.5C840 35 960 40 1080 42.5C1200 45 1320 45 1380 45L1440 45V60H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- 📝 FORM SỬA BAN --}}
<section class="container mx-auto px-6 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Chỉnh sửa thông tin ban</h2>
                        <p class="text-sm text-gray-600">Cập nhật thông tin chi tiết cho ban</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('giangvien.phancong.ban.update', $ban->maban) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Tên ban --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-amber-500 mr-2"></i>Tên ban
                            <span class="text-red-500">*</span>
                        </label>
                        @php
                            $danhSachBan = [
                                'Ban Học thuật',
                                'Ban Tổ chức',
                                'Ban Chuyên môn',
                                'Ban Giám khảo',
                                'Ban Đề thi',
                                'Ban Giám khảo Sơ khảo',
                                'Ban Giám khảo Chung kết',
                                'Ban Hậu cần'
                            ];
                            $currentTenBan = old('tenban', $ban->tenban);
                            $isCustomName = !in_array($currentTenBan, $danhSachBan);
                        @endphp
                        
                        <select 
                            name="tenban" 
                            id="tenban"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition @error('tenban') border-red-500 @enderror"
                            required>
                            <option value="">-- Chọn tên ban --</option>
                            @foreach($danhSachBan as $tenBan)
                                <option value="{{ $tenBan }}" {{ $currentTenBan == $tenBan ? 'selected' : '' }}>
                                    {{ $tenBan }}
                                </option>
                            @endforeach
                            <option value="khac" {{ $isCustomName ? 'selected' : '' }}>Khác (Nhập tên tùy chỉnh)</option>
                        </select>
                        @error('tenban')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input tùy chỉnh --}}
                    <div id="customBanName" class="{{ $isCustomName ? '' : 'hidden' }}">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-pencil-alt text-amber-500 mr-2"></i>Nhập tên ban tùy chỉnh
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="tenban_custom" 
                            id="tenban_custom"
                            value="{{ $isCustomName ? $currentTenBan : old('tenban_custom') }}"
                            placeholder="VD: Ban Kỹ thuật, Ban Truyền thông..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    </div>

                    {{-- Mô tả --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-amber-500 mr-2"></i>Mô tả
                        </label>
                        <textarea 
                            name="mota" 
                            rows="5"
                            placeholder="Mô tả nhiệm vụ và trách nhiệm của ban..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition @error('mota') border-red-500 @enderror">{{ old('mota', $ban->mota) }}</textarea>
                        @error('mota')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Thông tin ban --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-amber-600"></i>
                            Thông tin hiện tại
                        </h3>
                        <div class="grid md:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-hashtag text-amber-500 mt-0.5"></i>
                                <div>
                                    <span class="text-gray-600">Mã ban:</span>
                                    <p class="font-medium text-gray-800">{{ $ban->maban }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-trophy text-amber-500 mt-0.5"></i>
                                <div>
                                    <span class="text-gray-600">Cuộc thi:</span>
                                    <p class="font-medium text-gray-800">{{ $ban->cuocthi->tencuocthi ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-users text-amber-500 mt-0.5"></i>
                                <div>
                                    <span class="text-gray-600">Số giảng viên:</span>
                                    <p class="font-medium text-gray-800">{{ $ban->phancongs->count() }} người</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="far fa-calendar text-amber-500 mt-0.5"></i>
                                <div>
                                    <span class="text-gray-600">Thời gian cuộc thi:</span>
                                    <p class="font-medium text-gray-800">
                                        {{ $ban->cuocthi->thoigianbatdau ? \Carbon\Carbon::parse($ban->cuocthi->thoigianbatdau)->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cảnh báo nếu có phân công --}}
                    @if($ban->phancongs->count() > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="text-sm">
                            <p class="font-semibold text-blue-800 mb-1">Lưu ý quan trọng</p>
                            <p class="text-blue-700">
                                Ban này đang có <span class="font-bold">{{ $ban->phancongs->count() }} giảng viên</span> được phân công. 
                                Việc thay đổi thông tin sẽ ảnh hưởng đến các phân công hiện tại.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Lưu thay đổi
                    </button>
                    <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" 
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                        <i class="fas fa-times mr-2"></i>Hủy
                    </a>
                </div>
            </form>
        </div>

        {{-- Danh sách giảng viên được phân công --}}
        @if($ban->phancongs->count() > 0)
        <div class="mt-6 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200 p-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-indigo-600"></i>
                    Giảng viên được phân công ({{ $ban->phancongs->count() }})
                </h3>
            </div>
            <div class="p-4">
                <div class="grid md:grid-cols-2 gap-3">
                    @foreach($ban->phancongs as $phancong)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800 truncate">
                                {{ $phancong->giangvien->nguoiDung->hoten ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $phancong->vaitro }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
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

    // Kiểm tra ngay khi load trang
    if (selectBan.value === 'khac') {
        customBanInput.setAttribute('name', 'tenban');
        selectBan.removeAttribute('name');
    }
});
</script>

@endsection