@extends('layouts.client')

@section('title', 'Chi tiết cuộc thi')

@section('content')
<section class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('giangvien.cuocthi.index') }}" 
                    class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Chi tiết cuộc thi</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('giangvien.cuocthi.edit', $cuocthi->macuocthi) }}" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                </a>
                <form action="{{ route('giangvien.cuocthi.destroy', $cuocthi->macuocthi) }}" 
                    method="POST" class="inline"
                    onsubmit="return confirm('Bạn có chắc muốn xóa cuộc thi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition">
                        <i class="fas fa-trash mr-2"></i>Xóa
                    </button>
                </form>
            </div>
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
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Thông tin cơ bản --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $cuocthi->tencuocthi }}</h2>
                
                <div class="flex items-center gap-4 mb-6">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                        {{ $cuocthi->loaicuocthi }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($cuocthi->trangthai == 'Approved') bg-green-100 text-green-700
                        @elseif($cuocthi->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                        @elseif($cuocthi->trangthai == 'InProgress') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ $cuocthi->trangthai }}
                    </span>
                </div>

                <div class="space-y-4">
                    @if($cuocthi->mota)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Mô tả:</h3>
                        <p class="text-gray-600">{{ $cuocthi->mota }}</p>
                    </div>
                    @endif

                    @if($cuocthi->mucdich)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Mục đích:</h3>
                        <p class="text-gray-600">{{ $cuocthi->mucdich }}</p>
                    </div>
                    @endif

                    @if($cuocthi->doituongthamgia)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Đối tượng tham gia:</h3>
                        <p class="text-gray-600">{{ $cuocthi->doituongthamgia }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vòng thi --}}
            @if($vongthi->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Vòng thi</h3>
                <div class="space-y-3">
                    @foreach($vongthi as $vt)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-800">{{ $vt->tenvongthi }}</h4>
                            <span class="text-sm text-gray-500">Thứ tự: {{ $vt->thutu }}</span>
                        </div>
                        @if($vt->mota)
                        <p class="text-sm text-gray-600">{{ $vt->mota }}</p>
                        @endif
                        @if($vt->thoigianbatdau)
                        <div class="text-sm text-gray-500 mt-2">
                            <i class="far fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($vt->thoigianbatdau)->format('d/m/Y H:i') }}
                            @if($vt->thoigianketthuc)
                                - {{ \Carbon\Carbon::parse($vt->thoigianketthuc)->format('d/m/Y H:i') }}
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Danh sách đăng ký --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Danh sách đăng ký</h3>
                
                {{-- Đăng ký cá nhân --}}
                @if($dangkycanhan->count() > 0)
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Đăng ký cá nhân ({{ $dangkycanhan->count() }})</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sinh viên</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Lớp</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Trạng thái</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Ngày đăng ký</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($dangkycanhan as $dk)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-800">{{ $dk->hoten }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $dk->lop }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs
                                            @if($dk->trangthai == 'Approved') bg-green-100 text-green-700
                                            @elseif($dk->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $dk->trangthai }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($dk->ngaydangky)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Đăng ký đội --}}
                @if($dangkydoi->count() > 0)
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Đăng ký đội ({{ $dangkydoi->count() }})</h4>
                    <div class="space-y-3">
                        @foreach($dangkydoi as $dk)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="font-semibold text-gray-800">{{ $dk->tendoithi }}</h5>
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($dk->trangthai == 'Approved') bg-green-100 text-green-700
                                    @elseif($dk->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $dk->trangthai }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span>Số thành viên: {{ $dk->soluongthanhvien }}</span>
                                <span class="ml-4">Ngày đăng ký: {{ \Carbon\Carbon::parse($dk->ngaydangky)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($dangkycanhan->count() == 0 && $dangkydoi->count() == 0)
                <p class="text-center text-gray-500 py-8">Chưa có đăng ký nào</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Thông tin chi tiết --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Thông tin chi tiết</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Bộ môn</p>
                        <p class="font-semibold text-gray-800">{{ $cuocthi->tenbomon ?? 'Chưa có' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Thời gian bắt đầu</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Thời gian kết thúc</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @if($cuocthi->diadiem)
                    <div>
                        <p class="text-sm text-gray-500">Địa điểm</p>
                        <p class="font-semibold text-gray-800">{{ $cuocthi->diadiem }}</p>
                    </div>
                    @endif
                    @if($cuocthi->hinhthucthamgia)
                    <div>
                        <p class="text-sm text-gray-500">Hình thức tham gia</p>
                        <p class="font-semibold text-gray-800">
                            @if($cuocthi->hinhthucthamgia == 'CaNhan') Cá nhân
                            @elseif($cuocthi->hinhthucthamgia == 'DoiNhom') Đội nhóm
                            @else Cả hai
                            @endif
                        </p>
                    </div>
                    @endif
                    @if($cuocthi->soluongthanhvien)
                    <div>
                        <p class="text-sm text-gray-500">Số lượng thành viên/đội</p>
                        <p class="font-semibold text-gray-800">{{ $cuocthi->soluongthanhvien }} người</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Thống kê --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Thống kê</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Tổng đăng ký</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $cuocthi->soluongdangky }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Đăng ký cá nhân</span>
                        <span class="text-xl font-semibold text-gray-800">{{ $dangkycanhan->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Đăng ký đội</span>
                        <span class="text-xl font-semibold text-gray-800">{{ $dangkydoi->count() }}</span>
                    </div>
                    @if($cuocthi->dutrukinhphi)
                    <div class="pt-4 border-t border-gray-200">
                        <span class="text-gray-600">Dự trù kinh phí</span>
                        <p class="text-xl font-semibold text-green-600">
                            {{ number_format($cuocthi->dutrukinhphi) }} VNĐ
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection