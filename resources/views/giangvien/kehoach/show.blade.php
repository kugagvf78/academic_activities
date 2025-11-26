@extends('layouts.client')

@section('title', 'Chi tiết Kế hoạch Cuộc thi')

@section('content')
<div class="container mx-auto px-6 py-8">
    {{-- Back button --}}
    <div class="mb-6">
        <a href="{{ route('giangvien.kehoach.index') }}" 
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 font-semibold transition">
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

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        {{-- Thông tin kế hoạch --}}
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Mã kế hoạch</div>
                        <h2 class="text-3xl font-black text-gray-800 mb-3">{{ $kehoach->makehoach }}</h2>
                        <h3 class="text-xl font-bold text-purple-600 mb-2">{{ $kehoach->tencuocthi }}</h3>
                        <div class="flex items-center gap-4 text-sm flex-wrap">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">
                                {{ $kehoach->loaicuocthi }}
                            </span>
                            
                            @if($kehoach->trangthaiduyet == 'Pending')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i>Chờ duyệt
                                </span>
                            @elseif($kehoach->trangthaiduyet == 'Approved')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Đã duyệt
                                </span>
                            @elseif($kehoach->trangthaiduyet == 'Rejected')
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle mr-1"></i>Từ chối
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex gap-2 flex-wrap">
                        {{-- ✨ Nút duyệt/từ chối cho trưởng bộ môn --}}
                        @if($isTruongBoMon && $kehoach->trangthaiduyet == 'Pending')
                            <button onclick="approveModal()" 
                                class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition shadow-lg">
                                <i class="fas fa-check mr-2"></i>Duyệt
                            </button>
                            
                            <button onclick="rejectModal()" 
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition shadow-lg">
                                <i class="fas fa-times mr-2"></i>Từ chối
                            </button>
                        @endif

                        {{-- Các nút khác --}}
                        @if(in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected']))
                            <a href="{{ route('giangvien.kehoach.edit', $kehoach->makehoach) }}" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                                <i class="fas fa-edit mr-2"></i>Sửa
                            </a>
                        @endif
                        
                        <a href="{{ route('giangvien.kehoach.export', $kehoach->makehoach) }}" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition">
                            <i class="fas fa-file-pdf mr-2"></i>Export PDF
                        </a>
                    </div>
                </div>

                <div class="space-y-4 border-t pt-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Bộ môn</div>
                            <div class="font-bold text-gray-800">{{ $kehoach->tenbomon }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Năm học / Học kỳ</div>
                            <div class="font-bold text-gray-800">{{ $kehoach->namhoc }} - Học kỳ {{ $kehoach->hocky }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Ngày nộp kế hoạch</div>
                            <div class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($kehoach->ngaynopkehoach)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    @if($kehoach->ngayduyet)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-check text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Người duyệt / Ngày duyệt</div>
                            <div class="font-bold text-gray-800">{{ $kehoach->tennguoiduyet ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($kehoach->ngayduyet)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($kehoach->ghichu)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-sticky-note text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Ghi chú</div>
                            <div class="text-gray-700 whitespace-pre-line">{{ $kehoach->ghichu }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Thống kê --}}
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="text-sm font-semibold mb-2 opacity-90">Tổng số ban</div>
                <div class="text-5xl font-black mb-2">{{ $bans->count() }}</div>
                <div class="text-sm opacity-90">Ban tổ chức</div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="text-sm font-semibold text-gray-600 mb-4">Công việc</div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Tổng công việc</span>
                        <span class="text-2xl font-bold text-purple-600">{{ $congviecs->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách ban --}}
    @if($bans->count() > 0)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
            <h3 class="text-xl font-bold text-gray-800">Danh sách Ban ({{ $bans->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tên ban</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Mô tả</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bans as $index => $ban)
                        <tr class="hover:bg-purple-50/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $ban->tenban }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $ban->mota ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Danh sách công việc --}}
    @if($congviecs->count() > 0)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
            <h3 class="text-xl font-bold text-gray-800">Danh sách Công việc ({{ $congviecs->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tên công việc</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Mô tả</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($congviecs as $index => $cv)
                        <tr class="hover:bg-purple-50/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $cv->tencongviec }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($cv->thoigianbatdau)->format('d/m/Y H:i') }}
                                @if($cv->thoigianketthuc)
                                    - {{ \Carbon\Carbon::parse($cv->thoigianketthuc)->format('d/m/Y H:i') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $cv->mota ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- ✨ Modal Duyệt kế hoạch --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-3xl text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Xác nhận duyệt kế hoạch</h3>
            <p class="text-gray-600">Bạn có chắc chắn muốn duyệt kế hoạch này không?</p>
        </div>
        
        <form action="{{ route('giangvien.kehoach.approve', $kehoach->makehoach) }}" method="POST">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="closeApproveModal()" 
                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Hủy
                </button>
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition shadow-lg">
                    Xác nhận duyệt
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ✨ Modal Từ chối kế hoạch --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-3xl text-red-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Từ chối kế hoạch</h3>
            <p class="text-gray-600">Vui lòng nhập lý do từ chối kế hoạch này</p>
        </div>
        
        <form action="{{ route('giangvien.kehoach.reject', $kehoach->makehoach) }}" method="POST">
            @csrf
            <div class="mb-6">
                <textarea name="lydotuchoi" 
                    rows="4" 
                    required
                    placeholder="Nhập lý do từ chối (tối thiểu 10 ký tự)..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 transition"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" 
                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Hủy
                </button>
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg">
                    Xác nhận từ chối
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function approveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function rejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Đóng modal khi click outside
document.getElementById('approveModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endpush

@endsection