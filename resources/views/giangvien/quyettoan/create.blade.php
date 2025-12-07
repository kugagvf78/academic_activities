@extends('layouts.client')

@section('title', 'Lập Quyết toán mới')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black mb-2">Lập Quyết toán mới</h1>
                <p class="text-blue-100">Tạo hồ sơ quyết toán cho cuộc thi đã hoàn thành</p>
            </div>
            <a href="{{ route('giangvien.quyettoan.index') }}" 
                class="hidden md:flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur-sm text-white rounded-xl font-semibold hover:bg-white/20 transition border border-white/20">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại</span>
            </a>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section class="container mx-auto px-6 -mt-8 relative z-20 pb-12">
    {{-- Mobile: Nút quay lại --}}
    <div class="md:hidden mb-6">
        <a href="{{ route('giangvien.quyettoan.index') }}" 
            class="inline-flex items-center gap-2 text-blue-600 font-semibold">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    {{-- Thông báo lỗi --}}
    @if(session('error'))
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-2xl"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-2xl mt-1"></i>
                <div>
                    <div class="font-semibold mb-2">Có lỗi xảy ra:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form tạo quyết toán --}}
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-6">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Thông tin Quyết toán</span>
            </h2>
        </div>

        <form action="{{ route('giangvien.quyettoan.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf

            @if($cuocthis->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-info-circle text-6xl text-blue-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Không có cuộc thi nào để lập quyết toán</h3>
                    <p class="text-gray-500 mb-6">Chỉ có thể lập quyết toán cho các cuộc thi đã hoàn thành và chưa có quyết toán.</p>
                    <a href="{{ route('giangvien.quyettoan.index') }}" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition">
                        <i class="fas fa-arrow-left"></i>
                        <span>Quay lại danh sách</span>
                    </a>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Chọn cuộc thi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Cuộc thi <span class="text-red-500">*</span>
                        </label>
                        <select name="macuocthi" id="macuocthi" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">-- Chọn cuộc thi --</option>
                            @foreach($cuocthis as $ct)
                                <option value="{{ $ct->macuocthi }}" 
                                    data-dutru="{{ $ct->dutrukinhphi }}"
                                    data-thucte="{{ $ct->chiphithucte }}"
                                    {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                    {{ $ct->tencuocthi }}
                                </option>
                            @endforeach
                        </select>
                        @error('macuocthi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tổng dự trù --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Tổng dự trù <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="tongdutru" id="tongdutru" required min="0" step="1000"
                                value="{{ old('tongdutru') }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="0">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₫</span>
                        </div>
                        @error('tongdutru')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <button type="button" id="btn-auto-calculate" 
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                            <i class="fas fa-calculator"></i>
                            <span>Tính toán tự động từ chi phí</span>
                        </button>
                    </div>

                    {{-- Tổng thực tế (TỰ ĐỘNG TÍNH, KHÔNG CHO NHẬP) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Tổng thực tế <span class="text-red-500">*</span>
                            <span class="ml-2 text-xs font-normal text-gray-500">(Tự động tính từ chi phí)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="tongthucte" id="tongthucte" required min="0" step="1000"
                                value="{{ old('tongthucte') }}"
                                readonly
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl bg-gray-50 cursor-not-allowed text-gray-600"
                                placeholder="0">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">₫</span>
                        </div>
                        @error('tongthucte')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            <span>Tổng tự động được tính từ các chi phí đã được duyệt</span>
                        </p>
                    </div>

                    {{-- Chênh lệch (tự động tính) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Chênh lệch (Dự trù - Thực tế)
                        </label>
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl px-6 py-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 font-semibold">Chênh lệch:</span>
                                <span id="chenhlech-display" class="text-2xl font-bold text-blue-600">0 ₫</span>
                            </div>
                        </div>
                    </div>

                    {{-- File quyết toán --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            File quyết toán (PDF)
                        </label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-400 transition">
                            <input type="file" name="filequyettoan" id="filequyettoan" accept=".pdf"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 font-semibold">Nhấn để chọn file PDF</p>
                                <p class="text-sm text-gray-500 mt-1">Dung lượng tối đa: 10MB</p>
                                <p id="file-name" class="text-sm text-blue-600 font-semibold mt-2"></p>
                            </div>
                        </div>
                        @error('filequyettoan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ghi chú --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Ghi chú
                        </label>
                        <textarea name="ghichu" id="ghichu" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                            placeholder="Nhập ghi chú, diễn giải về quyết toán...">{{ old('ghichu') }}</textarea>
                        @error('ghichu')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Lưu quyết toán</span>
                    </button>
                    <a href="{{ route('giangvien.quyettoan.index') }}"
                        class="flex-1 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition text-center flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Hủy bỏ</span>
                    </a>
                </div>
            @endif
        </form>
    </div>
</section>

@push('scripts')
<script>
// Tính chênh lệch tự động
function calculateChenhlech() {
    const dutru = parseFloat(document.getElementById('tongdutru').value) || 0;
    const thucte = parseFloat(document.getElementById('tongthucte').value) || 0;
    const chenhlech = dutru - thucte;
    
    const display = document.getElementById('chenhlech-display');
    display.textContent = new Intl.NumberFormat('vi-VN').format(chenhlech) + ' ₫';
    
    // Đổi màu theo giá trị
    if (chenhlech > 0) {
        display.className = 'text-2xl font-bold text-green-600';
    } else if (chenhlech < 0) {
        display.className = 'text-2xl font-bold text-red-600';
    } else {
        display.className = 'text-2xl font-bold text-blue-600';
    }
}

// Lắng nghe sự thay đổi
document.getElementById('tongdutru').addEventListener('input', calculateChenhlech);
document.getElementById('tongthucte').addEventListener('input', calculateChenhlech);

// Hiển thị tên file được chọn
document.getElementById('filequyettoan').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const display = document.getElementById('file-name');
    if (fileName) {
        display.textContent = '📄 ' + fileName;
    } else {
        display.textContent = '';
    }
});

// TỰ ĐỘNG TÍNH TỔNG THỰC TẾ khi chọn cuộc thi
document.getElementById('macuocthi').addEventListener('change', async function() {
    const macuocthi = this.value;
    
    if (!macuocthi) {
        document.getElementById('tongdutru').value = '';
        document.getElementById('tongthucte').value = '';
        calculateChenhlech();
        return;
    }
    
    // Gọi API để lấy tổng chi phí thực tế
    try {
        const response = await fetch(`{{ route('giangvien.quyettoan.api.auto-calculate', ':macuocthi') }}`.replace(':macuocthi', macuocthi));
        const data = await response.json();
        
        // Cập nhật giá trị
        document.getElementById('tongdutru').value = data.tongdutru;
        document.getElementById('tongthucte').value = data.tongthucte;
        calculateChenhlech();
        
        // Hiển thị thông báo nhỏ
        const thucteInput = document.getElementById('tongthucte');
        thucteInput.classList.add('ring-2', 'ring-green-500');
        setTimeout(() => {
            thucteInput.classList.remove('ring-2', 'ring-green-500');
        }, 1500);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tính toán tự động!');
    }
});

// Tính toán tự động từ chi phí (nút bấm)
document.getElementById('btn-auto-calculate').addEventListener('click', async function() {
    const macuocthi = document.getElementById('macuocthi').value;
    
    if (!macuocthi) {
        alert('Vui lòng chọn cuộc thi trước!');
        return;
    }
    
    try {
        const btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tính toán...';
        btn.disabled = true;
        
        const response = await fetch(`{{ route('giangvien.quyettoan.api.auto-calculate', ':macuocthi') }}`.replace(':macuocthi', macuocthi));
        const data = await response.json();
        
        document.getElementById('tongdutru').value = data.tongdutru;
        document.getElementById('tongthucte').value = data.tongthucte;
        calculateChenhlech();
        
        btn.innerHTML = '<i class="fas fa-check"></i> Đã cập nhật!';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-calculator"></i> Tính toán tự động từ chi phí';
            btn.disabled = false;
        }, 2000);
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tính toán tự động!');
        this.innerHTML = '<i class="fas fa-calculator"></i> Tính toán tự động từ chi phí';
        this.disabled = false;
    }
});

// Tính chênh lệch khi load trang (nếu có giá trị cũ)
calculateChenhlech();
</script>
@endpush

@endsection