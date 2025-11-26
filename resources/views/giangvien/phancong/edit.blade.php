{{-- resources/views/giangvien/phancong/edit.blade.php --}}
@extends('layouts.client')
@section('title', 'Chỉnh sửa Phân công')

@section('content')
{{-- HERO SECTION - Màu xanh dương cyan theo yêu cầu --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v-4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.show', $phanCong->maphancong) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Phân công #{{ $phanCong->maphancong }}</span>
        </div>
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-edit mr-3"></i>Chỉnh sửa Phân công
        </h1>
        <p class="text-cyan-100">Cập nhật thông tin phân công công việc cho giảng viên</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- FORM SECTION --}}
<section class="container mx-auto px-6 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header card -->
            <div class="bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-500 text-white p-6">
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Chỉnh sửa phân công
                </h2>
                <p class="text-cyan-100 mt-1">Cập nhật thông tin một cách chính xác và nhanh chóng</p>
            </div>

            <form action="{{ route('giangvien.phancong.update', $phanCong->maphancong) }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- Mã phân công (readonly) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-hashtag text-cyan-600 mr-1"></i>
                        Mã phân công
                    </label>
                    <input type="text" value="{{ $phanCong->maphancong }}" readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-600 cursor-not-allowed">
                </div>

                <!-- Giảng viên -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-600 mr-1"></i>
                        Giảng viên <span class="text-red-500">*</span>
                    </label>
                    <select name="magiangvien" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 transition @error('magiangvien') border-red-500 @enderror">
                        <option value="">-- Chọn giảng viên --</option>
                        @foreach($giangVienList as $gv)
                            <option value="{{ $gv->magiangvien }}" 
                                {{ old('magiangvien', $phanCong->magiangvien) == $gv->magiangvien ? 'selected' : '' }}>
                                {{ $gv->nguoiDung->hoten ?? 'N/A' }} - {{ $gv->chucvu ?? $gv->magiangvien }}
                            </option>
                        @endforeach
                    </select>
                    @error('magiangvien')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ban -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-users text-blue-600 mr-1"></i>
                        Ban <span class="text-red-500">*</span>
                    </label>
                    <select name="maban" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 transition @error('maban') border-red-500 @enderror">
                        <option value="">-- Chọn ban --</option>
                        @foreach($banList as $ban)
                            <option value="{{ $ban->maban }}" 
                                {{ old('maban', $phanCong->maban) == $ban->maban ? 'selected' : '' }}>
                                {{ $ban->tenban }}
                            </option>
                        @endforeach
                    </select>
                    @error('maban')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Công việc -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase text-cyan-600 mr-1"></i>
                        Công việc <span class="text-red-500">*</span>
                    </label>
                    @php
                        $danhSachCongViec = [
                            'Xây dựng đề tài và tiêu chí đánh giá',
                            'Chuẩn bị kế hoạch mac',
                            'Soạn đề thi',
                            'Chấm điểm vòng sơ khảo',
                            'Chấm điểm vòng chung kết',
                            'Hỗ trợ kỹ thuật',
                            'Đánh giá dự án',
                            'Xây dựng nội dung',
                            'Xây dựng nội dung thi đấu'
                        ];
                        
                        // Lấy tên công việc hiện tại
                        $currentCongViec = '';
                        if($phanCong->congviec) {
                            $currentCongViec = $phanCong->congviec->tencongviec;
                        }
                        
                        $isCustomCongViec = !in_array($currentCongViec, $danhSachCongViec) && !empty($currentCongViec);
                    @endphp
                    
                    <select name="tencongviec" id="congviec-select" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 transition @error('tencongviec') border-red-500 @enderror">
                        <option value="">-- Chọn công việc --</option>
                        @foreach($danhSachCongViec as $tenCV)
                            <option value="{{ $tenCV }}" {{ old('tencongviec', $currentCongViec) == $tenCV ? 'selected' : '' }}>
                                {{ $tenCV }}
                            </option>
                        @endforeach
                        <option value="khac" {{ $isCustomCongViec ? 'selected' : '' }}>Khác (Nhập tùy chỉnh)</option>
                    </select>
                    @error('tencongviec')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input công việc tùy chỉnh --}}
                <div id="customCongViec" class="{{ $isCustomCongViec ? '' : 'hidden' }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-pencil-alt text-cyan-600 mr-1"></i>
                        Nhập tên công việc tùy chỉnh
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        name="tencongviec_custom" 
                        id="tencongviec_custom"
                        value="{{ $isCustomCongViec ? $currentCongViec : old('tencongviec_custom') }}"
                        placeholder="VD: Hỗ trợ kỹ thuật sân khấu, Thiết kế backdrop..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                </div>

                <!-- Vai trò -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-purple-600 mr-1"></i>
                        Vai trò <span class="text-red-500">*</span>
                    </label>
                    <select name="vaitro" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 transition @error('vaitro') border-red-500 @enderror">
                        <option value="">-- Chọn vai trò --</option>
                        <option value="Trưởng ban" {{ old('vaitro', $phanCong->vaitro) == 'Trưởng ban' ? 'selected' : '' }}>Trưởng ban</option>
                        <option value="Phó ban" {{ old('vaitro', $phanCong->vaitro) == 'Phó ban' ? 'selected' : '' }}>Phó ban</option>
                        <option value="Ủy viên" {{ old('vaitro', $phanCong->vaitro) == 'Ủy viên' ? 'selected' : '' }}>Ủy viên</option>
                        <option value="Thư ký" {{ old('vaitro', $phanCong->vaitro) == 'Thư ký' ? 'selected' : '' }}>Thư ký</option>
                        <option value="Thành viên" {{ old('vaitro', $phanCong->vaitro) == 'Thành viên' ? 'selected' : '' }}>Thành viên</option>
                    </select>
                    @error('vaitro')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ngày phân công -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="far fa-calendar-alt text-cyan-600 mr-1"></i>
                        Ngày phân công
                    </label>
                    <input type="date" name="ngayphancong" 
                        value="{{ old('ngayphancong', $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 transition">
                    <p class="mt-1 text-sm text-gray-500">Để trống sẽ giữ nguyên hoặc lấy ngày hiện tại</p>
                </div>

                <!-- Thông tin bổ sung -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Thông tin hiện tại
                    </h3>
                    <div class="grid md:grid-cols-2 gap-3 text-sm">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-trophy text-blue-500 mt-0.5"></i>
                            <div>
                                <span class="text-gray-600">Cuộc thi:</span>
                                <p class="font-medium text-gray-800">
                                    {{ $phanCong->ban->cuocthi->tencuocthi ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fas fa-users text-blue-500 mt-0.5"></i>
                            <div>
                                <span class="text-gray-600">Ban hiện tại:</span>
                                <p class="font-medium text-gray-800">{{ $phanCong->ban->tenban ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-700 via-blue-600 to-cyan-500 hover:from-indigo-800 hover:via-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i>
                        Cập nhật phân công
                    </button>
                    <a href="{{ route('giangvien.phancong.show', $phanCong->maphancong) }}" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition text-center">
                        <i class="fas fa-times mr-2"></i>
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const congViecSelect = document.getElementById('congviec-select');
    const customCongViecDiv = document.getElementById('customCongViec');
    const customCongViecInput = document.getElementById('tencongviec_custom');

    congViecSelect.addEventListener('change', function() {
        if (this.value === 'khac') {
            customCongViecDiv.classList.remove('hidden');
            customCongViecInput.required = true;
            congViecSelect.removeAttribute('name');
            customCongViecInput.setAttribute('name', 'tencongviec');
        } else {
            customCongViecDiv.classList.add('hidden');
            customCongViecInput.required = false;
            customCongViecInput.removeAttribute('name');
            congViecSelect.setAttribute('name', 'tencongviec');
        }
    });

    // Kiểm tra ngay khi load trang
    if (congViecSelect.value === 'khac') {
        customCongViecInput.setAttribute('name', 'tencongviec');
        congViecSelect.removeAttribute('name');
    }
});
</script>
@endpush

@endsection