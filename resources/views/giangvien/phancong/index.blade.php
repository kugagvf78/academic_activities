@extends('layouts.client')
@section('title', 'Danh sách Phân công')

@section('content')
{{-- 📋 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.profile.index') }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Quản lý công việc</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    <i class="fas fa-tasks mr-3"></i>
                    @if($isTruongBoMon)
                        Quản lý Phân công Bộ môn
                    @else
                        Danh sách Phân công
                    @endif
                </h1>
                <p class="text-blue-100">
                    @if($isTruongBoMon)
                        Phân công và theo dõi công việc của giảng viên trong bộ môn
                    @else
                        Theo dõi và cập nhật công việc được giao
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $phanCongList->total() }}</div>
                        <div class="text-sm text-blue-100">
                            @if($isTruongBoMon)
                                Tổng phân công
                            @else
                                Công việc của tôi
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- 📊 THỐNG KÊ NHANH --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-indigo-500">
            <div class="text-2xl font-bold text-indigo-700">{{ $phanCongList->total() }}</div>
            <div class="text-sm text-gray-500">
                @if($isTruongBoMon) Tổng phân công @else Công việc của tôi @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-purple-700">{{ $cuocThiList->count() }}</div>
            <div class="text-sm text-gray-500">Cuộc thi</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-700">{{ $banList->count() }}</div>
            <div class="text-sm text-gray-500">Số ban</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-700">{{ $congViecList->count() }}</div>
            <div class="text-sm text-gray-500">Loại công việc</div>
        </div>
    </div>

    {{-- Bộ Lọc --}}
    <div class="bg-white rounded-2xl shadow-xl border border-indigo-100 p-6">
        <form method="GET" action="{{ route('giangvien.phancong.index') }}" class="space-y-4">
            <div class="grid lg:grid-cols-6 md:grid-cols-3 gap-4">
                {{-- Tìm kiếm --}}
                <div class="lg:col-span-2 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Tìm theo vai trò, giảng viên, cuộc thi..." 
                        class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                {{-- Cuộc thi - THÊM MỚI --}}
                <div>
                    <select name="cuocthi" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Tất cả cuộc thi</option>
                        @foreach($cuocThiList as $ct)
                            <option value="{{ $ct->macuocthi }}" {{ request('cuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                {{ $ct->tencuocthi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Công việc --}}
                <div>
                    <select name="congviec" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Tất cả công việc</option>
                        @foreach($congViecList as $cv)
                            <option value="{{ $cv->macongviec }}" {{ request('congviec') == $cv->macongviec ? 'selected' : '' }}>
                                {{ $cv->tencongviec }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ban --}}
                <div>
                    <select name="ban" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Tất cả ban</option>
                        @foreach($banList as $ban)
                            <option value="{{ $ban->maban }}" {{ request('ban') == $ban->maban ? 'selected' : '' }}>
                                {{ $ban->tenban }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nút --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-blue-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-indigo-700 hover:to-blue-600 transition">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                    @if(request()->hasAny(['search', 'congviec', 'ban', 'cuocthi', 'giangvien_filter']))
                    <a href="{{ route('giangvien.phancong.index') }}" 
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-rotate-right"></i>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Lọc theo giảng viên (chỉ trưởng bộ môn) --}}
            @if($isTruongBoMon && $giangVienList->count() > 0)
            <div class="pt-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lọc theo giảng viên</label>
                <select name="giangvien_filter" class="w-full md:w-1/3 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="">Tất cả giảng viên</option>
                    @foreach($giangVienList as $gv)
                        <option value="{{ $gv->magiangvien }}" {{ request('giangvien_filter') == $gv->magiangvien ? 'selected' : '' }}>
                            {{ $gv->nguoiDung->hoten ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>

        {{-- Hành động --}}
        <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-3 justify-between items-center">
            <div class="flex gap-3">
                <a href="{{ route('giangvien.phancong.export') }}" 
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-file-excel"></i>
                    <span>Xuất Excel</span>
                </a>
                
                @if($isTruongBoMon)
                <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" 
                    class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-users-cog"></i>
                    <span>Quản lý Ban</span>
                </a>
                @endif
            </div>
            
            @if($isTruongBoMon)
            <a href="{{ route('giangvien.phancong.create') }}" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                <i class="fas fa-plus"></i>
                <span>Phân công mới</span>
            </a>
            @endif
        </div>
    </div>
</section>

{{-- 📋 DANH SÁCH PHÂN CÔNG --}}
<section class="container mx-auto px-6 py-12">
    @if($phanCongList->count() > 0)
        <div class="grid gap-6">
            @foreach ($phanCongList as $phanCong)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition group">
                <div class="p-6">
                    <div class="grid lg:grid-cols-12 gap-6 items-start">
                        {{-- LEFT: Thông tin công việc --}}
                        <div class="lg:col-span-8">
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center text-white flex-shrink-0">
                                    <i class="fas fa-briefcase text-xl"></i>
                                </div>

                                <div class="flex-1">
                                    {{-- CUỘC THI - THÊM MỚI --}}
                                    @if($phanCong->ban && $phanCong->ban->cuocthi)
                                    <div class="mb-2">
                                        <span class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-medium">
                                            <i class="fas fa-trophy"></i>
                                            {{ $phanCong->ban->cuocthi->tencuocthi }}
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Tiêu đề --}}
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition">
                                        {{ $phanCong->congviec->tencongviec ?? 'N/A' }}
                                    </h3>

                                    {{-- Giảng viên (chỉ hiện cho trưởng bộ môn) --}}
                                    @if($isTruongBoMon)
                                    <div class="mb-2 flex items-center gap-2">
                                        <i class="fas fa-user text-gray-400 text-sm"></i>
                                        <span class="font-medium text-gray-700">
                                            {{ $phanCong->giangvien->nguoiDung->hoten ?? 'N/A' }}
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Vai trò --}}
                                    @if($phanCong->vaitro)
                                    <div class="mb-3">
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium">
                                            {{ $phanCong->vaitro }}
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Meta info --}}
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-users text-blue-500"></i>
                                            <span>{{ $phanCong->ban->tenban ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i class="far fa-calendar text-cyan-500"></i>
                                            <span>{{ $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Actions --}}
                        <div class="lg:col-span-4">
                            <div class="flex flex-col gap-3">
                                <a href="{{ route('giangvien.phancong.show', $phanCong->maphancong) }}" 
                                    class="w-full bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="fas fa-eye"></i>
                                    <span>Chi tiết</span>
                                </a>
                                
                                @if($isTruongBoMon)
                                <div class="flex gap-2">
                                    <a href="{{ route('giangvien.phancong.edit', $phanCong->maphancong) }}" 
                                        class="flex-1 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center gap-2">
                                        <i class="fas fa-edit"></i>
                                        <span>Sửa</span>
                                    </a>
                                    <button onclick="confirmDelete('{{ $phanCong->maphancong }}')" 
                                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center gap-2">
                                        <i class="fas fa-trash"></i>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $phanCongList->appends(request()->query())->links() }}
        </div>
    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-tasks text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">
                    @if($isTruongBoMon)
                        Chưa có phân công nào
                    @else
                        Chưa có công việc nào
                    @endif
                </h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'congviec', 'ban', 'cuocthi', 'giangvien_filter']))
                        Không tìm thấy phân công nào phù hợp với bộ lọc.
                    @elseif($isTruongBoMon)
                        Chưa có phân công nào trong bộ môn. Bạn có thể tạo phân công mới.
                    @else
                        Hiện tại bạn chưa được phân công công việc nào.
                    @endif
                </p>
                <div class="flex gap-3 justify-center">
                    @if(request()->hasAny(['search', 'congviec', 'ban', 'cuocthi', 'giangvien_filter']))
                    <a href="{{ route('giangvien.phancong.index') }}" 
                        class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                        <i class="fas fa-rotate-right mr-2"></i>Xóa bộ lọc
                    </a>
                    @endif
                    
                    @if($isTruongBoMon)
                    <a href="{{ route('giangvien.phancong.create') }}" 
                        class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                        <i class="fas fa-plus mr-2"></i>Tạo phân công
                    </a>
                    @endif
                    
                    <a href="{{ route('giangvien.profile.index') }}" 
                        class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border border-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    @endif
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
    if (confirm('Bạn có chắc chắn muốn xóa phân công này?')) {
        const form = document.getElementById('delete-form');
        form.action = `/giang-vien/phan-cong/${id}`;
        form.submit();
    }
}
</script>
@endpush

@endsection