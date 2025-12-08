@extends('layouts.client')
@section('title', 'Chỉnh sửa Ban')

@section('content')
{{-- 🎯 HERO SECTION: Phong cách Academic --}}
<section class="relative bg-gradient-to-br from-indigo-900 to-blue-800 text-white pt-20 pb-24 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="container mx-auto px-6 relative z-10">
        {{-- Breadcrumb --}}
        <div class="mb-5 text-sm font-medium text-blue-200 flex items-center gap-2">
            <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" class="hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quản lý Ban
            </a>
            <span class="text-white/40">/</span>
            <span class="text-white">Chỉnh sửa</span>
        </div>

        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">
            <i class="fas fa-edit mr-3 text-cyan-400"></i>
            Cập nhật Ban Chuyên môn
        </h1>
        <p class="text-indigo-200 text-lg font-light max-w-2xl">
            Chỉnh sửa thông tin cho: <strong class="text-white">{{ $ban->tenban }}</strong>
        </p>
    </div>

    {{-- Đường cắt hiện đại --}}
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-white" style="clip-path: polygon(0% 100%, 100% 0, 100% 100%, 0% 100%);"></div>
</section>

{{-- 📝 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-12 -mt-20 relative z-20">
    <div class="max-w-5xl mx-auto grid lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN: FORM EDIT (Chiếm 2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                {{-- Header Form --}}
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200 p-6 flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md">
                        <i class="fas fa-pen"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Thông tin cập nhật</h2>
                        <p class="text-sm text-gray-500">Thay đổi các thông tin cần thiết bên dưới.</p>
                    </div>
                </div>

                {{-- Form Body --}}
                <form action="{{ route('giangvien.phancong.ban.update', $ban->maban) }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- 1. Tên Ban --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-indigo-500 mr-1"></i> Tên Ban <span class="text-red-500">*</span>
                        </label>
                        @php
                            $danhSachBan = [
                                'Ban Học thuật', 'Ban Tổ chức', 'Ban Chuyên môn', 
                                'Ban Giám khảo', 'Ban Đề thi', 'Ban Giám khảo Sơ khảo',
                                'Ban Giám khảo Chung kết', 'Ban Hậu cần'
                            ];
                            $currentTenBan = old('tenban', $ban->tenban);
                            $isCustomName = !in_array($currentTenBan, $danhSachBan) && !empty($currentTenBan);
                        @endphp
                        
                        <div class="relative">
                            <select name="tenban" id="tenban" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm bg-white cursor-pointer appearance-none">
                                <option value="">-- Chọn tên ban --</option>
                                @foreach($danhSachBan as $tenBan)
                                    <option value="{{ $tenBan }}" {{ (!$isCustomName && $currentTenBan == $tenBan) ? 'selected' : '' }}>
                                        {{ $tenBan }}
                                    </option>
                                @endforeach
                                <option value="khac" {{ $isCustomName ? 'selected' : '' }}>✨ Khác (Nhập tên tùy chỉnh)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        @error('tenban')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Custom (Hidden by default) --}}
                    <div id="customBanName" class="{{ $isCustomName ? '' : 'hidden' }} transition-all duration-300">
                        <label class="block text-sm font-semibold text-indigo-700 mb-2">
                            <i class="fas fa-pen text-indigo-500 mr-1"></i> Nhập tên mới
                        </label>
                        <input type="text" 
                            name="tenban_custom" 
                            id="tenban_custom"
                            value="{{ $isCustomName ? $currentTenBan : old('tenban_custom') }}"
                            placeholder="VD: Ban Truyền thông..."
                            class="w-full px-4 py-3 border border-indigo-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-indigo-50/50 shadow-inner">
                    </div>

                    {{-- 2. Mô tả --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-blue-500 mr-1"></i> Mô tả nhiệm vụ
                        </label>
                        <textarea name="mota" rows="5"
                            placeholder="Mô tả chi tiết nhiệm vụ..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm resize-y">{{ old('mota', $ban->mota) }}</textarea>
                        @error('mota')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Warning Box nếu đã có người --}}
                    @if($ban->phancongs->count() > 0)
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-600 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-bold text-amber-800">Lưu ý quan trọng</h4>
                                <p class="text-sm text-amber-700 mt-1">
                                    Ban này đang có <strong>{{ $ban->phancongs->count() }} thành viên</strong>. 
                                    Việc đổi tên ban có thể ảnh hưởng đến ngữ cảnh công việc của họ.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" 
                            class="px-6 py-3 rounded-xl font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                            Hủy bỏ
                        </a>
                        <button type="submit" 
                            class="flex-1 px-6 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT COLUMN: INFO & STAFF (Chiếm 1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Info Card --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i> Thông tin chung
                </h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex justify-between items-center">
                        <span class="text-gray-500">Mã ban</span>
                        <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded">{{ $ban->maban }}</span>
                    </li>
                    <li>
                        <span class="text-gray-500 block mb-1">Thuộc cuộc thi</span>
                        <span class="font-semibold text-indigo-700">{{ $ban->cuocthi->tencuocthi ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="text-gray-500 block mb-1">Ngày bắt đầu</span>
                        <span class="font-medium text-gray-800">
                            {{ $ban->cuocthi->thoigianbatdau ? \Carbon\Carbon::parse($ban->cuocthi->thoigianbatdau)->format('d/m/Y') : 'N/A' }}
                        </span>
                    </li>
                </ul>
            </div>

            {{-- Staff List Card --}}
            @if($ban->phancongs->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-sm">Nhân sự ({{ $ban->phancongs->count() }})</h3>
                    <span class="text-xs text-blue-600 font-medium">Đang hoạt động</span>
                </div>
                <div class="max-h-80 overflow-y-auto p-2 space-y-2 custom-scrollbar">
                    @foreach($ban->phancongs as $phancong)
                    <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition group">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            {{ substr($phancong->giangvien->nguoiDung->hoten ?? 'U', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-indigo-600 transition">
                                {{ $phancong->giangvien->nguoiDung->hoten ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ $phancong->vaitro }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</section>

{{-- JAVASCRIPT --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectBan = document.getElementById('tenban');
    const customBanDiv = document.getElementById('customBanName');
    const customBanInput = document.getElementById('tenban_custom');

    // Hàm xử lý logic ẩn hiện và hoán đổi name attribute
    function toggleCustomInput() {
        if (selectBan.value === 'khac') {
            customBanDiv.classList.remove('hidden');
            customBanInput.required = true;
            selectBan.removeAttribute('name'); // Bỏ name của select để tránh gửi trùng
            customBanInput.setAttribute('name', 'tenban'); // Input này sẽ là giá trị gửi đi
            customBanInput.focus();
        } else {
            customBanDiv.classList.add('hidden');
            customBanInput.required = false;
            customBanInput.removeAttribute('name');
            selectBan.setAttribute('name', 'tenban'); // Select sẽ là giá trị gửi đi
        }
    }

    selectBan.addEventListener('change', toggleCustomInput);

    // Xử lý trạng thái khi load lại trang (ví dụ khi validate fail hoặc edit)
    if (selectBan.value === 'khac') {
        toggleCustomInput();
    }
});
</script>
<style>
    /* Thanh cuộn nhỏ cho danh sách nhân sự */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c7c7c7; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
</style>
@endpush

@endsection