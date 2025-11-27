@extends('layouts.client')

@section('title', 'Chi tiết Quyết toán #' . $quyettoan->maquyettoan)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-4xl font-black">Quyết toán #{{ $quyettoan->maquyettoan }}</h1>
                    @if($quyettoan->trangthai == 'Draft')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm border border-white/30">
                            <i class="fas fa-file-alt mr-1"></i>Nháp
                        </span>
                    @elseif($quyettoan->trangthai == 'Pending')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-400/30 backdrop-blur-sm border border-yellow-300/50">
                            <i class="fas fa-clock mr-1"></i>Chờ duyệt
                        </span>
                    @elseif($quyettoan->trangthai == 'Approved')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-400/30 backdrop-blur-sm border border-green-300/50">
                            <i class="fas fa-check-circle mr-1"></i>Đã duyệt
                        </span>
                    @elseif($quyettoan->trangthai == 'Rejected')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-400/30 backdrop-blur-sm border border-red-300/50">
                            <i class="fas fa-times-circle mr-1"></i>Từ chối
                        </span>
                    @endif
                </div>
                <p class="text-blue-100">{{ $quyettoan->tencuocthi }}</p>
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

    {{-- Action Buttons --}}
    <div class="mb-6 flex flex-wrap gap-3">
        @if($quyettoan->trangthai == 'Draft')
            <a href="{{ route('giangvien.quyettoan.edit', $quyettoan->maquyettoan) }}" 
                class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Chỉnh sửa</span>
            </a>
            
            <form action="{{ route('giangvien.quyettoan.submit', $quyettoan->maquyettoan) }}" method="POST" class="inline"
                onsubmit="return confirm('Bạn có chắc muốn nộp quyết toán này để duyệt?')">
                @csrf
                <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Nộp để duyệt</span>
                </button>
            </form>

            <form action="{{ route('giangvien.quyettoan.destroy', $quyettoan->maquyettoan) }}" method="POST" class="inline"
                onsubmit="return confirm('Bạn có chắc muốn xóa quyết toán này?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold shadow-lg hover:bg-red-700 transition flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Xóa</span>
                </button>
            </form>
        @endif

        @if($isTruongBoMon && $quyettoan->trangthai == 'Pending')
            <button onclick="showApproveModal()" 
                class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>Duyệt</span>
            </button>
            
            <button onclick="showRejectModal()" 
                class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold shadow-lg hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-times-circle"></i>
                <span>Từ chối</span>
            </button>
        @endif

        <a href="{{ route('giangvien.quyettoan.export', $quyettoan->maquyettoan) }}" 
            class="px-6 py-3 bg-purple-600 text-white rounded-xl font-bold shadow-lg hover:bg-purple-700 transition flex items-center gap-2">
            <i class="fas fa-file-pdf"></i>
            <span>Export PDF</span>
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Thông tin chính --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Thông tin quyết toán --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Thông tin Quyết toán</span>
                    </h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-500 uppercase">Mã quyết toán</label>
                            <div class="text-lg font-bold text-blue-600 font-mono">{{ $quyettoan->maquyettoan }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-500 uppercase">Ngày quyết toán</label>
                            <div class="text-lg font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                                <label class="text-sm font-semibold text-blue-600 uppercase block mb-1">Tổng dự trù</label>
                                <div class="text-2xl font-bold text-blue-700">
                                    {{ number_format($quyettoan->tongdutru, 0, ',', '.') }} ₫
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                                <label class="text-sm font-semibold text-green-600 uppercase block mb-1">Tổng thực tế</label>
                                <div class="text-2xl font-bold text-green-700">
                                    {{ number_format($quyettoan->tongthucte, 0, ',', '.') }} ₫
                                </div>
                            </div>
                            <div class="bg-{{ $quyettoan->chenhlech >= 0 ? 'emerald' : 'red' }}-50 rounded-xl p-4 border border-{{ $quyettoan->chenhlech >= 0 ? 'emerald' : 'red' }}-200">
                                <label class="text-sm font-semibold text-{{ $quyettoan->chenhlech >= 0 ? 'emerald' : 'red' }}-600 uppercase block mb-1">Chênh lệch</label>
                                <div class="text-2xl font-bold text-{{ $quyettoan->chenhlech >= 0 ? 'emerald' : 'red' }}-700">
                                    {{ number_format($quyettoan->chenhlech, 0, ',', '.') }} ₫
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($quyettoan->ghichu)
                        <div class="pt-4 border-t border-gray-200">
                            <label class="text-sm font-semibold text-gray-500 uppercase block mb-2">Ghi chú</label>
                            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 whitespace-pre-wrap">{{ $quyettoan->ghichu }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Danh sách chi phí --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-list-ul"></i>
                        <span>Chi phí đã duyệt ({{ $chiphis->count() }})</span>
                    </h2>
                </div>
                
                @if($chiphis->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tên chi phí</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase">Dự trù</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase">Thực tế</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase">Chênh lệch</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($chiphis as $cp)
                                    <tr class="hover:bg-blue-50/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-800">{{ $cp->tenkhoanchi }}</div>
                                            @if($cp->ghichu)
                                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($cp->ghichu, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold text-blue-600">
                                            {{ number_format($cp->dutruchiphi, 0, ',', '.') }} ₫
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold text-green-600">
                                            {{ number_format($cp->thuctechi, 0, ',', '.') }} ₫
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold {{ ($cp->dutruchiphi - $cp->thuctechi) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($cp->dutruchiphi - $cp->thuctechi, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-50 to-blue-50 border-t-2 border-blue-200">
                                <tr>
                                    <td class="px-6 py-4 font-bold text-gray-700 uppercase">Tổng cộng</td>
                                    <td class="px-6 py-4 text-right font-bold text-blue-700">
                                        {{ number_format($chiphis->sum('dutruchiphi'), 0, ',', '.') }} ₫
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-green-700">
                                        {{ number_format($chiphis->sum('thuctechi'), 0, ',', '.') }} ₫
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold {{ ($chiphis->sum('dutruchiphi') - $chiphis->sum('thuctechi')) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                        {{ number_format($chiphis->sum('dutruchiphi') - $chiphis->sum('thuctechi'), 0, ',', '.') }} ₫
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-semibold">Chưa có chi phí nào được duyệt</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Thông tin cuộc thi --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-trophy"></i>
                        <span>Thông tin Cuộc thi</span>
                    </h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-500 uppercase block mb-1">Tên cuộc thi</label>
                        <div class="text-lg font-bold text-gray-800">{{ $quyettoan->tencuocthi }}</div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-semibold text-gray-500 uppercase block mb-1">Loại cuộc thi</label>
                        <div class="font-semibold text-gray-700">{{ $quyettoan->loaicuocthi }}</div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-500 uppercase block mb-1">Bộ môn</label>
                        <div class="font-semibold text-gray-700">{{ $quyettoan->tenbomon }}</div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <label class="text-sm font-semibold text-gray-500 uppercase block mb-1">Thời gian</label>
                        <div class="text-sm text-gray-700">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-play text-green-500"></i>
                                <span>Bắt đầu: {{ \Carbon\Carbon::parse($quyettoan->thoigianbatdau)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-stop text-red-500"></i>
                                <span>Kết thúc: {{ \Carbon\Carbon::parse($quyettoan->thoigianketthuc)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- File đính kèm --}}
            @if($quyettoan->filequyettoan)
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-600 to-pink-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-paperclip"></i>
                            <span>File đính kèm</span>
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-start gap-4 p-4 bg-red-50 rounded-xl border border-red-200">
                            <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                            <div class="flex-1">
                                <div class="font-bold text-gray-800 mb-1">{{ basename($quyettoan->filequyettoan) }}</div>
                                <a href="{{ route('giangvien.quyettoan.download-file', $quyettoan->maquyettoan) }}" 
                                    class="inline-flex items-center gap-2 text-red-600 hover:text-red-800 font-semibold">
                                    <i class="fas fa-download"></i>
                                    <span>Tải xuống</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Lịch sử duyệt --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-600 to-cyan-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-history"></i>
                        <span>Lịch sử</span>
                    </h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-500 uppercase block mb-1">Người lập</label>
                        <div class="font-semibold text-gray-800">{{ $quyettoan->tennguoilap ?? 'Không xác định' }}</div>
                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($quyettoan->nguoiduyet)
                        <div class="pt-4 border-t border-gray-200">
                            <label class="text-sm font-semibold text-gray-500 uppercase block mb-1">Người duyệt</label>
                            <div class="font-semibold text-gray-800">{{ $quyettoan->tennguoiduyet ?? 'Không xác định' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modal Duyệt --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 px-6 py-4">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>Xác nhận duyệt</span>
            </h3>
        </div>
        <form action="{{ route('giangvien.quyettoan.approve', $quyettoan->maquyettoan) }}" method="POST" class="p-6">
            @csrf
            <p class="text-gray-700 mb-6">Bạn có chắc chắn muốn <span class="font-bold text-green-600">duyệt</span> quyết toán này?</p>
            <div class="flex gap-3">
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl font-bold hover:shadow-lg transition">
                    <i class="fas fa-check mr-2"></i>Duyệt
                </button>
                <button type="button" onclick="hideApproveModal()" 
                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Từ chối --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-pink-500 px-6 py-4">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-times-circle"></i>
                <span>Từ chối quyết toán</span>
            </h3>
        </div>
        <form action="{{ route('giangvien.quyettoan.reject', $quyettoan->maquyettoan) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Lý do từ chối <span class="text-red-500">*</span>
                </label>
                <textarea name="lydotuchoi" rows="4" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition resize-none"
                    placeholder="Nhập lý do từ chối (tối thiểu 10 ký tự)..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition">
                    <i class="fas fa-times mr-2"></i>Từ chối
                </button>
                <button type="button" onclick="hideRejectModal()" 
                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function hideApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Đóng modal khi click bên ngoài
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) hideApproveModal();
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) hideRejectModal();
});
</script>
@endpush

@endsection