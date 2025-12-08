@extends('layouts.client')
@section('title', 'Danh sách Phân công')

@section('content')

{{-- 🏛️ HEADER SECTION: Phong cách Academic --}}
<div class="relative bg-slate-900 overflow-hidden pb-12">
    {{-- Background Effect vẫn giữ lại nhưng tinh chỉnh tối hơn để nổi bật chữ --}}
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-blue-900 to-slate-900 opacity-90"></div>
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <div class="container mx-auto px-4 py-8 relative z-10">
        {{-- Breadcrumb gọn --}}
        <div class="flex items-center gap-2 text-blue-200 text-sm mb-6">
            <a href="{{ route('giangvien.profile.index') }}" class="hover:text-white transition flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="opacity-50">/</span>
            <span class="text-white font-medium">Quản lý phân công</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-2">
                    @if($isTruongBoMon)
                        Quản lý Phân công Bộ môn
                    @else
                        Công việc của tôi
                    @endif
                </h1>
                <p class="text-blue-200 text-lg font-light">
                    @if($isTruongBoMon)
                        Điều phối và theo dõi tiến độ công việc giảng viên.
                    @else
                        Danh sách các nhiệm vụ và trách nhiệm được giao.
                    @endif
                </p>
            </div>
            
            {{-- Hành động chính (Primary Actions) --}}
            @if($isTruongBoMon)
            <div>
                <a href="{{ route('giangvien.phancong.create') }}" 
                    class="inline-flex items-center gap-2 bg-white text-indigo-900 px-5 py-2.5 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg shadow-indigo-900/20">
                    <i class="fas fa-plus"></i>
                    <span>Tạo phân công mới</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- 📊 STATS BAR: Gọn gàng, nằm đè lên header --}}
<div class="container mx-auto px-4 -mt-8 relative z-20">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 grid grid-cols-2 md:grid-cols-4 gap-4 divide-x divide-gray-100">
        <div class="flex items-center gap-4 px-2 md:px-4">
            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fas fa-briefcase"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $phanCongList->total() }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Tổng phân công</div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-2 md:px-4">
            <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                <i class="fas fa-trophy"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $cuocThiList->count() }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Cuộc thi</div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-2 md:px-4">
            <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $banList->count() }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Ban chuyên môn</div>
            </div>
        </div>
        <div class="flex items-center gap-4 px-2 md:px-4 border-l-0 md:border-l"> <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                <i class="fas fa-tasks"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $congViecList->count() }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Đầu việc</div>
            </div>
        </div>
    </div>
</div>

