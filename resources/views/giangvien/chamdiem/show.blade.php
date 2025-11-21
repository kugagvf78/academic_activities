@extends('layouts.client')
@section('title', 'Chấm điểm bài thi')

@section('content')
{{-- HERO SECTION - ĐỔI SANG XANH DƯƠNG --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-12 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.chamdiem.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <h1 class="text-3xl font-black">
            <i class="fas fa-pen-to-square mr-3"></i>Chấm điểm bài thi
        </h1>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section class="container mx-auto px-6 py-12">
    <div class="grid lg:grid-cols-3 gap-8">
        {{-- LEFT: Thông tin sinh viên & bài thi --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Thông tin sinh viên --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-user-graduate mr-2"></i>Thông tin sinh viên
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-md">
                            {{ strtoupper(substr($ketqua->baithi->sinhvien->nguoiDung->hoten ?? 'SV', 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-lg">{{ $ketqua->baithi->sinhvien->nguoiDung->hoten ?? 'N/A' }}</div>
                            <div class="text-gray-500">{{ $ketqua->baithi->sinhvien->masinhvien ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lớp:</span>
                            <span class="font-semibold text-gray-800">{{ $ketqua->baithi->sinhvien->lop ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Khoa:</span>
                            <span class="font-semibold text-gray-800">{{ $ketqua->baithi->sinhvien->khoa ?? 'Công nghệ Thông tin' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold text-gray-800">{{ $ketqua->baithi->sinhvien->nguoiDung->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông tin bài thi --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-file-lines mr-2"></i>Thông tin bài thi
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Cuộc thi</div>
                        <div class="font-semibold text-gray-800">{{ $ketqua->baithi->dethi->cuocthi->tencuocthi ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Đề thi</div>
                        <div class="font-semibold text-gray-800">{{ $ketqua->baithi->dethi->tendethi ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Loại đề</div>
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $ketqua->baithi->dethi->loaidethi ?? 'Tự luận' }}
                        </span>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Thời gian nộp bài</div>
                        <div class="font-semibold text-gray-800">
                            <i class="far fa-clock text-gray-400 mr-1"></i>
                            {{ $ketqua->created_at ? \Carbon\Carbon::parse($ketqua->created_at)->format('H:i d/m/Y') : 'N/A' }}
                        </div>
                    </div>

                    {{-- File đề thi --}}
                    @if($ketqua->baithi->dethi->filepath)
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('giangvien.dethi.download-file', $ketqua->baithi->dethi->madethi) }}" 
                            class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-download"></i>
                            <span>Tải đề thi gốc</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Form chấm điểm --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-clipboard-check mr-2"></i>Chấm điểm & Nhận xét
                    </h3>
                </div>

                <form action="{{ route('giangvien.chamdiem.update', $ketqua->maketqua) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    {{-- Bài làm của sinh viên --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-3">
                            <i class="fas fa-file-alt text-blue-500 mr-2"></i>Bài làm của sinh viên
                        </label>
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                            @if($ketqua->baithi->filepath)
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">Bài làm đã nộp</div>
                                            <div class="text-sm text-gray-500">{{ basename($ketqua->baithi->filepath) }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($ketqua->baithi->filepath) }}" 
                                        target="_blank"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                                        <i class="fas fa-eye"></i>
                                        <span>Xem bài</span>
                                    </a>
                                </div>
                            @endif

                            @if($ketqua->baithi->noidung)
                                <div class="prose max-w-none">
                                    <div class="text-gray-600 text-sm mb-2 font-semibold">Nội dung bài làm:</div>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                                        {!! nl2br(e($ketqua->baithi->noidung)) !!}
                                    </div>
                                </div>
                            @endif

                            @if(!$ketqua->baithi->filepath && !$ketqua->baithi->noidung)
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Sinh viên chưa nộp bài làm</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Điểm số --}}
                    <div class="mb-6">
                        <label for="diem" class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>Điểm số <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                name="diem" 
                                id="diem" 
                                min="0" 
                                max="10" 
                                step="0.01" 
                                value="{{ old('diem', $ketqua->diem) }}" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('diem') border-red-500 @enderror">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">/10</span>
                        </div>
                        @error('diem')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Nhập điểm từ 0 đến 10 (có thể dùng số thập phân, ví dụ: 8.5)
                        </p>
                    </div>

                    {{-- Nhận xét --}}
                    <div class="mb-6">
                        <label for="nhanxet" class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-comment-dots text-blue-500 mr-2"></i>Nhận xét
                        </label>
                        <textarea name="nhanxet" 
                            id="nhanxet" 
                            rows="6" 
                            placeholder="Nhận xét về bài làm của sinh viên..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none @error('nhanxet') border-red-500 @enderror">{{ old('nhanxet', $ketqua->nhanxet) }}</textarea>
                        @error('nhanxet')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex gap-3 pt-6 border-t border-gray-200">
                        <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105 inline-flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            <span>Lưu điểm & Nhận xét</span>
                        </button>
                        <a href="{{ route('giangvien.chamdiem.index') }}" 
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition inline-flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            <span>Hủy</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Lịch sử chấm điểm --}}
            @if($ketqua->diem !== null)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mt-6">
                <h4 class="font-bold text-blue-800 mb-3">
                    <i class="fas fa-history mr-2"></i>Thông tin chấm điểm hiện tại
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Điểm hiện tại:</span>
                        <span class="font-bold text-blue-700 text-lg">{{ $ketqua->diem }}/10</span>
                    </div>
                    @if($ketqua->nguoichamdiem)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Người chấm:</span>
                        <span class="font-semibold">Giảng viên #{{ $ketqua->nguoichamdiem }}</span>
                    </div>
                    @endif
                    @if($ketqua->updated_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cập nhật lần cuối:</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($ketqua->updated_at)->format('H:i d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Giới hạn điểm từ 0-10
    document.getElementById('diem').addEventListener('input', function(e) {
        let value = parseFloat(e.target.value);
        if (isNaN(value) || value < 0) e.target.value = 0;
        if (value > 10) e.target.value = 10;
    });

    // Xác nhận trước khi lưu
    document.querySelector('form').addEventListener('submit', function(e) {
        const diem = document.getElementById('diem').value.trim();
        if (!diem) {
            e.preventDefault();
            alert('Vui lòng nhập điểm trước khi lưu!');
            return false;
        }
        
        return confirm(`Xác nhận lưu điểm ${diem}/10 cho sinh viên này?`);
    });
</script>
@endpush

@endsection