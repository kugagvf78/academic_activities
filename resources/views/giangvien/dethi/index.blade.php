@extends('layouts.client')

@section('title', 'Quản lý Đề thi')

@section('content')
<section class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản lý Đề thi</h1>
            <p class="text-gray-600 mt-1">Danh sách các đề thi bạn đã tạo</p>
        </div>
        <a href="{{ route('giangvien.dethi.create') }}" 
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tạo đề thi mới</span>
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
        <form method="GET" class="grid md:grid-cols-5 gap-4 items-end">
            
            {{-- Ô tìm kiếm tên đề thi --}}
            <div>
                <input type="text" 
                    name="search" 
                    placeholder="Tìm kiếm tên đề thi..." 
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Lọc cuộc thi --}}
            <div>
                <select name="macuocthi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tất cả cuộc thi --</option>
                    @foreach($cuocthiList as $ct)
                        <option value="{{ $ct->macuocthi }}" {{ request('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                            {{ $ct->tencuocthi }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lọc loại đề thi --}}
            <div>
                <select name="loaidethi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Loại đề thi --</option>
                    <option value="LyThuyet" {{ request('loaidethi') == 'LyThuyet' ? 'selected' : '' }}>Lý thuyết</option>
                    <option value="ThucHanh" {{ request('loaidethi') == 'ThucHanh' ? 'selected' : '' }}>Thực hành</option>
                    <option value="VietBao" {{ request('loaidethi') == 'VietBao' ? 'selected' : '' }}>Viết báo</option>
                    <option value="Khac" {{ request('loaidethi') == 'Khac' ? 'selected' : '' }}>Khác</option>
                </select>
            </div>

            {{-- Lọc trạng thái --}}
            <div>
                <select name="trangthai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Trạng thái --</option>
                    <option value="Draft" {{ request('trangthai') == 'Draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="Active" {{ request('trangthai') == 'Active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="Archived" {{ request('trangthai') == 'Archived' ? 'selected' : '' }}>Đã lưu trữ</option>
                </select>
            </div>

            {{-- Nút tìm kiếm + nút xóa lọc --}}
            <div class="flex gap-3">
                <button type="submit" 
                        class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </button>

                @if(request()->hasAny(['search', 'macuocthi', 'loaidethi', 'trangthai']))
                    <a href="{{ route('giangvien.dethi.index') }}"
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đề thi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuộc thi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài thi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dethiList as $dt)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $dt->tendethi }}</div>
                            <div class="text-sm text-gray-500">{{ $dt->madethi }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-700">{{ $dt->tencuocthi }}</div>
                            <div class="text-sm text-gray-500">{{ $dt->loaicuocthi }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($dt->loaidethi == 'LyThuyet') bg-blue-100 text-blue-700
                                @elseif($dt->loaidethi == 'ThucHanh') bg-purple-100 text-purple-700
                                @elseif($dt->loaidethi == 'VietBao') bg-green-100 text-green-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                @if($dt->loaidethi == 'LyThuyet') Lý thuyết
                                @elseif($dt->loaidethi == 'ThucHanh') Thực hành
                                @elseif($dt->loaidethi == 'VietBao') Viết báo
                                @else Khác
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div><i class="fas fa-clock mr-1"></i>{{ $dt->thoigianlambai }} phút</div>
                            <div class="text-gray-400"><i class="fas fa-star mr-1"></i>{{ $dt->diemtoida }} điểm</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                {{ $dt->sobaithi }} bài
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($dt->status_color == 'green') bg-green-100 text-green-700
                                @elseif($dt->status_color == 'yellow') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $dt->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('giangvien.dethi.show', $dt->madethi) }}" 
                                    class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($dt->sobaithi == 0)
                                    <a href="{{ route('giangvien.dethi.edit', $dt->madethi) }}" 
                                        class="text-green-600 hover:text-green-800" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('giangvien.dethi.destroy', $dt->madethi) }}" 
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa đề thi này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400" title="Không thể sửa/xóa đề thi đã có bài nộp">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">Chưa có đề thi nào</p>
                                <p class="text-sm mt-2">Hãy tạo đề thi đầu tiên của bạn</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $dethiList->links() }}
        </div>
    </div>
</section>
@endsection