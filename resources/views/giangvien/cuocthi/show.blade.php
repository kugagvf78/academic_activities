@extends('layouts.client')

@section('title', 'Chi tiết cuộc thi')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.cuocthi.index') }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Chi tiết cuộc thi</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    {{ $cuocthi->tencuocthi }}
                </h1>
                <p class="text-cyan-100 flex items-center gap-2">
                    <i class="fas fa-tag"></i>{{ $cuocthi->loaicuocthi }}
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $cuocthi->soluongdangky }}</div>
                        <div class="text-sm text-cyan-100">Đăng ký</div>
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

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Thông tin cơ bản --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-5">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Thông tin cuộc thi
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                            {{ $cuocthi->loaicuocthi }}
                        </span>
                        <span class="px-4 py-2 rounded-full text-sm font-bold
                            @if($cuocthi->trangthai == 'Approved') bg-green-100 text-green-700
                            @elseif($cuocthi->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                            @elseif($cuocthi->trangthai == 'InProgress') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ $cuocthi->trangthai }}
                        </span>
                    </div>

                    <div class="space-y-5">
                        @if($cuocthi->mota)
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-5 border border-blue-200">
                            <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-align-left text-blue-500"></i>
                                Mô tả:
                            </h3>
                            <p class="text-gray-700 leading-relaxed">{{ $cuocthi->mota }}</p>
                        </div>
                        @endif

                        @if($cuocthi->mucdich)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                            <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-bullseye text-green-500"></i>
                                Mục đích:
                            </h3>
                            <p class="text-gray-700 leading-relaxed">{{ $cuocthi->mucdich }}</p>
                        </div>
                        @endif

                        @if($cuocthi->doituongthamgia)
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-200">
                            <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-users text-purple-500"></i>
                                Đối tượng tham gia:
                            </h3>
                            <p class="text-gray-700 leading-relaxed">{{ $cuocthi->doituongthamgia }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vòng thi --}}
            @if($vongthi->count() > 0)
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        Vòng thi ({{ $vongthi->count() }})
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($vongthi as $vt)
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 border border-blue-200 rounded-xl p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-800 text-lg">{{ $vt->tenvongthi }}</h4>
                            <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-bold">Thứ tự: {{ $vt->thutu }}</span>
                        </div>
                        @if($vt->mota)
                        <p class="text-gray-600 mb-3">{{ $vt->mota }}</p>
                        @endif
                        @if($vt->thoigianbatdau)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="far fa-clock text-blue-500"></i>
                            <span>{{ \Carbon\Carbon::parse($vt->thoigianbatdau)->format('d/m/Y H:i') }}</span>
                            @if($vt->thoigianketthuc)
                                <span>-</span>
                                <span>{{ \Carbon\Carbon::parse($vt->thoigianketthuc)->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Danh sách đăng ký --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-5">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-user-check"></i>
                        Danh sách đăng ký
                    </h3>
                </div>
                
                <div class="p-6">
                    {{-- Đăng ký cá nhân --}}
                    @if($dangkycanhan->count() > 0)
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-blue-500"></i>
                            Đăng ký cá nhân ({{ $dangkycanhan->count() }})
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Sinh viên</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Lớp</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Trạng thái</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Ngày đăng ký</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($dangkycanhan as $dk)
                                    <tr class="hover:bg-blue-50/50 transition">
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $dk->hoten }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $dk->lop }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                                @if($dk->trangthai == 'Approved') bg-green-100 text-green-700
                                                @elseif($dk->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ $dk->trangthai }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
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
                        <h4 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <i class="fas fa-users text-purple-500"></i>
                            Đăng ký đội ({{ $dangkydoi->count() }})
                        </h4>
                        <div class="space-y-3">
                            @foreach($dangkydoi as $dk)
                            <div class="bg-gradient-to-r from-gray-50 to-purple-50 border border-purple-200 rounded-xl p-5 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-bold text-gray-800 text-lg">{{ $dk->tendoithi }}</h5>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        @if($dk->trangthai == 'Approved') bg-green-100 text-green-700
                                        @elseif($dk->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ $dk->trangthai }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-friends text-purple-500"></i>
                                        <span>{{ $dk->soluongthanhvien }} thành viên</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-blue-500"></i>
                                        <span>{{ \Carbon\Carbon::parse($dk->ngaydangky)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($dangkycanhan->count() == 0 && $dangkydoi->count() == 0)
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Chưa có đăng ký nào</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Thao tác --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-blue-600"></i>
                    Thao tác
                </h2>
                
                <div class="space-y-3">
                    <a href="{{ route('giangvien.cuocthi.edit', $cuocthi->macuocthi) }}" 
                        class="block w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-center">
                        <i class="fas fa-edit mr-2"></i>
                        Chỉnh sửa
                    </a>
                    
                    <form action="{{ route('giangvien.cuocthi.destroy', $cuocthi->macuocthi) }}" 
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa cuộc thi này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full px-6 py-3.5 bg-gradient-to-r from-red-600 to-red-600 hover:from-red-700 hover:to-red-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                            <i class="fas fa-trash mr-2"></i>
                            Xóa
                        </button>
                    </form>

                    <a href="{{ route('giangvien.cuocthi.index') }}" 
                        class="block w-full px-6 py-3.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại
                    </a>
                </div>
            </div>

            {{-- Thông tin chi tiết --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin chi tiết
                </h3>
                <div class="space-y-4">
                    @if($cuocthi->tenbomon)
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Bộ môn</p>
                        <p class="font-bold text-gray-800">{{ $cuocthi->tenbomon }}</p>
                    </div>
                    @endif
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Thời gian bắt đầu</p>
                        <p class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-blue-500"></i>
                            {{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Thời gian kết thúc</p>
                        <p class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-calendar-check text-green-500"></i>
                            {{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @if($cuocthi->diadiem)
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Địa điểm</p>
                        <p class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-red-500"></i>
                            {{ $cuocthi->diadiem }}
                        </p>
                    </div>
                    @endif
                    @if($cuocthi->hinhthucthamgia)
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Hình thức tham gia</p>
                        <p class="font-bold text-gray-800">
                            @if($cuocthi->hinhthucthamgia == 'CaNhan') Cá nhân
                            @elseif($cuocthi->hinhthucthamgia == 'DoiNhom') Đội nhóm
                            @else Cả hai
                            @endif
                        </p>
                    </div>
                    @endif
                    @if($cuocthi->soluongthanhvien)
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Số lượng thành viên/đội</p>
                        <p class="font-bold text-gray-800">{{ $cuocthi->soluongthanhvien }} người</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Thống kê --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 shadow-md">
                <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                    Thống kê
                </h3>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-semibold">Tổng đăng ký</span>
                            <span class="text-3xl font-black text-blue-600">{{ $cuocthi->soluongdangky }}</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-semibold">Đăng ký cá nhân</span>
                            <span class="text-2xl font-bold text-green-600">{{ $dangkycanhan->count() }}</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-semibold">Đăng ký đội</span>
                            <span class="text-2xl font-bold text-purple-600">{{ $dangkydoi->count() }}</span>
                        </div>
                    </div>
                    @if($cuocthi->dutrukinhphi)
                    <div class="bg-white rounded-xl p-4 shadow-sm border-t-2 border-green-500">
                        <p class="text-gray-600 font-semibold mb-1">Dự trù kinh phí</p>
                        <p class="text-xl font-black text-green-600">
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