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

    @if(session('info'))
        <div class="mb-6 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 text-blue-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-info-circle text-2xl"></i>
                <span class="font-semibold">{{ session('info') }}</span>
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

                            {{-- Hiển thị trạng thái cuộc thi --}}
                            @if($kehoach->macuocthi)
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-semibold">
                                    <i class="fas fa-trophy mr-1"></i>Đã tạo cuộc thi
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex gap-2 flex-wrap justify-end">
                        {{-- ✨ Nút duyệt/từ chối cho trưởng bộ môn --}}
                        @if(($isTruongBoMon ?? false) && $kehoach->trangthaiduyet == 'Pending')
                            <button onclick="approveModal()" 
                                class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition shadow-lg">
                                <i class="fas fa-check mr-2"></i>Duyệt
                            </button>
                            
                            <button onclick="rejectModal()" 
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition shadow-lg">
                                <i class="fas fa-times mr-2"></i>Từ chối
                            </button>
                        @endif

                        {{-- Nút tạo cuộc thi (nếu đã duyệt và chưa có cuộc thi) --}}
                        @if($kehoach->trangthaiduyet == 'Approved' && !$kehoach->macuocthi)
                            <form action="{{ route('giangvien.kehoach.create-cuocthi', $kehoach->makehoach) }}" 
                                method="POST" 
                                onsubmit="return confirm('Bạn có chắc muốn tạo cuộc thi từ kế hoạch này? Thao tác này không thể hoàn tác!')">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-xl hover:from-purple-700 hover:to-pink-600 transition shadow-lg">
                                    <i class="fas fa-plus mr-2"></i>Tạo cuộc thi
                                </button>
                            </form>
                        @endif

                        {{-- Nút xem cuộc thi (nếu đã tạo) --}}
                        @if($kehoach->macuocthi)
                            <a href="{{ route('giangvien.cuocthi.show', $kehoach->macuocthi) }}" 
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl hover:from-blue-700 hover:to-cyan-600 transition shadow-lg">
                                <i class="fas fa-trophy mr-2"></i>Xem cuộc thi
                            </a>
                        @endif

                        {{-- Các nút khác --}}
                        @if(in_array($kehoach->trangthaiduyet, ['Pending', 'Rejected']))
                            <a href="{{ route('giangvien.kehoach.edit', $kehoach->makehoach) }}" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                                <i class="fas fa-edit mr-2"></i>Sửa
                            </a>
                        @endif

                        @if($kehoach->trangthaiduyet == 'Rejected')
                            <form action="{{ route('giangvien.kehoach.resubmit', $kehoach->makehoach) }}" 
                                method="POST" 
                                class="inline"
                                onsubmit="return confirm('Gửi lại kế hoạch để duyệt?')">
                                @csrf
                                <button type="submit" 
                                    class="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition">
                                    <i class="fas fa-paper-plane mr-2"></i>Gửi lại
                                </button>
                            </form>
                        @endif
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
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-indigo-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Thời gian cuộc thi</div>
                            <div class="font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($kehoach->thoigianbatdau)->format('d/m/Y H:i') }}
                                <span class="text-gray-500 mx-2">→</span>
                                {{ \Carbon\Carbon::parse($kehoach->thoigianketthuc)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    @if($kehoach->diadiem)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-pink-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Địa điểm</div>
                            <div class="font-bold text-gray-800">{{ $kehoach->diadiem }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Người nộp / Ngày nộp</div>
                            <div class="font-bold text-gray-800">{{ $kehoach->tennguoinop ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($kehoach->ngaynopkehoach)->format('d/m/Y H:i') }}</div>
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

                    @if($kehoach->mota)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-align-left text-cyan-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Mô tả</div>
                            <div class="text-gray-700 whitespace-pre-line">{{ $kehoach->mota }}</div>
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
                <div class="text-sm font-semibold mb-2 opacity-90">Dự trù kinh phí</div>
                <div class="text-4xl font-black mb-2">
                    {{ number_format($kehoach->dutrukinhphi ?? 0, 0, ',', '.') }} đ
                </div>
                <div class="text-sm opacity-90">Tổng chi phí dự kiến</div>
            </div>

            @if($kehoach->soluongthanhvien)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Số lượng thành viên</div>
                        <div class="text-3xl font-bold text-purple-600">{{ $kehoach->soluongthanhvien }}</div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
            @endif

            @if($kehoach->hinhthucthamgia)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="text-sm font-semibold text-gray-600 mb-2">Hình thức tham gia</div>
                <div class="text-gray-800 font-medium">{{ $kehoach->hinhthucthamgia }}</div>
            </div>
            @endif

            @if($kehoach->doituongthamgia)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="text-sm font-semibold text-gray-600 mb-2">Đối tượng tham gia</div>
                <div class="text-gray-800">{{ $kehoach->doituongthamgia }}</div>
            </div>
            @endif
        </div>
    </div>
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
                <textarea name="ghichu" 
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