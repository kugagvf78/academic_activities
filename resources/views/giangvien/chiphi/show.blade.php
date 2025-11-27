@extends('layouts.client')

@section('title', 'Chi tiết Chi phí')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-emerald-700 via-green-600 to-teal-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('giangvien.chiphi.index') }}" 
                class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black">Chi tiết Chi phí</h1>
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
    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-2xl"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-5xl mx-auto">
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Chi tiết chính --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">Thông tin Chi phí</h2>
                            @if($chiphi->trangthai == 'Pending')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i>Chờ duyệt
                                </span>
                            @elseif($chiphi->trangthai == 'Approved')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Đã duyệt
                                </span>
                            @elseif($chiphi->trangthai == 'Rejected')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle mr-1"></i>Từ chối
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-8 space-y-6">
                        {{-- Mã chi phí --}}
                        <div class="flex items-center gap-4 pb-6 border-b border-gray-200">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hashtag text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mã chi phí</p>
                                <p class="text-xl font-bold text-green-600">{{ $chiphi->machiphi }}</p>
                            </div>
                        </div>

                        {{-- Tên khoản chi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-1">Tên khoản chi</label>
                            <p class="text-lg font-bold text-gray-800">{{ $chiphi->tenkhoanchi }}</p>
                        </div>

                        {{-- Cuộc thi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-1">Cuộc thi</label>
                            <p class="text-gray-800">{{ $chiphi->tencuocthi }}</p>
                        </div>

                        {{-- Bộ môn --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-1">Bộ môn</label>
                            <p class="text-gray-800">{{ $chiphi->tenbomon }}</p>
                        </div>

                        {{-- Người yêu cầu --}}
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Người yêu cầu</label>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ strtoupper(substr($chiphi->tennguoiyeucau ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $chiphi->tennguoiyeucau ?? 'N/A' }}</p>
                                    @if($chiphi->emailnguoiyeucau)
                                        <p class="text-sm text-gray-600 flex items-center gap-1">
                                            <i class="fas fa-envelope text-xs"></i>
                                            {{ $chiphi->emailnguoiyeucau }}
                                        </p>
                                    @endif
                                    @if($chiphi->ngayyeucau)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            Ngày yêu cầu: {{ \Carbon\Carbon::parse($chiphi->ngayyeucau)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Chi phí --}}
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-xl p-4">
                                <label class="block text-sm font-bold text-blue-600 mb-1">Dự trù chi phí</label>
                                <p class="text-2xl font-bold text-blue-700">{{ number_format($chiphi->dutruchiphi, 0, ',', '.') }} ₫</p>
                            </div>
                            <div class="bg-green-50 rounded-xl p-4">
                                <label class="block text-sm font-bold text-green-600 mb-1">Thực tế chi</label>
                                <p class="text-2xl font-bold text-green-700">
                                    {{ $chiphi->thuctechi ? number_format($chiphi->thuctechi, 0, ',', '.') . ' ₫' : 'Chưa có' }}
                                </p>
                            </div>
                        </div>

                        {{-- Ngày chi --}}
                        @if($chiphi->ngaychi)
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-1">Ngày chi</label>
                            <p class="text-gray-800 flex items-center gap-2">
                                <i class="fas fa-calendar text-green-500"></i>
                                {{ \Carbon\Carbon::parse($chiphi->ngaychi)->format('d/m/Y') }}
                            </p>
                        </div>
                        @endif

                        {{-- Người duyệt --}}
                        @if($chiphi->tennguoiduyet)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Người duyệt</label>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ strtoupper(substr($chiphi->tennguoiduyet, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $chiphi->tennguoiduyet }}</p>
                                    @if($chiphi->emailnguoiduyet)
                                        <p class="text-sm text-gray-600 flex items-center gap-1">
                                            <i class="fas fa-envelope text-xs"></i>
                                            {{ $chiphi->emailnguoiduyet }}
                                        </p>
                                    @endif
                                    @if($chiphi->ngayduyet)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="far fa-calendar-check mr-1"></i>
                                            Ngày duyệt: {{ \Carbon\Carbon::parse($chiphi->ngayduyet)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Ghi chú --}}
                        @if($chiphi->ghichu)
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-1">Ghi chú</label>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-gray-700 whitespace-pre-line">{{ $chiphi->ghichu }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Thao tác</h3>
                    <div class="space-y-3">
                        {{-- Chỉnh sửa (chỉ người tạo) --}}
                        @if($isOwner)
                            @if(in_array($chiphi->trangthai, ['Pending', 'Rejected']))
                                {{-- Sửa dự trù khi Pending/Rejected --}}
                                <a href="{{ route('giangvien.chiphi.edit', $chiphi->machiphi) }}" 
                                    class="block w-full bg-gradient-to-r from-green-600 to-teal-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-teal-600 transition text-center shadow-md">
                                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa dự trù
                                </a>
                                
                                <form action="{{ route('giangvien.chiphi.destroy', $chiphi->machiphi) }}" 
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa chi phí này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="w-full bg-red-500 text-white px-4 py-3 rounded-xl font-semibold hover:bg-red-600 transition shadow-md">
                                        <i class="fas fa-trash mr-2"></i>Xóa
                                    </button>
                                </form>
                            @elseif($chiphi->trangthai == 'Approved')
                                {{-- Cập nhật thực tế chi khi đã duyệt --}}
                                <a href="{{ route('giangvien.chiphi.edit', $chiphi->machiphi) }}" 
                                    class="block w-full bg-gradient-to-r from-blue-600 to-indigo-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-600 transition text-center shadow-md">
                                    <i class="fas fa-money-bill-wave mr-2"></i>Cập nhật thực tế chi
                                </a>
                            @endif
                        @endif

                        {{-- Duyệt/Từ chối (chỉ Trưởng bộ môn và không phải chi phí của mình) --}}
                        @if($isTruongBoMon && !$isOwner && $chiphi->trangthai == 'Pending')
                            <button onclick="openApproveModal()" 
                                class="w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-600 transition shadow-md">
                                <i class="fas fa-check-circle mr-2"></i>Duyệt
                            </button>
                            
                            <button onclick="openRejectModal()" 
                                class="w-full bg-gradient-to-r from-orange-600 to-orange-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-orange-700 hover:to-orange-600 transition shadow-md">
                                <i class="fas fa-times-circle mr-2"></i>Từ chối
                            </button>
                        @endif

                        <a href="{{ route('giangvien.chiphi.index') }}" 
                            class="block w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition text-center">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                    </div>
                </div>

                {{-- Chứng từ --}}
                @if($chiphi->chungtu)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Chứng từ</h3>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center">
                        <i class="fas fa-file-pdf text-5xl text-blue-500 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-4">File chứng từ đã tải lên</p>
                        <a href="{{ route('giangvien.chiphi.download-chung-tu', $chiphi->machiphi) }}" 
                            class="inline-block bg-blue-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-download mr-2"></i>Tải xuống
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Modal Duyệt --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">Xác nhận duyệt</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Bạn có chắc chắn muốn duyệt chi phí này?</p>
            <form action="{{ route('giangvien.chiphi.approve', $chiphi->machiphi) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-600 to-teal-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-teal-600 transition">
                        <i class="fas fa-check mr-2"></i>Xác nhận
                    </button>
                    <button type="button" 
                        onclick="closeApproveModal()"
                        class="px-4 py-3 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Từ chối --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">Từ chối chi phí</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('giangvien.chiphi.reject', $chiphi->machiphi) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Lý do từ chối <span class="text-red-500">*</span>
                    </label>
                    <textarea name="lydotuchoi" 
                        rows="4"
                        required
                        placeholder="Nhập lý do từ chối..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-red-600 to-red-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-red-700 hover:to-red-600 transition">
                        <i class="fas fa-times-circle mr-2"></i>Từ chối
                    </button>
                    <button type="button" 
                        onclick="closeRejectModal()"
                        class="px-4 py-3 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush

@endsection