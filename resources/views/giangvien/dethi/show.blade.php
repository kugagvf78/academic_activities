@extends('layouts.client')

@section('title', 'Chi tiết đề thi - ' . $dethi->tendethi)

@section('content')
<section class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('giangvien.dethi.index') }}" 
                class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Chi tiết đề thi</h1>
        </div>
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

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Cột trái - Thông tin chi tiết --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Thông tin đề thi --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-file-alt"></i>
                        {{ $dethi->tendethi }}
                    </h2>
                    <p class="text-blue-100 text-sm mt-1">Mã đề: {{ $dethi->madethi }}</p>
                </div>
                
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Thông tin cuộc thi --}}
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-trophy text-yellow-500"></i>
                                Thông tin cuộc thi
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Tên cuộc thi:</span>
                                    <span class="font-medium text-gray-800">{{ $dethi->tencuocthi }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Loại cuộc thi:</span>
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-medium">
                                        {{ $dethi->loaicuocthi }}
                                    </span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Thời gian:</span>
                                    <div class="text-gray-800">
                                        <div>{{ \Carbon\Carbon::parse($dethi->thoigianbatdau)->format('d/m/Y H:i') }}</div>
                                        <div class="text-xs text-gray-500">đến</div>
                                        <div>{{ \Carbon\Carbon::parse($dethi->thoigianketthuc)->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Thông tin đề thi --}}
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                Chi tiết đề thi
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Loại đề:</span>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @if($dethi->loaidethi == 'LyThuyet') bg-blue-100 text-blue-700
                                        @elseif($dethi->loaidethi == 'ThucHanh') bg-purple-100 text-purple-700
                                        @elseif($dethi->loaidethi == 'VietBao') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        @if($dethi->loaidethi == 'LyThuyet') Lý thuyết
                                        @elseif($dethi->loaidethi == 'ThucHanh') Thực hành
                                        @elseif($dethi->loaidethi == 'VietBao') Viết báo
                                        @else Khác
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Thời gian làm bài:</span>
                                    <span class="font-medium text-gray-800">{{ $dethi->thoigianlambai }} phút</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Điểm tối đa:</span>
                                    <span class="font-medium text-gray-800">{{ $dethi->diemtoida }} điểm</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 min-w-[120px]">Trạng thái:</span>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @if($dethi->status_color == 'green') bg-green-100 text-green-700
                                        @elseif($dethi->status_color == 'yellow') bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        {{ $dethi->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- File đề thi --}}
                    @if($dethi->filedethi)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-paperclip text-green-500"></i>
                                File đề thi đính kèm
                            </h3>
                            
                            @php
                                $extension = pathinfo($dethi->filedethi, PATHINFO_EXTENSION);
                                $isPdf = strtolower($extension) === 'pdf';
                            @endphp

                            <div class="flex flex-wrap gap-3">
                                {{-- Nút Xem (chỉ hiện với PDF)
                                @if($isPdf)
                                    <a href="{{ route('giangvien.dethi.view-file', $dethi->madethi) }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">
                                        <i class="fas fa-eye"></i>
                                        <span>Xem file đề thi</span>
                                    </a>
                                @endif --}}

                                {{-- Nút Tải xuống --}}
                                <a href="{{ route('giangvien.dethi.download-file', $dethi->madethi) }}" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-download"></i>
                                    <span>Tải xuống file</span>
                                </a>
                            </div>

                            {{-- Thông tin file --}}
                            <div class="mt-3 text-sm text-gray-500">
                                <i class="fas fa-file"></i>
                                <span>Định dạng: <strong class="uppercase">{{ $extension }}</strong></span>
                                <span class="mx-2">•</span>
                                <span>{{ basename($dethi->filedethi) }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Thông tin người tạo --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user"></i>
                                <span>Người tạo: <strong>{{ $dethi->nguoitao_ten }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar"></i>
                                <span>{{ \Carbon\Carbon::parse($dethi->ngaytao)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danh sách bài thi --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-invoice"></i>
                        Danh sách bài thi
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium ml-2">
                            {{ $dethi->sobaithi }} bài
                        </span>
                    </h2>
                </div>

                @if($baithiList->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sinh viên</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đội thi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian nộp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($baithiList as $bt)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            @if($bt->masinhvien)
                                                <div class="font-medium text-gray-800">{{ $bt->sinhvien_ten }}</div>
                                                <div class="text-sm text-gray-500">{{ $bt->masinhvien }}</div>
                                            @else
                                                <span class="text-gray-400">--</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($bt->tendoithi)
                                                <span class="text-gray-800">{{ $bt->tendoithi }}</span>
                                            @else
                                                <span class="text-gray-400">Cá nhân</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            @if($bt->thoigiannop)
                                                {{ \Carbon\Carbon::parse($bt->thoigiannop)->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-yellow-600">Chưa nộp</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($bt->diemso !== null)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                                    {{ $bt->diemso }} điểm
                                                </span>
                                            @else
                                                <span class="text-gray-400">Chưa chấm</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="#" 
                                                class="text-blue-600 hover:text-blue-800" 
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium">Chưa có bài thi nào</p>
                        <p class="text-sm mt-2">Sinh viên sẽ nộp bài thi ở đây</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Cột phải - Hành động --}}
        <div class="space-y-6">
            {{-- Thao tác --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-blue-600"></i>
                    Thao tác
                </h2>
                
                <div class="space-y-3">
                    @if($dethi->sobaithi == 0)
                        <a href="{{ route('giangvien.dethi.edit', $dethi->madethi) }}" 
                            class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                            <i class="fas fa-edit"></i>
                            Chỉnh sửa
                        </a>
                        
                        <form action="{{ route('giangvien.dethi.destroy', $dethi->madethi) }}" 
                            method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa đề thi này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i>
                                Xóa đề thi
                            </button>
                        </form>
                    @else
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-lock text-yellow-600 mt-1"></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-semibold mb-1">Không thể chỉnh sửa</p>
                                    <p>Đề thi đã có bài nộp nên không thể sửa hoặc xóa.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('giangvien.dethi.index') }}" 
                        class="w-full px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                </div>
            </div>

            {{-- Thống kê --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                    Thống kê
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-500"></i>
                            <span class="text-sm text-gray-700">Tổng bài thi</span>
                        </div>
                        <span class="font-bold text-blue-600">{{ $dethi->sobaithi }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm text-gray-700">Đã chấm điểm</span>
                        </div>
                        <span class="font-bold text-green-600">
                            {{ $baithiList->where('diemso', '!=', null)->count() }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-yellow-500"></i>
                            <span class="text-sm text-gray-700">Chưa chấm</span>
                        </div>
                        <span class="font-bold text-yellow-600">
                            {{ $baithiList->where('diemso', null)->count() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Thông tin bổ sung --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin thêm
                </h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-code text-gray-400"></i>
                        <span>Mã đề: <strong>{{ $dethi->madethi }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-trophy text-gray-400"></i>
                        <span>Mã cuộc thi: <strong>{{ $dethi->macuocthi }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection