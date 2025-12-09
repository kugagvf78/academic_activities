@extends('layouts.client')
@section('title', 'Chỉnh sửa Phân công')

@section('content')
{{-- 🎯 HERO SECTION: Phong cách Academic (Indigo/Blue) --}}
<section class="relative bg-gradient-to-br from-indigo-900 to-blue-800 text-white pt-20 pb-24 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="container mx-auto px-6 relative z-10">
        {{-- Breadcrumb --}}
        <div class="mb-5 text-sm font-medium text-blue-200 flex items-center gap-2">
            <a href="{{ route('giangvien.phancong.index') }}" class="hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Danh sách
            </a>
            <span class="text-white/40">/</span>
            <a href="{{ route('giangvien.phancong.show', $phanCong->maphancong) }}" class="hover:text-white transition">
                Chi tiết
            </a>
            <span class="text-white/40">/</span>
            <span class="text-white">Chỉnh sửa</span>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-3">
            <i class="fas fa-edit mr-3 text-cyan-400"></i>Chỉnh sửa Phân công
        </h1>
        <p class="text-indigo-200 text-xl font-light max-w-2xl">
            Cập nhật thông tin công việc, vai trò và thời gian thực hiện cho giảng viên.
        </p>
    </div>

    {{-- Đường cắt hiện đại --}}
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-white" style="clip-path: polygon(0% 100%, 100% 0, 100% 100%, 0% 100%);"></div>
</section>

{{-- 📝 FORM SECTION --}}
<section class="container mx-auto px-6 py-12 -mt-20 relative z-20">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            
            {{-- Form Header --}}
            <div class="bg-gradient-to-r from-indigo-700 to-blue-600 text-white p-6 md:p-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-cog fa-spin-hover mr-3"></i>
                        Thông tin cập nhật
                    </h2>
                    <p class="text-blue-100 mt-1 text-sm opacity-90">Mã phân công: <strong>{{ $phanCong->maphancong }}</strong></p>
                </div>
                {{-- Info Box nhỏ hiển thị cuộc thi hiện tại --}}
                <div class="hidden md:block bg-white/10 backdrop-blur-md rounded-lg p-3 border border-white/20 max-w-xs">
                    <div class="text-xs text-blue-200 uppercase font-semibold">Thuộc cuộc thi</div>
                    <div class="font-bold truncate" title="{{ $phanCong->ban->cuocthi->tencuocthi ?? '' }}">
                        {{ $phanCong->ban->cuocthi->tencuocthi ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <form action="{{ route('giangvien.phancong.update', $phanCong->maphancong) }}" method="POST" class="p-8 space-y-8">
                @csrf
                @method('PUT')

                {{-- SECTION 1: NHÂN SỰ & TỔ CHỨC --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-5 pb-2 border-b border-gray-200 flex items-center gap-2">
                        <i class="fas fa-users-cog text-indigo-600"></i> Nhân sự & Tổ chức
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- 1. Giảng viên --}}
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition">
                                <i class="fas fa-user text-gray-400 group-focus-within:text-indigo-500 mr-1"></i>
                                Giảng viên <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="magiangvien" required
                                    class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm bg-gray-50 focus:bg-white hover:bg-white cursor-pointer appearance-none">
                                    <option value="">-- Chọn giảng viên --</option>
                                    @foreach($giangVienList as $gv)
                                        <option value="{{ $gv->magiangvien }}" 
                                            {{ old('magiangvien', $phanCong->magiangvien) == $gv->magiangvien ? 'selected' : '' }}>
                                            {{ $gv->nguoiDung->hoten ?? 'N/A' }} 
                                            @if($gv->chucvu) ({{ $gv->chucvu }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('magiangvien')
                                <p class="mt-1 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 2. Ban (Đơn vị) --}}
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition">
                                <i class="fas fa-layer-group text-gray-400 group-focus-within:text-blue-500 mr-1"></i>
                                Ban chuyên môn <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="maban" required
                                    class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50 focus:bg-white hover:bg-white cursor-pointer appearance-none">
                                    <option value="">-- Chọn ban --</option>
                                    @foreach($banList as $ban)
                                        <option value="{{ $ban->maban }}" 
                                            {{ old('maban', $phanCong->maban) == $ban->maban ? 'selected' : '' }}>
                                            {{ $ban->tenban }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('maban')
                                <p class="mt-1 text-sm text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: CHI TIẾT CÔNG VIỆC --}}
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <i class="fas fa-briefcase text-cyan-600"></i> Nội dung Công việc & Vai trò
                    </h3>
                    
                    <div class="space-y-6">
                        {{-- 3. Công việc (Logic phức tạp: Select + Input Custom) --}}
                        @php
                            $danhSachCongViec = [
                                'Xây dựng đề tài và tiêu chí đánh giá',
                                'Chuẩn bị kế hoạch mac',
                                'Soạn đề thi',
                                'Chấm điểm',
                                'Hỗ trợ kỹ thuật',
                                'Đánh giá dự án',
                                'Xây dựng nội dung',
                                'Xây dựng nội dung thi đấu'
                            ];
                            
                            $currentCongViec = $phanCong->congviec->tencongviec ?? '';
                            $isCustomCongViec = !in_array($currentCongViec, $danhSachCongViec) && !empty($currentCongViec);
                        @endphp
                        
                        <div class="grid md:grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tên công việc <span class="text-red-500">*</span>
                                </label>
                                <select id="congviec-select" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 transition shadow-sm">
                                    <option value="">-- Chọn đầu việc --</option>
                                    @foreach($danhSachCongViec as $tenCV)
                                        <option value="{{ $tenCV }}" {{ old('tencongviec', $isCustomCongViec ? '' : $currentCongViec) == $tenCV ? 'selected' : '' }}>
                                            {{ $tenCV }}
                                        </option>
                                    @endforeach
                                    <option value="khac" {{ $isCustomCongViec ? 'selected' : '' }}>-- Khác (Nhập thủ công) --</option>
                                </select>
                            </div>

                            {{-- Input ẩn cho công việc tùy chỉnh --}}
                            <div id="customCongViec" class="{{ $isCustomCongViec ? '' : 'hidden' }} transition-all duration-300 ease-in-out">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-pen text-cyan-500 mr-1"></i> Nhập tên công việc cụ thể
                                </label>
                                <input type="text" 
                                    id="tencongviec_custom"
                                    value="{{ $isCustomCongViec ? $currentCongViec : old('tencongviec_custom') }}"
                                    placeholder="VD: Thiết kế banner, Hậu cần sân khấu..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white shadow-inner">
                                <p class="text-xs text-gray-500 mt-1">Nhập tên công việc chi tiết nếu không có trong danh sách.</p>
                            </div>
                            @error('tencongviec')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 pt-2">
                            {{-- 4. Vai trò --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-badge text-purple-500 mr-1"></i>
                                    Vai trò đảm nhiệm <span class="text-red-500">*</span>
                                </label>
                                <select name="vaitro" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition shadow-sm bg-white">
                                    <option value="">-- Chọn vai trò --</option>
                                    @foreach(['Trưởng ban', 'Phó ban', 'Ủy viên', 'Thư ký', 'Thành viên'] as $role)
                                        <option value="{{ $role }}" {{ old('vaitro', $phanCong->vaitro) == $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vaitro')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 5. Ngày phân công --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="far fa-calendar-alt text-teal-500 mr-1"></i>
                                    Ngày phân công
                                </label>
                                <input type="date" name="ngayphancong" 
                                    value="{{ old('ngayphancong', $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('Y-m-d') : '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 justify-end">
                    <a href="{{ route('giangvien.phancong.show', $phanCong->maphancong) }}" 
                        class="px-8 py-3 rounded-xl font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </a>
                    <button type="submit" 
                        class="px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- JAVASCRIPT LOGIC --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const congViecSelect = document.getElementById('congviec-select');
    const customCongViecDiv = document.getElementById('customCongViec');
    const customCongViecInput = document.getElementById('tencongviec_custom');

    // Hàm xử lý chuyển đổi tên input
    function toggleCustomJob(isCustom) {
        if (isCustom) {
            customCongViecDiv.classList.remove('hidden');
            customCongViecInput.required = true;
            // Server sẽ nhận giá trị từ input text
            customCongViecInput.setAttribute('name', 'tencongviec');
            congViecSelect.removeAttribute('name');
        } else {
            customCongViecDiv.classList.add('hidden');
            customCongViecInput.required = false;
            // Server sẽ nhận giá trị từ select
            customCongViecInput.removeAttribute('name');
            congViecSelect.setAttribute('name', 'tencongviec');
        }
    }

    // Sự kiện change
    congViecSelect.addEventListener('change', function() {
        toggleCustomJob(this.value === 'khac');
    });

    // Khởi tạo ban đầu (dành cho trường hợp reload hoặc edit có dữ liệu cũ)
    if (congViecSelect.value === 'khac') {
        toggleCustomJob(true);
    } else {
        // Đảm bảo name attribute đúng ngay cả khi không phải custom
        congViecSelect.setAttribute('name', 'tencongviec');
        customCongViecInput.removeAttribute('name');
    }
});
</script>
@endpush

@endsection