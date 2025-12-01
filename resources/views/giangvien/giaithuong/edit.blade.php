@extends('layouts.client')
@section('title', 'Chỉnh sửa cơ cấu giải thưởng')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-orange-600 to-red-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.show', $cocau->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Chỉnh sửa cơ cấu giải thưởng</span>
        </div>
        
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-edit mr-3"></i>Chỉnh sửa cơ cấu giải thưởng
        </h1>
        <p class="text-orange-100">{{ $cocau->tengiai }} - {{ $cocau->cuocthi->tencuocthi }}</p>
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
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-8 py-6 border-b border-amber-100">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-trophy text-amber-600 mr-2"></i>Cập nhật thông tin giải thưởng
            </h2>
            <p class="text-sm text-gray-600 mt-1">Chỉnh sửa thông tin về cơ cấu giải thưởng</p>
        </div>

        <form action="{{ route('giangvien.giaithuong.update', $cocau->macocau) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Tên giải --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Tên giải thưởng <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                    name="tengiai" 
                    value="{{ old('tengiai', $cocau->tengiai) }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition @error('tengiai') border-red-500 @enderror"
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
                        value="{{ old('soluong', $cocau->soluong) }}" 
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition @error('soluong') border-red-500 @enderror"
                        id="soluong"
                        required>
                    @error('soluong')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @php
                        $daGan = $cocau->gangiaithuong()->whereIn('trangthai', ['Pending', 'Approved'])->count();
                    @endphp
                    @if($daGan > 0)
                        <p class="text-xs text-amber-600 mt-1 font-semibold">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Đã gán {{ $daGan }} giải, không thể giảm xuống dưới {{ $daGan }}
                        </p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Số lượng người/đội có thể nhận giải này</p>
                    @endif
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
                                {{ old('chophepdonghang', $cocau->chophepdonghang) == '0' ? 'checked' : '' }}
                                class="w-5 h-5 text-amber-600"
                                onchange="toggleDongHang(false)">
                            <span class="ml-3 text-sm font-medium text-gray-700">Không cho phép</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" 
                                name="chophepdonghang" 
                                value="1" 
                                {{ old('chophepdonghang', $cocau->chophepdonghang) == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-amber-600"
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
            <div id="ghichudongkang-container" style="display: {{ old('chophepdongkang', $cocau->chophepdongkang) == '1' ? 'block' : 'none' }}">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Ghi chú về đồng hạng
                </label>
                <textarea name="ghichudongkang" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition @error('ghichudongkang') border-red-500 @enderror"
                    placeholder="Điều kiện hoặc ghi chú về việc cho phép đồng hạng...">{{ old('ghichudongkang', $cocau->ghichudongkang) }}</textarea>
                @error('ghichudongkang')
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
                            value="{{ old('tienthuong', $cocau->tienthuong) }}" 
                            min="0"
                            step="1000"
                            class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition @error('tienthuong') border-red-500 @enderror"
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
                                {{ old('giaykhen', $cocau->giaykhen) == '1' ? 'checked' : '' }}
                                class="w-5 h-5 text-amber-600">
                            <span class="ml-3 text-sm font-medium text-gray-700">Có giấy khen</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" 
                                name="giaykhen" 
                                value="0" 
                                {{ old('giaykhen', $cocau->giaykhen) == '0' ? 'checked' : '' }}
                                class="w-5 h-5 text-amber-600">
                            <span class="ml-3 text-sm font-medium text-gray-700">Không có giấy khen</span>
                        </label>
                    </div>
                    @error('giaykhen')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Trạng thái <span class="text-red-500">*</span>
                </label>
                <div class="grid md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" 
                            name="trangthai" 
                            value="Active" 
                            {{ old('trangthai', $cocau->trangthai) == 'Active' ? 'checked' : '' }}
                            class="w-5 h-5 text-amber-600">
                        <div class="ml-3">
                            <div class="text-sm font-semibold text-gray-700">Kích hoạt</div>
                            <div class="text-xs text-gray-500">Giải thưởng có thể được sử dụng</div>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" 
                            name="trangthai" 
                            value="Inactive" 
                            {{ old('trangthai', $cocau->trangthai) == 'Inactive' ? 'checked' : '' }}
                            class="w-5 h-5 text-amber-600">
                        <div class="ml-3">
                            <div class="text-sm font-semibold text-gray-700">Tạm ngưng</div>
                            <div class="text-xs text-gray-500">Giải thưởng tạm thời không sử dụng</div>
                        </div>
                    </label>
                </div>
                @error('trangthai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ghi chú --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Ghi chú
                </label>
                <textarea name="ghichu" 
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition @error('ghichu') border-red-500 @enderror"
                    placeholder="Các thông tin bổ sung về giải thưởng...">{{ old('ghichu', $cocau->ghichu) }}</textarea>
                @error('ghichu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>Cập nhật cơ cấu giải thưởng
                </button>
                <a href="{{ route('giangvien.giaithuong.show', $cocau->macuocthi) }}" 
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
    const container = document.getElementById('ghichudongkang-container');
    const soluongInput = document.getElementById('soluong');
    
    if (allowed) {
        container.style.display = 'block';
        soluongInput.disabled = true;
        soluongInput.value = 999; // Giá trị mặc định khi cho phép đồng hạng
    } else {
        container.style.display = 'none';
        soluongInput.disabled = false;
        if (soluongInput.value == 999) {
            soluongInput.value = {{ $cocau->soluong }};
        }
    }
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const dongHangRadios = document.querySelectorAll('input[name="chophepdongkang"]');
    dongHangRadios.forEach(radio => {
        if (radio.checked && radio.value == '1') {
            toggleDongHang(true);
        }
    });
});
</script>
@endpush

@endsection