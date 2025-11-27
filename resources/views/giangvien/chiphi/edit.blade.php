@extends('layouts.client')

@section('title', 'Chỉnh sửa Chi phí')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-emerald-700 via-green-600 to-teal-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('giangvien.chiphi.show', $chiphi->machiphi) }}" 
                class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black">Chỉnh sửa Chi phí</h1>
                <p class="text-green-100">Mã: {{ $chiphi->machiphi }}</p>
            </div>
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
    <div class="max-w-4xl mx-auto">
        {{-- Thông báo lỗi --}}
        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-md">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-2xl"></i>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            {{-- Form Header --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        Chỉnh sửa thông tin Chi phí
                    </h2>
                    @if($chiphi->trangthai == 'Rejected')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            <i class="fas fa-exclamation-circle mr-1"></i>Đã bị từ chối
                        </span>
                    @elseif($chiphi->trangthai == 'Pending')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                            <i class="fas fa-clock mr-1"></i>Chờ duyệt
                        </span>
                    @endif
                </div>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('giangvien.chiphi.update', $chiphi->machiphi) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                {{-- Cuộc thi (chỉ hiển thị, không cho sửa) --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cuộc thi</label>
                    <div class="bg-gray-50 px-4 py-3 rounded-xl border border-gray-200">
                        <p class="text-gray-800 font-semibold">{{ $chiphi->tencuocthi }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-lock mr-1"></i>
                            Không thể thay đổi cuộc thi sau khi tạo
                        </p>
                    </div>
                </div>

                {{-- Tên khoản chi --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Tên khoản chi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        name="tenkhoanchi" 
                        value="{{ old('tenkhoanchi', $chiphi->tenkhoanchi) }}"
                        placeholder="VD: Chi phí in ấn, Chi phí giải thưởng..."
                        maxlength="300"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('tenkhoanchi') border-red-500 @enderror">
                    @error('tenkhoanchi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dự trù chi phí và Thực tế chi --}}
                @if($chiphi->trangthai == 'Approved')
                    {{-- Khi đã duyệt: Chỉ cho cập nhật thực tế chi --}}
                    <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Dự trù chi phí (₫)
                        </label>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ number_format($chiphi->dutruchiphi, 0, ',', '.') }} ₫
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-lock mr-1"></i>
                            Không thể thay đổi dự trù sau khi đã duyệt
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Thực tế chi (₫) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                name="thuctechi" 
                                value="{{ old('thuctechi', $chiphi->thuctechi) }}"
                                placeholder="0"
                                min="0"
                                step="1000"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('thuctechi') border-red-500 @enderror">
                            @error('thuctechi')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Số tiền đã chi thực tế
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Ngày chi <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                name="ngaychi" 
                                value="{{ old('ngaychi', $chiphi->ngaychi) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('ngaychi') border-red-500 @enderror">
                            @error('ngaychi')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    {{-- Khi Pending/Rejected: Chỉ cho sửa dự trù --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Dự trù chi phí (₫) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                            name="dutruchiphi" 
                            value="{{ old('dutruchiphi', $chiphi->dutruchiphi) }}"
                            placeholder="0"
                            min="0"
                            step="1000"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('dutruchiphi') border-red-500 @enderror">
                        @error('dutruchiphi')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-amber-600 bg-amber-50 px-3 py-2 rounded-lg">
                            <i class="fas fa-info-circle mr-1"></i>
                            Thực tế chi sẽ được cập nhật sau khi yêu cầu được duyệt
                        </p>
                    </div>

                    @if($chiphi->thuctechi)
                    <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Thực tế đã chi
                        </label>
                        <p class="text-xl font-bold text-green-600">
                            {{ number_format($chiphi->thuctechi, 0, ',', '.') }} ₫
                        </p>
                        @if($chiphi->ngaychi)
                        <p class="text-sm text-gray-500 mt-1">
                            Ngày chi: {{ \Carbon\Carbon::parse($chiphi->ngaychi)->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                    @endif
                @endif

                {{-- Upload chứng từ --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Chứng từ (PDF, JPG, PNG - Max 5MB)
                    </label>
                    
                    @if($chiphi->chungtu)
                        <div class="mb-3 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-pdf text-blue-500 text-2xl"></i>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">File hiện tại</p>
                                    <p class="text-xs text-gray-500">{{ basename($chiphi->chungtu) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('giangvien.chiphi.download-chung-tu', $chiphi->machiphi) }}" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                <i class="fas fa-download mr-1"></i>Tải xuống
                            </a>
                        </div>
                    @endif

                    <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-green-500 transition">
                        <input type="file" 
                            name="chungtu" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            id="chungtu-input"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 font-medium">
                                @if($chiphi->chungtu)
                                    Click để thay đổi file chứng từ
                                @else
                                    Click hoặc kéo file vào đây
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 mt-1">PDF, JPG, PNG (tối đa 5MB)</p>
                            <p id="file-name" class="text-sm text-green-600 font-semibold mt-2 hidden"></p>
                        </div>
                    </div>
                    @error('chungtu')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ghi chú --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Ghi chú
                    </label>
                    <textarea name="ghichu" 
                        rows="4"
                        placeholder="Nhập ghi chú về khoản chi này..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('ghichu') border-red-500 @enderror">{{ old('ghichu', $chiphi->ghichu) }}</textarea>
                    @error('ghichu')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thông báo trạng thái --}}
                @if($chiphi->trangthai == 'Rejected')
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-400 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-semibold text-red-800 mb-2">Chi phí đã bị từ chối</p>
                                <p class="text-red-700 mb-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Sau khi chỉnh sửa, chi phí sẽ được chuyển về trạng thái "Chờ duyệt" và gửi lại cho trưởng bộ môn
                                </p>
                                @if(strpos($chiphi->ghichu, 'LÝ DO TỪ CHỐI:') !== false)
                                    <div class="bg-white rounded-lg p-3 mt-2">
                                        <p class="text-xs font-semibold text-gray-600 mb-1">Lý do từ chối:</p>
                                        <p class="text-sm text-gray-700">
                                            {{ trim(substr($chiphi->ghichu, strpos($chiphi->ghichu, 'LÝ DO TỪ CHỐI:') + 15)) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($chiphi->trangthai == 'Pending')
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-clock text-yellow-600 text-xl mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-semibold mb-1">Chi phí đang chờ duyệt</p>
                                <p class="text-yellow-700">
                                    Bạn có thể chỉnh sửa thông tin trước khi trưởng bộ môn duyệt
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Buttons --}}
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-600 to-teal-500 text-white px-6 py-3 rounded-xl font-bold hover:from-green-700 hover:to-teal-600 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>
                        @if($chiphi->trangthai == 'Rejected')
                            Cập nhật và gửi lại
                        @elseif($chiphi->trangthai == 'Approved')
                            Cập nhật thực tế chi
                        @else
                            Cập nhật dự trù chi phí
                        @endif
                    </button>
                    <a href="{{ route('giangvien.chiphi.show', $chiphi->machiphi) }}" 
                        class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                        <i class="fas fa-times mr-2"></i>Hủy
                    </a>
                </div>
            </form>
        </div>

        {{-- Lịch sử yêu cầu --}}
        <div class="mt-6 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-8 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-gray-600"></i>
                    Lịch sử yêu cầu
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    {{-- Ngày tạo yêu cầu --}}
                    @if($chiphi->ngayyeucau)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-plus-circle text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Yêu cầu được tạo</p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($chiphi->ngayyeucau)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Ngày duyệt (nếu có) --}}
                    @if($chiphi->ngayduyet && $chiphi->trangthai == 'Approved')
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Đã được duyệt</p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($chiphi->ngayduyet)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @elseif($chiphi->ngayduyet && $chiphi->trangthai == 'Rejected')
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Đã bị từ chối</p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($chiphi->ngayduyet)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Preview file name
document.getElementById('chungtu-input').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = '📄 File mới: ' + fileName;
        fileNameDisplay.classList.remove('hidden');
    }
});
</script>
@endpush

@endsection