<section class="container mx-auto px-4 py-8 flex flex-col lg:flex-row gap-8">
    
    {{-- 🔍 SIDEBAR / FILTERS: Đặt bên trái hoặc trên cùng tùy màn hình --}}
    <aside class="w-full lg:w-1/4 flex-shrink-0">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-4">
            <div class="p-5 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-filter text-indigo-600"></i> Bộ lọc tìm kiếm
                </h3>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('giangvien.phancong.index') }}" class="space-y-4">
                    {{-- Search --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Từ khóa</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Tìm tên, vai trò..." 
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        </div>
                    </div>

                    {{-- Selects --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Cuộc thi</label>
                        <select name="cuocthi" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tất cả</option>
                            @foreach($cuocThiList as $ct)
                                <option value="{{ $ct->macuocthi }}" {{ request('cuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                    {{ Str::limit($ct->tencuocthi, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Công việc</label>
                        <select name="congviec" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tất cả</option>
                            @foreach($congViecList as $cv)
                                <option value="{{ $cv->macongviec }}" {{ request('congviec') == $cv->macongviec ? 'selected' : '' }}>
                                    {{ Str::limit($cv->tencongviec, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Ban</label>
                        <select name="ban" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tất cả</option>
                            @foreach($banList as $ban)
                                <option value="{{ $ban->maban }}" {{ request('ban') == $ban->maban ? 'selected' : '' }}>
                                    {{ $ban->tenban }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($isTruongBoMon && $giangVienList->count() > 0)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Giảng viên</label>
                        <select name="giangvien_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Tất cả</option>
                            @foreach($giangVienList as $gv)
                                <option value="{{ $gv->magiangvien }}" {{ request('giangvien_filter') == $gv->magiangvien ? 'selected' : '' }}>
                                    {{ $gv->nguoiDung->hoten ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="pt-2 flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-medium transition">
                            Áp dụng
                        </button>
                        @if(request()->hasAny(['search', 'congviec', 'ban', 'cuocthi', 'giangvien_filter']))
                        <a href="{{ route('giangvien.phancong.index') }}" 
                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition" title="Reset bộ lọc">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
            
            {{-- Extra Actions Sidebar --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl flex flex-col gap-2">
                <a href="{{ route('giangvien.phancong.export') }}" 
                    class="flex items-center justify-center gap-2 text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 px-3 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
                @if($isTruongBoMon)
                <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" 
                    class="flex items-center justify-center gap-2 text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 px-3 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-users-cog"></i> Quản lý Ban
                </a>
                @endif
            </div>
        </div>
    </aside>

    {{-- 📋 MAIN CONTENT --}}
    <div class="flex-1">
        @if($phanCongList->count() > 0)
            <div class="flex flex-col gap-4">
                @foreach ($phanCongList as $phanCong)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-indigo-300 transition-all duration-200 group">
                    <div class="p-5">
                        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start">
                            
                            {{-- Nội dung chính --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    {{-- Tag Cuộc thi nhỏ gọn --}}
                                    @if($phanCong->ban && $phanCong->ban->cuocthi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ $phanCong->ban->cuocthi->tencuocthi }}
                                        </span>
                                    @endif
                                    {{-- Tag Vai trò --}}
                                    @if($phanCong->vaitro)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $phanCong->vaitro }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-indigo-700 transition">
                                    {{ $phanCong->congviec->tencongviec ?? 'N/A' }}
                                </h3>

                                <div class="grid sm:grid-cols-2 gap-y-1 gap-x-4 text-sm text-gray-500 mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-layer-group text-gray-400 w-4"></i>
                                        <span>Ban: <span class="text-gray-700 font-medium">{{ $phanCong->ban->tenban ?? 'Chưa phân ban' }}</span></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-calendar-alt text-gray-400 w-4"></i>
                                        <span>Ngày: <span class="text-gray-700 font-medium">{{ $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('d/m/Y') : 'N/A' }}</span></span>
                                    </div>
                                    @if($isTruongBoMon)
                                    <div class="flex items-center gap-2 col-span-2 mt-1 pt-2 border-t border-gray-100">
                                        <i class="fas fa-user-circle text-indigo-500 w-4"></i>
                                        <span>Giảng viên: <span class="text-indigo-900 font-semibold">{{ $phanCong->giangvien->nguoiDung->hoten ?? 'N/A' }}</span></span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Nút thao tác --}}
                            <div class="flex sm:flex-col gap-2 w-full sm:w-auto mt-4 sm:mt-0 pt-4 sm:pt-0 border-t sm:border-0 border-gray-100">
                                <a href="{{ route('giangvien.phancong.show', $phanCong->maphancong) }}" 
                                    class="flex-1 sm:flex-none text-center px-4 py-2 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white text-sm font-medium transition whitespace-nowrap">
                                    Chi tiết
                                </a>
                                
                                @if($isTruongBoMon)
                                <div class="flex gap-2">
                                    <a href="{{ route('giangvien.phancong.edit', $phanCong->maphancong) }}" 
                                        class="flex-1 sm:flex-none text-center px-3 py-2 rounded border border-gray-200 text-gray-600 hover:border-amber-500 hover:text-amber-600 text-sm transition" title="Sửa">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button onclick="confirmDelete('{{ $phanCong->maphancong }}')" 
                                        class="flex-1 sm:flex-none text-center px-3 py-2 rounded border border-gray-200 text-gray-600 hover:border-red-500 hover:text-red-600 text-sm transition" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination Modern --}}
            <div class="mt-6">
                {{ $phanCongList->appends(request()->query())->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4">
                    <i class="fas fa-clipboard-list text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Không tìm thấy dữ liệu</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">
                    Không có phân công nào phù hợp với bộ lọc hiện tại hoặc danh sách đang trống.
                </p>
                @if(request()->hasAny(['search', 'congviec', 'ban', 'cuocthi', 'giangvien_filter']))
                    <a href="{{ route('giangvien.phancong.index') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-arrow-left"></i> Xóa bộ lọc và quay lại
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>

{{-- Form xóa ẩn --}}
@if($isTruongBoMon)
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Xác nhận xóa phân công này? Hành động này không thể hoàn tác.')) {
        const form = document.getElementById('delete-form');
        form.action = `/giang-vien/phan-cong/${id}`;
        form.submit();
    }
}
</script>
@endpush

@endsection