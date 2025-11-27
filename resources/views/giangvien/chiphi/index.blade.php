@extends('layouts.client')

@section('title', 'Quản lý Chi phí')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-emerald-700 via-green-600 to-teal-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black mb-2">
                    Quản lý Chi phí
                    @if($isTruongBoMon)
                        <span class="text-sm font-normal bg-white/20 px-3 py-1 rounded-full ml-2">Trưởng bộ môn</span>
                    @endif
                </h1>
                <p class="text-green-100">Theo dõi và quản lý chi phí cuộc thi</p>
            </div>
            <div class="hidden md:flex items-center gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1" id="stat-total">-</div>
                        <div class="text-sm text-green-100">Chi phí</div>
                    </div>
                </div>
                <a href="{{ route('giangvien.chiphi.create') }}" 
                    class="px-6 py-3 bg-white text-green-600 rounded-xl font-bold hover:bg-green-50 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Tạo chi phí mới</span>
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

    {{-- Mobile: Nút tạo chi phí --}}
    <div class="md:hidden mb-6">
        <a href="{{ route('giangvien.chiphi.create') }}" 
            class="block w-full px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl font-bold shadow-lg text-center">
            <i class="fas fa-plus mr-2"></i>Tạo chi phí mới
        </a>
    </div>

    {{-- Thống kê nhanh --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
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

        <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-2xl p-6 border border-amber-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-amber-600" id="stat-tongthucte">-</div>
                    <div class="text-sm text-amber-700 font-medium mt-1">Tổng chi</div>
                </div>
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-6 mb-6">
        <form method="GET" class="grid md:grid-cols-5 gap-4">
            
            {{-- Ô tìm kiếm --}}
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                    name="search" 
                    placeholder="Tìm kiếm..." 
                    value="{{ request('search') }}"
                    class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            </div>

            {{-- Lọc trạng thái --}}
            <div>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>

            {{-- Lọc cuộc thi --}}
            <div>
                <select name="macuocthi" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <option value="">Tất cả cuộc thi</option>
                    @foreach($cuocthis as $ct)
                        <option value="{{ $ct->macuocthi }}" {{ request('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                            {{ $ct->tencuocthi }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter người yêu cầu (chỉ trưởng bộ môn) --}}
            @if($isTruongBoMon)
            <div>
                <select name="nguoiyeucau" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <option value="">Tất cả người yêu cầu</option>
                    @foreach($giangviens as $gv)
                        <option value="{{ $gv->magiangvien }}" {{ request('nguoiyeucau') == $gv->magiangvien ? 'selected' : '' }}>
                            {{ $gv->hoten }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Nút tìm kiếm --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-teal-500 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>
                @if(request()->hasAny(['search', 'status', 'macuocthi', 'nguoiyeucau']))
                <a href="{{ route('giangvien.chiphi.index') }}" 
                    class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Danh sách chi phí --}}
    @if($chiphis->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-green-50 to-teal-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mã CP</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Khoản chi</th>
                            @if($isTruongBoMon)
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Người yêu cầu</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Cuộc thi</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Dự trù</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Thực tế</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($chiphis as $cp)
                            <tr class="hover:bg-green-50/50 transition duration-200">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-green-600">{{ $cp->machiphi }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $cp->tenkhoanchi }}</div>
                                    @if($cp->ngayyeucau)
                                        <div class="text-sm text-gray-500">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($cp->ngayyeucau)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                @if($isTruongBoMon)
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($cp->tennguoiyeucau ?? 'N', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-800">{{ $cp->tennguoiyeucau ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $cp->tencuocthi }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-blue-600">
                                    {{ number_format($cp->dutruchiphi, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    {{ $cp->thuctechi ? number_format($cp->thuctechi, 0, ',', '.') . ' ₫' : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($cp->trangthai == 'Pending')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-clock mr-1"></i>Chờ duyệt
                                        </span>
                                    @elseif($cp->trangthai == 'Approved')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>Đã duyệt
                                        </span>
                                    @elseif($cp->trangthai == 'Rejected')
                                        <span class="px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                            <i class="fas fa-times-circle mr-1"></i>Từ chối
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('giangvien.chiphi.show', $cp->machiphi) }}" 
                                            class="text-blue-600 hover:text-blue-800 hover:scale-110 transition transform" 
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye text-lg"></i>
                                        </a>
                                        
                                        {{-- Chỉ người tạo mới được sửa/xóa --}}
                                        @if(in_array($cp->trangthai, ['Pending', 'Rejected']) && (!$isTruongBoMon || $cp->nguoiyeucau == $giangvien->magiangvien))
                                            <a href="{{ route('giangvien.chiphi.edit', $cp->machiphi) }}" 
                                                class="text-green-600 hover:text-green-800 hover:scale-110 transition transform" 
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit text-lg"></i>
                                            </a>
                                            <form action="{{ route('giangvien.chiphi.destroy', $cp->machiphi) }}" 
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa chi phí này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 hover:scale-110 transition transform" 
                                                    title="Xóa">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $chiphis->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-money-bill-wave text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có chi phí nào</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'status', 'macuocthi', 'nguoiyeucau']))
                        Không tìm thấy chi phí nào phù hợp với bộ lọc của bạn.
                    @else
                        Hãy tạo chi phí đầu tiên để bắt đầu.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'status', 'macuocthi', 'nguoiyeucau']))
                    <a href="{{ route('giangvien.chiphi.index') }}" 
                        class="bg-gradient-to-r from-green-600 to-teal-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-rotate-right mr-2"></i>Xóa bộ lọc
                    </a>
                    @endif
                    <a href="{{ route('giangvien.chiphi.create') }}" 
                        class="bg-white text-green-600 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border-2 border-green-600">
                        <i class="fas fa-plus mr-2"></i>Tạo chi phí mới
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
        const response = await fetch('{{ route("giangvien.chiphi.api.statistics") }}');
        const data = await response.json();
        
        document.getElementById('stat-pending').textContent = data.pending;
        document.getElementById('stat-approved').textContent = data.approved;
        document.getElementById('stat-rejected').textContent = data.rejected;
        document.getElementById('stat-total').textContent = data.total;
        
        // Format số tiền
        const tongThucTe = new Intl.NumberFormat('vi-VN').format(data.tongthucte) + ' ₫';
        document.getElementById('stat-tongthucte').textContent = tongThucTe;
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Load khi trang load xong
document.addEventListener('DOMContentLoaded', loadStatistics);
</script>
@endpush

@endsection