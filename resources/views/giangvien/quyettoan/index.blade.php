@extends('layouts.client')

@section('title', 'Quản lý Quyết toán')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black mb-2">
                    Hồ sơ Quyết toán
                    @if($isTruongBoMon)
                        <span class="text-sm font-normal bg-white/20 px-3 py-1 rounded-full ml-2">
                            <i class="fas fa-crown mr-1"></i>Trưởng bộ môn
                        </span>
                    @endif
                </h1>
                <p class="text-blue-100">
                    @if($isTruongBoMon)
                        Quản lý tất cả quyết toán trong bộ môn
                    @else
                        Lập và quản lý hồ sơ quyết toán cuộc thi
                    @endif
                </p>
            </div>
            <div class="hidden md:flex items-center gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1" id="stat-total">-</div>
                        <div class="text-sm text-blue-100">Quyết toán</div>
                    </div>
                </div>
                <a href="{{ route('giangvien.quyettoan.create') }}" 
                    class="px-6 py-3 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Lập quyết toán mới</span>
                </a>
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

    {{-- Mobile: Nút lập quyết toán --}}
    <div class="md:hidden mb-6">
        <a href="{{ route('giangvien.quyettoan.create') }}" 
            class="block w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl font-bold shadow-lg text-center">
            <i class="fas fa-plus mr-2"></i>Lập quyết toán mới
        </a>
    </div>

    {{-- Thống kê nhanh --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-gray-600" id="stat-draft">-</div>
                    <div class="text-sm text-gray-700 font-medium mt-1">Nháp</div>
                </div>
                <div class="w-12 h-12 bg-gray-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-blue-600" id="stat-pending">-</div>
                    <div class="text-sm text-blue-700 font-medium mt-1">Chờ duyệt</div>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-green-600" id="stat-approved">-</div>
                    <div class="text-sm text-green-700 font-medium mt-1">Đã duyệt</div>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 border border-red-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-red-600" id="stat-rejected">-</div>
                    <div class="text-sm text-red-700 font-medium mt-1">Từ chối</div>
                </div>
                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-purple-600" id="stat-total">-</div>
                    <div class="text-sm text-purple-700 font-medium mt-1">Tổng số</div>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-list text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6 mb-6">
        <form method="GET" class="grid md:grid-cols-4 gap-4">
            
            {{-- Ô tìm kiếm --}}
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                    name="search" 
                    placeholder="Tìm kiếm cuộc thi..." 
                    value="{{ request('search') }}"
                    class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            {{-- Lọc trạng thái --}}
            <div>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Trạng thái --</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>

            {{-- Lọc cuộc thi --}}
            <div>
                <select name="macuocthi" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Cuộc thi --</option>
                    @foreach($cuocthis as $ct)
                        <option value="{{ $ct->macuocthi }}" {{ request('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                            {{ $ct->tencuocthi }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nút tìm kiếm + nút xóa lọc --}}
            <div class="flex gap-2">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-blue-700 hover:to-cyan-600 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>

                @if(request()->hasAny(['search', 'status', 'macuocthi']))
                    <a href="{{ route('giangvien.quyettoan.index') }}"
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-rotate-right"></i>
                    </a>
                @endif
            </div>
            
        </form>
    </div>

    {{-- Table --}}
    @if($quyettoans->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mã QT</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Cuộc thi</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Dự trù</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Thực tế</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Chênh lệch</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ngày QT</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quyettoans as $qt)
                            <tr class="hover:bg-blue-50/50 transition duration-200">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-blue-600">{{ $qt->maquyettoan }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $qt->tencuocthi }}</div>
                                    @if($qt->tennguoilap)
                                        <div class="text-sm text-gray-500">Người lập: {{ $qt->tennguoilap }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-blue-600">
                                    {{ number_format($qt->tongdutru, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    {{ number_format($qt->tongthucte, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 text-right font-semibold {{ $qt->chenhlech >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($qt->chenhlech, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($qt->ngayquyettoan)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($qt->trangthai == 'Draft')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">
                                            <i class="fas fa-file-alt mr-1"></i>Nháp
                                        </span>
                                    @elseif($qt->trangthai == 'Pending')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-clock mr-1"></i>Chờ duyệt
                                        </span>
                                    @elseif($qt->trangthai == 'Approved')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Đã duyệt
                                        </span>
                                    @elseif($qt->trangthai == 'Rejected')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-times-circle mr-1"></i>Từ chối
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('giangvien.quyettoan.show', $qt->maquyettoan) }}" 
                                            class="text-blue-600 hover:text-blue-800 hover:scale-110 transition transform" 
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye text-lg"></i>
                                        </a>
                                        
                                        @if($qt->trangthai == 'Draft')
                                            <a href="{{ route('giangvien.quyettoan.edit', $qt->maquyettoan) }}" 
                                                class="text-green-600 hover:text-green-800 hover:scale-110 transition transform" 
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit text-lg"></i>
                                            </a>
                                            <form action="{{ route('giangvien.quyettoan.destroy', $qt->maquyettoan) }}" 
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa quyết toán này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 hover:scale-110 transition transform" 
                                                    title="Xóa">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('giangvien.quyettoan.export', $qt->maquyettoan) }}" 
                                            class="text-purple-600 hover:text-purple-800 hover:scale-110 transition transform" 
                                            title="Export PDF">
                                            <i class="fas fa-file-pdf text-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $quyettoans->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-file-invoice-dollar text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có quyết toán nào</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'status', 'macuocthi']))
                        Không tìm thấy quyết toán nào phù hợp với bộ lọc của bạn.
                    @else
                        Hãy lập quyết toán đầu tiên để bắt đầu.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'status', 'macuocthi']))
                    <a href="{{ route('giangvien.quyettoan.index') }}" 
                        class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-rotate-right mr-2"></i>Xóa bộ lọc
                    </a>
                    @endif
                    <a href="{{ route('giangvien.quyettoan.create') }}" 
                        class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border-2 border-blue-600">
                        <i class="fas fa-plus mr-2"></i>Lập quyết toán mới
                    </a>
                </div>
            </div>
        </div>
    @endif
</section>

@push('scripts')
<script>
// Load thống kê
async function loadStatistics() {
    try {
        const response = await fetch('{{ route("giangvien.quyettoan.api.statistics") }}');
        const data = await response.json();
        
        document.getElementById('stat-draft').textContent = data.draft;
        document.getElementById('stat-pending').textContent = data.pending;
        document.getElementById('stat-approved').textContent = data.approved;
        document.getElementById('stat-rejected').textContent = data.rejected;
        document.getElementById('stat-total').textContent = data.total;
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Load khi trang load xong
document.addEventListener('DOMContentLoaded', loadStatistics);
</script>
@endpush

@endsection