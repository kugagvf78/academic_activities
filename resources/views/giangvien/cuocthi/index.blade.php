@extends('layouts.client')

@section('title', 'Quản lý Cuộc thi')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black mb-2">
                    Quản lý Cuộc thi
                </h1>
                <p class="text-cyan-100">Danh sách các cuộc thi thuộc bộ môn của bạn</p>
            </div>
            <div class="hidden md:flex items-center gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $cuocthiList->total() }}</div>
                        <div class="text-sm text-cyan-100">Cuộc thi</div>
                    </div>
                </div>
                <a href="{{ route('giangvien.cuocthi.create') }}" 
                    class="px-6 py-3 bg-white text-blue-600 rounded-xl font-bold hover:bg-cyan-50 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Tạo cuộc thi mới</span>
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

    {{-- Mobile: Nút tạo cuộc thi --}}
    <div class="md:hidden mb-6">
        <a href="{{ route('giangvien.cuocthi.create') }}" 
            class="block w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl font-bold shadow-lg text-center">
            <i class="fas fa-plus mr-2"></i>Tạo cuộc thi mới
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6 mb-6">
        <form method="GET" class="grid md:grid-cols-4 gap-4">
            
            {{-- Ô tìm kiếm tên cuộc thi --}}
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                    name="search" 
                    placeholder="Tìm kiếm tên cuộc thi..." 
                    value="{{ request('search') }}"
                    class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            {{-- Lọc trạng thái --}}
            <div>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Trạng thái --</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="InProgress" {{ request('status') == 'InProgress' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            {{-- Lọc loại cuộc thi --}}
            <div>
                <select name="loai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Loại cuộc thi --</option>
                    <option value="CuocThi" {{ request('loai') == 'CuocThi' ? 'selected' : '' }}>Cuộc thi</option>
                    <option value="HoiThao" {{ request('loai') == 'HoiThao' ? 'selected' : '' }}>Hội thảo</option>
                </select>
            </div>

            {{-- Nút tìm kiếm + nút xóa lọc --}}
            <div class="flex gap-2">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-blue-700 hover:to-cyan-600 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>

                @if(request()->hasAny(['search', 'status', 'loai']))
                    <a href="{{ route('giangvien.cuocthi.index') }}"
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-rotate-right"></i>
                    </a>
                @endif
            </div>
            
        </form>
    </div>

    {{-- Table/Cards --}}
    @if($cuocthiList->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Cuộc thi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Thời gian</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Đăng ký</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cuocthiList as $ct)
                            <tr class="hover:bg-blue-50/50 transition duration-200">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $ct->tencuocthi }}</div>
                                    <div class="text-sm text-gray-500">{{ $ct->loaicuocthi }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <i class="fas fa-calendar-alt text-blue-500"></i>
                                        <span>{{ \Carbon\Carbon::parse($ct->thoigianbatdau)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="text-gray-400 ml-6 mt-1">đến {{ \Carbon\Carbon::parse($ct->thoigianketthuc)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                                        {{ $ct->soluongdangky }} người
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 rounded-full text-sm font-semibold
                                        @if($ct->status_color == 'green') bg-green-100 text-green-700
                                        @elseif($ct->status_color == 'yellow') bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        {{ $ct->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('giangvien.cuocthi.show', $ct->macuocthi) }}" 
                                            class="text-blue-600 hover:text-blue-800 hover:scale-110 transition transform" 
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye text-lg"></i>
                                        </a>
                                        <a href="{{ route('giangvien.cuocthi.edit', $ct->macuocthi) }}" 
                                            class="text-green-600 hover:text-green-800 hover:scale-110 transition transform" 
                                            title="Chỉnh sửa">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <form action="{{ route('giangvien.cuocthi.destroy', $ct->macuocthi) }}" 
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa cuộc thi này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="text-red-600 hover:text-red-800 hover:scale-110 transition transform" 
                                                title="Xóa">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $cuocthiList->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-trophy text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có cuộc thi nào</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'status', 'loai']))
                        Không tìm thấy cuộc thi nào phù hợp với bộ lọc của bạn.
                    @else
                        Hãy tạo cuộc thi đầu tiên của bạn để bắt đầu.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'status', 'loai']))
                    <a href="{{ route('giangvien.cuocthi.index') }}" 
                        class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-rotate-right mr-2"></i>Xóa bộ lọc
                    </a>
                    @endif
                    <a href="{{ route('giangvien.cuocthi.create') }}" 
                        class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition border-2 border-blue-600">
                        <i class="fas fa-plus mr-2"></i>Tạo cuộc thi mới
                    </a>
                </div>
            </div>
        </div>
    @endif
</section>

@endsection