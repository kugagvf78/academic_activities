@extends('layouts.client')

@section('title', 'Tạo Chi phí Mới')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-emerald-700 via-green-600 to-teal-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('giangvien.chiphi.index') }}" 
                class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black">Tạo Chi phí Mới</h1>
                <p class="text-green-100">Thêm khoản chi phí cho cuộc thi</p>
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
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-white"></i>
                    </div>
                    Thông tin Chi phí
                </h2>
                <p class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Yêu cầu sẽ được gửi đến trưởng bộ môn để duyệt
                </p>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('giangvien.chiphi.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                {{-- Thông tin người yêu cầu (hiển thị) --}}
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Người yêu cầu</label>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ strtoupper(substr(jwt_user()->hoten ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ jwt_user()->hoten ?? 'N/A' }}</p>
                            @if(jwt_user()->email)
                                <p class="text-sm text-gray-600 flex items-center gap-1">
                                    <i class="fas fa-envelope text-xs"></i>
                                    {{ jwt_user()->email }}
                                </p>
                            @endif
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="far fa-calendar-alt mr-1"></i>
                                Ngày yêu cầu: {{ date('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Chọn cuộc thi --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Cuộc thi <span class="text-red-500">*</span>
                    </label>
                    <select name="macuocthi" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('macuocthi') border-red-500 @enderror">
                        <option value="">-- Chọn cuộc thi --</option>
                        @foreach($cuocthis as $ct)
                            <option value="{{ $ct->macuocthi }}" {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                {{ $ct->tencuocthi }} (Dự trù: {{ number_format($ct->dutrukinhphi, 0, ',', '.') }} ₫)
                            </option>
                        @endforeach
                    </select>
                    @error('macuocthi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tên khoản chi --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Tên khoản chi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        name="tenkhoanchi" 
                        value="{{ old('tenkhoanchi') }}"
                        placeholder="VD: Chi phí in ấn, Chi phí giải thưởng, Chi phí tổ chức..."
                        maxlength="300"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('tenkhoanchi') border-red-500 @enderror">
                    @error('tenkhoanchi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dự trù chi phí --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Dự trù chi phí (₫) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                        name="dutruchiphi" 
                        value="{{ old('dutruchiphi') }}"
                        placeholder="0"
                        min="0"
                        step="1000"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('dutruchiphi') border-red-500 @enderror">
                    @error('dutruchiphi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-blue-600 bg-blue-50 px-3 py-2 rounded-lg">
                        <i class="fas fa-info-circle mr-1"></i>
                        Số tiền dự kiến chi cho khoản này. Thực tế chi sẽ được cập nhật sau khi được duyệt.
                    </p>
                </div>

                {{-- Ngày chi --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Ngày chi
                    </label>
                    <input type="date" 
                        name="ngaychi" 
                        value="{{ old('ngaychi', date('Y-m-d')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('ngaychi') border-red-500 @enderror">
                    @error('ngaychi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload chứng từ --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Chứng từ (PDF, JPG, PNG - Max 5MB)
                    </label>
                    <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-green-500 transition">
                        <input type="file" 
                            name="chungtu" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            id="chungtu-input"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 font-medium">Click hoặc kéo file vào đây</p>
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition @error('ghichu') border-red-500 @enderror">{{ old('ghichu') }}</textarea>
                    @error('ghichu')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thông báo quan trọng --}}
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-semibold mb-1">Lưu ý:</p>
                            <ul class="list-disc list-inside space-y-1 text-yellow-700">
                                <li>Chi phí sau khi tạo sẽ ở trạng thái "Chờ duyệt"</li>
                                <li>Trưởng bộ môn sẽ xem xét và duyệt yêu cầu của bạn</li>
                                <li>Bạn có thể chỉnh sửa chi phí khi đang chờ duyệt hoặc bị từ chối</li>
                                <li>Sau khi được duyệt, chi phí không thể chỉnh sửa</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-600 to-teal-500 text-white px-6 py-3 rounded-xl font-bold hover:from-green-700 hover:to-teal-600 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-paper-plane mr-2"></i>Gửi yêu cầu chi phí
                    </button>
                    <a href="{{ route('giangvien.chiphi.index') }}" 
                        class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                        <i class="fas fa-times mr-2"></i>Hủy
                    </a>
                </div>
            </form>
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
        fileNameDisplay.textContent = '📄 ' + fileName;
        fileNameDisplay.classList.remove('hidden');
    }
});
</script>
@endpush

@endsection