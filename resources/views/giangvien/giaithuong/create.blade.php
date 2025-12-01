@extends('layouts.client')
@section('title', 'Thêm cơ cấu giải thưởng - ' . $cuocthi->tencuocthi)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-green-600 via-emerald-600 to-teal-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.show', $cuocthi->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Thêm cơ cấu giải thưởng</span>
        </div>
        
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-plus-circle mr-3"></i>Thêm cơ cấu giải thưởng
        </h1>
        <p class="text-emerald-100">{{ $cuocthi->tencuocthi }}</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- FORM --}}
<section class="container mx-auto px-6 py-12 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-green-100">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-trophy text-green-600 mr-2"></i>Thông tin giải thưởng
            </h2>
            <p class="text-sm text-gray-600 mt-1">Điền đầy đủ thông tin về cơ cấu giải thưởng</p>
        </div>

        <form action="{{ route('giangvien.giaithuong.store', $cuocthi->macuocthi) }}" method="POST" class="p-8 space-y-6">
            @csrf

            {{-- Tên giải --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Tên giải thưởng <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                    name="tengiai" 
                    value="{{ old('tengiai') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('tengiai') border-red-500 @enderror"
                    placeholder="Ví dụ: Giải Nhất, Giải Nhì, Giải Khuyến Khích..."
                    required>
                @error('tengiai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số lượng và Cho phép đồng hạng --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Số lượng <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                        name="soluong" 
                        value="{{ old('soluong', 1) }}" 
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('soluong') border-red-500 @enderror"
                        id="soluong"
                        required>
                    @error('soluong')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Số lượng người/đội có thể nhận giải này</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Cho phép đồng hạng <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" 
                                name="chophepdonghang" 
                                value="0" 
                                {{ old('chophepdonghang', '0') == '0' ? 'checked' : '' }}
                                class="w-5 h-5 text-green-600"
                                onchange="toggleDongHang(false)">
                            <span class="ml-3 text-sm font-medium text-gray-700">Không cho phép</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" 
                                name="chophepdonghang" 
                                value="1" 
                                {{ old('chophepdonghang') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-green-600"
                                onchange="toggleDongHang(true)">
                            <span class="ml-3 text-sm font-medium text-gray-700">Cho phép (không giới hạn số lượng)</span>
                        </label>
                    </div>
                    @error('chophepdonghang')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ghi chú đồng hạng --}}
            <div id="ghichudonghang-container" style="display: {{ old('chophepdonghang') == '1' ? 'block' : 'none' }}">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Ghi chú về đồng hạng
                </label>
                <textarea name="ghichudonghang" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('ghichudonghang') border-red-500 @enderror"
                    placeholder="Điều kiện hoặc ghi chú về việc cho phép đồng hạng...">{{ old('ghichudonghang') }}</textarea>
                @error('ghichudonghang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tiền thưởng và Giấy khen --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Tiền thưởng (VNĐ)
                    </label>
                    <div class="relative">
                        <input type="number" 
                            name="tienthuong" 
                            value="{{ old('tienthuong', 0) }}" 
                            min="0"
                            step="1000"
                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('tienthuong') border-red-500 @enderror"
                            placeholder="0">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">VNĐ</span>
                    </div>
                    @error('tienthuong')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Để 0 nếu không có tiền thưởng</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Giấy khen <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" 
                                name="giaykhen" 
                                value="1" 
                                {{ old('giaykhen', '1') == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-green-600">
                            <span class="ml-3 text-sm font-medium text-gray-700">Có giấy khen</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" 
                                name="giaykhen" 
                                value="0" 
                                {{ old('giaykhen') == '0' ? 'checked' : '' }}
                                class="w-5 h-5 text-green-600">
                            <span class="ml-3 text-sm font-medium text-gray-700">Không có giấy khen</span>
                        </label>
                    </div>
                    @error('giaykhen')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ghi chú --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Ghi chú
                </label>
                <textarea name="ghichu" 
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('ghichu') border-red-500 @enderror"
                    placeholder="Các thông tin bổ sung về giải thưởng...">{{ old('ghichu') }}</textarea>
                @error('ghichu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>Lưu cơ cấu giải thưởng
                </button>
                <a href="{{ route('giangvien.giaithuong.show', $cuocthi->macuocthi) }}" 
                    class="px-8 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
function toggleDongHang(allowed) {
    const container = document.getElementById('ghichudonghang-container');
    const soluongInput = document.getElementById('soluong');
    
    if (allowed) {
        container.style.display = 'block';
        soluongInput.disabled = true;
        soluongInput.value = 999; // Giá trị mặc định khi cho phép đồng hạng
    } else {
        container.style.display = 'none';
        soluongInput.disabled = false;
        if (soluongInput.value == 999) {
            soluongInput.value = 1;
        }
    }
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const dongHangRadios = document.querySelectorAll('input[name="chophepdonghang"]');
    dongHangRadios.forEach(radio => {
        if (radio.checked && radio.value == '1') {
            toggleDongHang(true);
        }
    });
});
</script>
@endpush

@endsection