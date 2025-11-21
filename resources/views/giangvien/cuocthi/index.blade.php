@extends('layouts.client')

@section('title', 'Quản lý Cuộc thi')

@section('content')
<section class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản lý Cuộc thi</h1>
            <p class="text-gray-600 mt-1">Danh sách các cuộc thi thuộc bộ môn của bạn</p>
        </div>
        <a href="{{ route('giangvien.cuocthi.create') }}" 
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tạo cuộc thi mới</span>
        </a>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid md:grid-cols-4 gap-4 items-end">
            
            {{-- Ô tìm kiếm tên cuộc thi --}}
            <div>
                <input type="text" 
                    name="search" 
                    placeholder="Tìm kiếm tên cuộc thi..." 
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Lọc trạng thái - đúng với CHECK constraint trong DB --}}
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Trạng thái --</option>
                    <option value="Draft"     {{ request('status') == 'Draft'     ? 'selected' : '' }}>Nháp</option>
                    <option value="Approved"  {{ request('status') == 'Approved'  ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="InProgress">{{ request('status') == 'InProgress'? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            {{-- Lọc loại cuộc thi - chỉ còn 2 loại đúng với DB --}}
            <div>
                <select name="loai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Loại cuộc thi --</option>
                    <option value="CuocThi" {{ request('loai') == 'CuocThi' ? 'selected' : '' }}>Cuộc thi</option>
                    <option value="HoiThao" {{ request('loai') == 'HoiThao' ? 'selected' : '' }}>Hội thảo</option>
                </select>
            </div>

            {{-- Nút tìm kiếm + nút xóa lọc --}}
            <div class="flex gap-3">
                <button type="submit" 
                        class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </button>

                @if(request()->hasAny(['search', 'status', 'loai']))
                    <a href="{{ route('giangvien.cuocthi.index') }}"
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Xóa lọc
                    </a>
                @endif
            </div>
            
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuộc thi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đăng ký</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cuocthiList as $ct)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $ct->tencuocthi }}</div>
                            <div class="text-sm text-gray-500">{{ $ct->loaicuocthi }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div>{{ \Carbon\Carbon::parse($ct->thoigianbatdau)->format('d/m/Y') }}</div>
                            <div class="text-gray-400">đến {{ \Carbon\Carbon::parse($ct->thoigianketthuc)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                {{ $ct->soluongdangky }} người
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($ct->status_color == 'green') bg-green-100 text-green-700
                                @elseif($ct->status_color == 'yellow') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $ct->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('giangvien.cuocthi.show', $ct->macuocthi) }}" 
                                    class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('giangvien.cuocthi.edit', $ct->macuocthi) }}" 
                                    class="text-green-600 hover:text-green-800" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('giangvien.cuocthi.destroy', $ct->macuocthi) }}" 
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa cuộc thi này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Chưa có cuộc thi nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $cuocthiList->links() }}
        </div>
    </div>
</section>
@endsection