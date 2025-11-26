@extends('layouts.client')
@section('title', 'Tạo Phân công mới')

@section('content')
{{-- 🎯 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-12 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Phân công</span>
        </div>
        <h1 class="text-3xl font-black">
            <i class="fas fa-plus-circle mr-3"></i>Tạo Phân công mới
        </h1>
        <p class="text-blue-100 mt-2">Phân công công việc cho giảng viên trong bộ môn</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- 📝 FORM SECTION --}}
<section class="container mx-auto px-6 py-12">
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('giangvien.phancong.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-indigo-100 overflow-hidden">
            @csrf
            
            <div class="p-8 space-y-6">
                {{-- Giảng viên --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-500 mr-2"></i>
                        Giảng viên <span class="text-red-500">*</span>
                    </label>
                    <select name="magiangvien" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('magiangvien') border-red-500 @enderror">
                        <option value="">-- Chọn giảng viên --</option>
                        @foreach($giangVienList as $gv)
                            <option value="{{ $gv->magiangvien }}" {{ old('magiangvien') == $gv->magiangvien ? 'selected' : '' }}>
                                {{ $gv->nguoiDung->hoten ?? 'N/A' }} - {{ $gv->magiangvien }}
                            </option>
                        @endforeach
                    </select>
                    @error('magiangvien')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Công việc --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase text-indigo-500 mr-2"></i>
                        Công việc <span class="text-red-500">*</span>
                    </label>
                    <select name="macongviec" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('macongviec') border-red-500 @enderror">
                        <option value="">-- Chọn công việc --</option>
                        @foreach($congViecList as $cv)
                            <option value="{{ $cv->macongviec }}" {{ old('macongviec') == $cv->macongviec ? 'selected' : '' }}>
                                {{ $cv->tencongviec }} 
                                @if($cv->cuocthi)
                                    - {{ $cv->cuocthi->tencuocthi }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('macongviec')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ban --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-users text-indigo-500 mr-2"></i>
                        Ban <span class="text-red-500">*</span>
                    </label>
                    <select name="maban" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('maban') border-red-500 @enderror">
                        <option value="">-- Chọn ban --</option>
                        @foreach($banList as $ban)
                            <option value="{{ $ban->maban }}" {{ old('maban') == $ban->maban ? 'selected' : '' }}>
                                {{ $ban->tenban }}
                            </option>
                        @endforeach
                    </select>
                    @error('maban')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Vai trò --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-indigo-500 mr-2"></i>
                        Vai trò <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="vaitro" value="{{ old('vaitro') }}" required
                        placeholder="VD: Trưởng ban, Thành viên, Phụ trách kỹ thuật..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('vaitro') border-red-500 @enderror">
                    @error('vaitro')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ngày phân công --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="far fa-calendar text-indigo-500 mr-2"></i>
                        Ngày phân công <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="ngayphancong" value="{{ old('ngayphancong', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('ngayphancong') border-red-500 @enderror">
                    @error('ngayphancong')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-gray-50 px-8 py-6 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end border-t border-gray-200">
                <a href="{{ route('giangvien.phancong.index') }}" 
                    class="w-full sm:w-auto px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold shadow-sm hover:shadow-md transition border border-gray-300 text-center">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
                <button type="submit" 
                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-500 text-white rounded-xl font-semibold shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-blue-600 transition">
                    <i class="fas fa-save mr-2"></i>Lưu phân công
                </button>
            </div>
        </form>
    </div>
</section>

@endsection


@section('content')
{{-- 🎯 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-green-600 via-emerald-500 to-teal-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Quản lý phân công</span>
        </div>
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-plus-circle mr-3"></i>Tạo Phân công mới
        </h1>
        <p class="text-green-100">Phân công công việc cho giảng viên trong bộ môn</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- 📝 FORM SECTION --}}
<section class="container mx-auto px-6 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white p-6">
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Thông tin phân công
                </h2>
                <p class="text-green-100 mt-1">Điền đầy đủ thông tin bên dưới</p>
            </div>

            <form action="{{ route('giangvien.phancong.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                {{-- Chọn Giảng viên --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-500 mr-1"></i>
                        Giảng viên <span class="text-red-500">*</span>
                    </label>
                    <select name="magiangvien" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('magiangvien') border-red-500 @enderror"
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

                {{-- Chọn Cuộc thi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-trophy text-amber-500 mr-1"></i>
                        Cuộc thi <span class="text-red-500">*</span>
                    </label>
                    <select name="macuocthi" id="cuocthi-select"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                        required>
                        <option value="">-- Chọn cuộc thi --</option>
                        @foreach($cuocThiList as $ct)
                            <option value="{{ $ct->macuocthi }}" {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                {{ $ct->tencuocthi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Chọn Ban --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-users text-blue-500 mr-1"></i>
                        Ban <span class="text-red-500">*</span>
                    </label>
                    <select name="maban" id="ban-select"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('maban') border-red-500 @enderror"
                        required disabled>
                        <option value="">-- Chọn cuộc thi trước --</option>
                    </select>
                    @error('maban')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Chọn Công việc --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase text-cyan-500 mr-1"></i>
                        Công việc <span class="text-red-500">*</span>
                    </label>
                    <select name="macongviec" id="congviec-select"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('macongviec') border-red-500 @enderror"
                        required disabled>
                        <option value="">-- Chọn cuộc thi trước --</option>
                    </select>
                    @error('macongviec')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Vai trò --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-purple-500 mr-1"></i>
                        Vai trò <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        name="vaitro" 
                        value="{{ old('vaitro') }}"
                        placeholder="VD: Trưởng ban, Phó ban, Ủy viên..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('vaitro') border-red-500 @enderror"
                        required>
                    @error('vaitro')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ngày phân công --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="far fa-calendar text-teal-500 mr-1"></i>
                        Ngày phân công
                    </label>
                    <input type="date" 
                        name="ngayphancong" 
                        value="{{ old('ngayphancong', date('Y-m-d')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <p class="mt-1 text-sm text-gray-500">Để trống sẽ lấy ngày hiện tại</p>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition">
                        <i class="fas fa-save mr-2"></i>
                        Lưu phân công
                    </button>
                    <a href="{{ route('giangvien.phancong.index') }}" 
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
    const cuocThiSelect = document.getElementById('cuocthi-select');
    const banSelect = document.getElementById('ban-select');
    const congViecSelect = document.getElementById('congviec-select');

    cuocThiSelect.addEventListener('change', async function() {
        const macuocthi = this.value;
        
        // Reset
        banSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        congViecSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        banSelect.disabled = true;
        congViecSelect.disabled = true;

        if (!macuocthi) {
            banSelect.innerHTML = '<option value="">-- Chọn cuộc thi trước --</option>';
            congViecSelect.innerHTML = '<option value="">-- Chọn cuộc thi trước --</option>';
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
                banSelect.appendChild(option);
            });
            banSelect.disabled = false;

            // Lấy danh sách công việc
            const cvResponse = await fetch(`/giang-vien/phan-cong/api/congviec/${macuocthi}`);
            const cvList = await cvResponse.json();
            
            congViecSelect.innerHTML = '<option value="">-- Chọn công việc --</option>';
            cvList.forEach(cv => {
                const option = document.createElement('option');
                option.value = cv.macongviec;
                option.textContent = cv.tencongviec;
                congViecSelect.appendChild(option);
            });
            congViecSelect.disabled = false;

        } catch (error) {
            console.error('Error:', error);
            banSelect.innerHTML = '<option value="">-- Lỗi khi tải dữ liệu --</option>';
            congViecSelect.innerHTML = '<option value="">-- Lỗi khi tải dữ liệu --</option>';
        }
    });
});
</script>
@endpush

@endsection