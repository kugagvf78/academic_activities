@extends('layouts.client')
@section('title', 'Thêm Giải thưởng')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-orange-600 to-red-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Thêm giải thưởng</span>
        </div>
        <h1 class="text-3xl font-black flex items-center gap-3">
            <i class="fas fa-trophy"></i>
            Thêm Giải thưởng Mới
        </h1>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 70C120 60 240 40 360 35C480 30 600 40 720 45C840 50 960 50 1080 45C1200 40 1320 30 1380 25L1440 20V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- FORM --}}
<section class="container mx-auto px-6 py-12 -mt-8 relative z-10">
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('giangvien.giaithuong.store') }}" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            @csrf

            {{-- Step 1: Chọn cuộc thi --}}
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-8 py-5 border-b border-orange-100">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 bg-orange-500 text-white rounded-full text-sm">1</span>
                    Chọn Cuộc thi
                </h3>
            </div>

            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cuộc thi <span class="text-red-500">*</span>
                    </label>
                    <select name="macuocthi" id="macuocthi" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('macuocthi') border-red-500 @enderror">
                        <option value="">-- Chọn cuộc thi --</option>
                        @foreach($cuocthiList as $ct)
                        <option value="{{ $ct->macuocthi }}" {{ old('macuocthi', $macuocthi) == $ct->macuocthi ? 'selected' : '' }}>
                            {{ $ct->tencuocthi }} - {{ $ct->namhoc }} (HK{{ $ct->hocky }})
                        </option>
                        @endforeach
                    </select>
                    @error('macuocthi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Loại đăng ký <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="loaidangky" id="loai_canhan" value="CaNhan" 
                                {{ old('loaidangky') == 'CaNhan' ? 'checked' : '' }} required
                                class="w-5 h-5 text-blue-600">
                            <div class="ml-3">
                                <div class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-user text-blue-500"></i>
                                    Cá nhân
                                </div>
                                <div class="text-xs text-gray-500">Trao giải cho sinh viên cá nhân</div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-500 transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="loaidangky" id="loai_doi" value="DoiNhom" 
                                {{ old('loaidangky') == 'DoiNhom' ? 'checked' : '' }}
                                class="w-5 h-5 text-purple-600">
                            <div class="ml-3">
                                <div class="font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-users text-purple-500"></i>
                                    Đội nhóm
                                </div>
                                <div class="text-xs text-gray-500">Trao giải cho đội thi</div>
                            </div>
                        </label>
                    </div>
                    @error('loaidangky')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Step 2: Chọn người đạt giải --}}
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-8 py-5 border-y border-orange-100">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 bg-orange-500 text-white rounded-full text-sm">2</span>
                    Chọn Người đạt giải
                </h3>
            </div>

            <div class="p-8 space-y-6">
                {{-- Danh sách cá nhân --}}
                <div id="canhan_section" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Sinh viên <span class="text-red-500">*</span>
                    </label>
                    <div id="canhan_list" class="space-y-2 max-h-96 overflow-y-auto">
                        @if($macuocthi && $dangkycanhans->count() > 0)
                            @foreach($dangkycanhans as $dk)
                            <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 transition cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                <input type="radio" name="madangkycanhan" value="{{ $dk->madangkycanhan }}" 
                                    {{ old('madangkycanhan') == $dk->madangkycanhan ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600">
                                <div class="ml-4 flex-1">
                                    <div class="font-semibold text-gray-900">{{ $dk->hoten }}</div>
                                    <div class="text-sm text-gray-600">
                                        MSSV: {{ $dk->masinhvien }} | Lớp: {{ $dk->malop }}
                                        @if($dk->diem_trung_binh > 0)
                                        <span class="ml-2 text-emerald-600 font-semibold">
                                            <i class="fas fa-star text-xs"></i> Điểm TB: {{ number_format($dk->diem_trung_binh, 2) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-info-circle text-3xl mb-2"></i>
                            <p>Vui lòng chọn cuộc thi trước</p>
                        </div>
                        @endif
                    </div>
                    @error('madangkycanhan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Danh sách đội --}}
                <div id="doi_section" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Đội thi <span class="text-red-500">*</span>
                    </label>
                    <div id="doi_list" class="space-y-2 max-h-96 overflow-y-auto">
                        @if($macuocthi && $dangkydois->count() > 0)
                            @foreach($dangkydois as $dk)
                            <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-purple-50 hover:border-purple-300 transition cursor-pointer has-[:checked]:bg-purple-50 has-[:checked]:border-purple-500">
                                <input type="radio" name="madangkydoi" value="{{ $dk->madangkydoi }}" 
                                    {{ old('madangkydoi') == $dk->madangkydoi ? 'checked' : '' }}
                                    class="w-5 h-5 text-purple-600">
                                <div class="ml-4 flex-1">
                                    <div class="font-semibold text-gray-900">{{ $dk->tendoithi }}</div>
                                    <div class="text-sm text-gray-600">
                                        Số thành viên: {{ $dk->sothanhvien }}
                                        @if($dk->diem_trung_binh > 0)
                                        <span class="ml-2 text-emerald-600 font-semibold">
                                            <i class="fas fa-star text-xs"></i> Điểm TB: {{ number_format($dk->diem_trung_binh, 2) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-info-circle text-3xl mb-2"></i>
                            <p>Vui lòng chọn cuộc thi trước</p>
                        </div>
                        @endif
                    </div>
                    @error('madangkydoi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Step 3: Thông tin giải thưởng --}}
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-8 py-5 border-y border-orange-100">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 bg-orange-500 text-white rounded-full text-sm">3</span>
                    Thông tin Giải thưởng
                </h3>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tên giải <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tengiai" value="{{ old('tengiai') }}" required
                            placeholder="VD: Giải Nhất, Giải Xuất sắc..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('tengiai') border-red-500 @enderror">
                        @error('tengiai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Ngày trao giải <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="ngaytrao" value="{{ old('ngaytrao') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('ngaytrao') border-red-500 @enderror">
                        @error('ngaytrao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Giải thưởng
                    </label>
                    <textarea name="giaithuong" rows="3"
                        placeholder="Mô tả chi tiết về giải thưởng (tiền mặt, quà tặng, chứng chỉ...)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('giaithuong') border-red-500 @enderror">{{ old('giaithuong') }}</textarea>
                    @error('giaithuong')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Điểm rèn luyện
                    </label>
                    <input type="number" name="diemrenluyen" value="{{ old('diemrenluyen') }}" 
                        step="0.1" min="0" max="100"
                        placeholder="VD: 10"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('diemrenluyen') border-red-500 @enderror">
                    @error('diemrenluyen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> Điểm rèn luyện thêm cho sinh viên đạt giải
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
                <a href="{{ route('giangvien.giaithuong.index') }}" 
                    class="px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold border border-gray-300 hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
                <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-orange-600 to-red-500 text-white rounded-xl font-bold hover:from-orange-700 hover:to-red-600 transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>Lưu giải thưởng
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const macuocthiSelect = document.getElementById('macuocthi');
    const loaiCaNhan = document.getElementById('loai_canhan');
    const loaiDoi = document.getElementById('loai_doi');
    const canhanSection = document.getElementById('canhan_section');
    const doiSection = document.getElementById('doi_section');

    // Toggle hiển thị section theo loại đăng ký
    function toggleSections() {
        if (loaiCaNhan.checked) {
            canhanSection.style.display = 'block';
            doiSection.style.display = 'none';
        } else if (loaiDoi.checked) {
            canhanSection.style.display = 'none';
            doiSection.style.display = 'block';
        }
    }

    loaiCaNhan.addEventListener('change', toggleSections);
    loaiDoi.addEventListener('change', toggleSections);

    // Load data khi chọn cuộc thi
    macuocthiSelect.addEventListener('change', function() {
        if (this.value) {
            window.location.href = "{{ route('giangvien.giaithuong.create') }}?macuocthi=" + this.value;
        }
    });

    // Initial toggle
    toggleSections();
});
</script>
@endpush

@endsection