@extends('layouts.client')
@section('title', 'Tạo Phân công mới')

@section('content')
{{-- 🎯 HERO SECTION: Màu nhấn Tạo mới (Green/Teal) --}}
<section class="relative bg-gradient-to-br from-teal-800 to-green-700 text-white py-16 overflow-hidden">
    {{-- Background Effect giữ nguyên --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">/</span>
            <span class="text-white/90 text-sm font-medium">Tạo phân công</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">
            <i class="fas fa-plus-circle mr-3 text-teal-400"></i>Tạo Phân công mới
        </h1>
        <p class="text-green-200 text-lg font-light">
            Phân công công việc cho giảng viên trong bộ môn một cách dễ dàng
        </p>
    </div>

    {{-- Đường cắt trắng sạch --}}
    <div class="absolute bottom-0 left-0 right-0 h-8 bg-white" style="clip-path: polygon(100% 0, 0% 0%, 0% 100%, 100% 100%);"></div>
</section>

{{-- 📝 FORM SECTION --}}
<section class="container mx-auto px-6 py-12 -mt-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
            {{-- Form Header --}}
            <div class="bg-gradient-to-r from-teal-600 to-green-500 text-white p-6">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Thông tin phân công chi tiết
                </h2>
                <p class="text-green-100 mt-1 text-sm">Vui lòng điền chính xác các trường bắt buộc (*)</p>
            </div>

            <form action="{{ route('giangvien.phancong.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                {{-- Group: Giảng viên và Cuộc thi --}}
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- 1. Chọn Giảng viên --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-indigo-500 mr-1"></i>
                            Giảng viên <span class="text-red-500">*</span>
                        </label>
                        <select name="magiangvien" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition @error('magiangvien') border-red-500 @enderror"
                            required>
                            <option value="">-- Chọn giảng viên --</option>
                            @foreach($giangVienList as $gv)
                                <option value="{{ $gv->magiangvien }}" {{ old('magiangvien') == $gv->magiangvien ? 'selected' : '' }}>
                                    {{ $gv->nguoiDung->hoten ?? 'N/A' }} - {{ $gv->chucvu ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('magiangvien')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Chọn Cuộc thi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-trophy text-amber-500 mr-1"></i>
                            Cuộc thi <span class="text-red-500">*</span>
                        </label>
                        <select name="macuocthi" id="cuocthi-select"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition"
                            required>
                            <option value="">-- Chọn cuộc thi --</option>
                            @foreach($cuocThiList as $ct)
                                <option value="{{ $ct->macuocthi }}" {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                    {{ $ct->tencuocthi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Group: Ban và Vai trò --}}
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- 3. Chọn Ban --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-users text-blue-500 mr-1"></i>
                            Ban <span class="text-red-500">*</span>
                        </label>
                        <select name="maban" id="ban-select"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition @error('maban') border-red-500 @enderror"
                            required disabled>
                            <option value="">-- Chọn cuộc thi trước --</option>
                        </select>
                        @error('maban')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4. Vai trò --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-tag text-purple-500 mr-1"></i>
                            Vai trò <span class="text-red-500">*</span>
                        </label>
                        <select name="vaitro" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition @error('vaitro') border-red-500 @enderror"
                            required>
                            <option value="">-- Chọn vai trò --</option>
                            <option value="Trưởng ban" {{ old('vaitro') == 'Trưởng ban' ? 'selected' : '' }}>Trưởng ban</option>
                            <option value="Phó ban" {{ old('vaitro') == 'Phó ban' ? 'selected' : '' }}>Phó ban</option>
                            <option value="Ủy viên" {{ old('vaitro') == 'Ủy viên' ? 'selected' : '' }}>Ủy viên</option>
                            <option value="Thư ký" {{ old('vaitro') == 'Thư ký' ? 'selected' : '' }}>Thư ký</option>
                            <option value="Thành viên" {{ old('vaitro') == 'Thành viên' ? 'selected' : '' }}>Thành viên</option>
                        </select>
                        @error('vaitro')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Group: Công việc --}}
                <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                    <h3 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                         <i class="fas fa-briefcase text-cyan-600"></i> Chi tiết Công việc
                    </h3>
                    
                    {{-- 5. Chọn Công việc --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-list-alt text-cyan-500 mr-1"></i>
                            Chọn đầu việc <span class="text-red-500">*</span>
                        </label>
                        <select name="tencongviec" id="congviec-select"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition @error('tencongviec') border-red-500 @enderror"
                            required>
                            <option value="">-- Chọn công việc --</option>
                            <option value="Xây dựng đề tài và tiêu chí đánh giá" {{ old('tencongviec') == 'Xây dựng đề tài và tiêu chí đánh giá' ? 'selected' : '' }}>Xây dựng đề tài và tiêu chí đánh giá</option>
                            <option value="Chuẩn bị kế hoạch mac" {{ old('tencongviec') == 'Chuẩn bị kế hoạch mac' ? 'selected' : '' }}>Chuẩn bị kế hoạch mac</option>
                            <option value="Soạn đề thi" {{ old('tencongviec') == 'Soạn đề thi' ? 'selected' : '' }}>Soạn đề thi</option>
                            <option value="Chấm điểm" {{ old('tencongviec') == 'Chấm điểm' ? 'selected' : '' }}>Chấm điểm</option>
                            <option value="Hỗ trợ kỹ thuật" {{ old('tencongviec') == 'Hỗ trợ kỹ thuật' ? 'selected' : '' }}>Hỗ trợ kỹ thuật</option>
                            <option value="Đánh giá dự án" {{ old('tencongviec') == 'Đánh giá dự án' ? 'selected' : '' }}>Đánh giá dự án</option>
                            <option value="Xây dựng nội dung" {{ old('tencongviec') == 'Xây dựng nội dung' ? 'selected' : '' }}>Xây dựng nội dung</option>
                            <option value="Xây dựng nội dung thi đấu" {{ old('tencongviec') == 'Xây dựng nội dung thi đấu' ? 'selected' : '' }}>Xây dựng nội dung thi đấu</option>
                            <option value="khac" {{ old('tencongviec') == 'khac' ? 'selected' : '' }}>Khác (Nhập tùy chỉnh)</option>
                        </select>
                        @error('tencongviec')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. Input công việc tùy chỉnh (ẩn mặc định) --}}
                    <div id="customCongViec" class="hidden mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-pencil-alt text-cyan-500 mr-1"></i>
                            Nhập tên công việc tùy chỉnh
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="tencongviec_custom" 
                            id="tencongviec_custom"
                            value="{{ old('tencongviec_custom') }}"
                            placeholder="VD: Hỗ trợ kỹ thuật sân khấu, Thiết kế backdrop..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    </div>
                </div>
                
                {{-- 7. Ngày phân công --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="far fa-calendar-alt text-teal-500 mr-1"></i>
                        Ngày phân công
                    </label>
                    <input type="date" 
                        name="ngayphancong" 
                        value="{{ old('ngayphancong', date('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    <p class="mt-1 text-sm text-gray-500">Để trống sẽ lấy ngày hiện tại</p>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t">
                    <button type="submit" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition inline-flex items-center justify-center gap-2">
                        <i class="fas fa-save mr-2"></i>
                        Lưu phân công
                    </button>
                    <a href="{{ route('giangvien.phancong.index') }}" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition text-center inline-flex items-center justify-center gap-2">
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
    const cuocThiSelect = document.getElementById('cuocthi-select');
    const banSelect = document.getElementById('ban-select');
    const congViecSelect = document.getElementById('congviec-select');
    const customCongViecDiv = document.getElementById('customCongViec');
    const customCongViecInput = document.getElementById('tencongviec_custom');

    // Hàm xử lý tải danh sách ban
    async function loadBanList(macuocthi) {
        banSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        banSelect.disabled = true;

        if (!macuocthi) {
            banSelect.innerHTML = '<option value="">-- Chọn cuộc thi trước --</option>';
            return;
        }

        try {
            // Lấy danh sách ban
            const banResponse = await fetch(`/giang-vien/phan-cong/api/ban/${macuocthi}`);
            const banList = await banResponse.json();
            
            banSelect.innerHTML = '<option value="">-- Chọn ban --</option>';
            banList.forEach(ban => {
                const option = document.createElement('option');
                option.value = ban.maban;
                option.textContent = ban.tenban;
                // Kiểm tra và chọn lại giá trị cũ nếu có
                if (option.value == '{{ old('maban') }}') {
                    option.selected = true;
                }
                banSelect.appendChild(option);
            });
            banSelect.disabled = false;

        } catch (error) {
            console.error('Error:', error);
            banSelect.innerHTML = '<option value="">-- Lỗi khi tải dữ liệu --</option>';
        }
    }

    // Xử lý khi chọn cuộc thi
    cuocThiSelect.addEventListener('change', function() {
        const macuocthi = this.value;
        loadBanList(macuocthi);
    });

    // Xử lý khi chọn công việc
    congViecSelect.addEventListener('change', function() {
        // Nếu chọn "Khác (Nhập tùy chỉnh)"
        if (this.value === 'khac') {
            customCongViecDiv.classList.remove('hidden');
            customCongViecInput.required = true;
            
            // Đổi tên trường để server nhận tên công việc từ input custom
            congViecSelect.removeAttribute('name');
            customCongViecInput.setAttribute('name', 'tencongviec');
        } else {
            // Nếu chọn công việc có sẵn
            customCongViecDiv.classList.add('hidden');
            customCongViecInput.required = false;
            
            // Đổi tên trường để server nhận tên công việc từ select
            customCongViecInput.removeAttribute('name');
            congViecSelect.setAttribute('name', 'tencongviec');
        }
    });

    // Logic kiểm tra và tải lại dữ liệu cũ khi load trang (Old value handling)
    const initialCuocThi = cuocThiSelect.value;
    if (initialCuocThi) {
        loadBanList(initialCuocThi);
    }
    // Kiểm tra hiển thị input tùy chỉnh cho công việc
    if (congViecSelect.value === 'khac') {
        customCongViecDiv.classList.remove('hidden');
        // Đảm bảo tên trường (name) được thiết lập đúng khi có old()
        congViecSelect.removeAttribute('name');
        customCongViecInput.setAttribute('name', 'tencongviec');
    }
});
</script>
@endpush

@endsection