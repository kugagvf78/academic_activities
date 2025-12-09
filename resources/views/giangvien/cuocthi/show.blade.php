@extends('layouts.client')

@section('title', 'Chi tiết cuộc thi')

@section('content')
{{-- HERO SECTION: Phong cách Academic, nghiêm túc --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-6 text-sm font-medium tracking-wide text-blue-200">
                    <a href="{{ route('giangvien.cuocthi.index') }}" class="hover:text-white transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Danh sách
                    </a>
                    <span class="text-slate-600">/</span>
                    <span class="text-white">Chi tiết</span>
                </div>
                
                <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4 tracking-tight">
                    {{ $cuocthi->tencuocthi }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-sm md:text-base text-slate-300">
                    <span class="flex items-center gap-2 px-3 py-1 bg-slate-800 rounded-md border border-slate-700">
                        <i class="fas fa-layer-group text-blue-400"></i> {{ $cuocthi->loaicuocthi }}
                    </span>
                    <span class="flex items-center gap-2">
                        <i class="far fa-building text-blue-400"></i> {{ $cuocthi->tenbomon ?? 'Khoa/Viện' }}
                    </span>
                    <span class="w-1 h-1 bg-slate-500 rounded-full"></span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($cuocthi->trangthai == 'Approved') bg-green-500/20 text-green-400 border border-green-500/30
                        @elseif($cuocthi->trangthai == 'Pending') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                        @elseif($cuocthi->trangthai == 'InProgress') bg-blue-500/20 text-blue-400 border border-blue-500/30
                        @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                        @endif">
                        {{ $cuocthi->trangthai }}
                    </span>
                </div>
            </div>

            {{-- Big Stat Box --}}
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-6 min-w-[200px]">
                <p class="text-white text-sm font-medium uppercase tracking-wider mb-1">Tổng đăng ký</p>
                <div class="text-4xl font-black text-white tracking-tight">
                    {{ str_pad($cuocthi->soluongdangky, 2, '0', STR_PAD_LEFT) }}
                    <span class="text-lg font-normal text-white">lượt</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section class="bg-slate-50 min-h-screen pb-20">
    <div class="container mx-auto px-6 -mt-10 relative z-20">
        
        {{-- Alert Messages (Giữ nguyên logic, làm đẹp UI) --}}
        @if(session('success'))
            <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
                <div class="text-green-500 bg-green-50 p-2 rounded-full"><i class="fas fa-check"></i></div>
                <span class="text-slate-700 font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-white border-l-4 border-red-500 p-4 shadow-sm rounded-r-lg flex items-center gap-3">
                <div class="text-red-500 bg-red-50 p-2 rounded-full"><i class="fas fa-exclamation"></i></div>
                <span class="text-slate-700 font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-8">
            {{-- Left Column (Content) --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- 1. Tổng quan (Clean & Professional) --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 px-8 py-5 flex items-center gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Nội dung chi tiết</h2>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        @if($cuocthi->mota)
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3 border-l-4 border-blue-500 pl-3">Mô tả cuộc thi</h3>
                            <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-justify">
                                {{ $cuocthi->mota }}
                            </div>
                        </div>
                        @endif

                        <div class="grid md:grid-cols-2 gap-8">
                            @if($cuocthi->mucdich)
                            <div class="bg-slate-50 p-5 rounded-lg border border-slate-100">
                                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                                    <i class="fas fa-bullseye text-red-500"></i> Mục đích
                                </h3>
                                <p class="text-sm text-slate-600">{{ $cuocthi->mucdich }}</p>
                            </div>
                            @endif

                            @if($cuocthi->doituongthamgia)
                            <div class="bg-slate-50 p-5 rounded-lg border border-slate-100">
                                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                                    <i class="fas fa-users text-indigo-500"></i> Đối tượng
                                </h3>
                                <p class="text-sm text-slate-600">{{ $cuocthi->doituongthamgia }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. Lộ trình / Vòng thi (Timeline Style) --}}
                @if($vongthi->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 px-8 py-5 flex items-center gap-3">
                        <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Lộ trình cuộc thi</h2>
                    </div>
                    <div class="p-8">
                        <div class="relative pl-8 border-l-2 border-blue-100 space-y-8">
                            @foreach($vongthi as $vt)
                            <div class="relative">
                                {{-- Timeline Dot --}}
                                <div class="absolute -left-[41px] top-0 flex items-center justify-center w-8 h-8 rounded-full bg-white border-2 border-blue-500 text-blue-600 text-xs font-bold shadow-sm">
                                    {{ $vt->thutu }}
                                </div>
                                
                                <div class="bg-white border border-slate-200 rounded-lg p-5 hover:shadow-md transition duration-300">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
                                        <h4 class="font-bold text-slate-800 text-lg">{{ $vt->tenvongthi }}</h4>
                                        <div class="flex items-center gap-2 text-sm text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                            <i class="far fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($vt->thoigianbatdau)->format('d/m/Y') }}</span>
                                            @if($vt->thoigianketthuc)
                                                <span>-</span>
                                                <span>{{ \Carbon\Carbon::parse($vt->thoigianketthuc)->format('d/m/Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($vt->mota)
                                        <p class="text-slate-600 text-sm leading-relaxed">{{ $vt->mota }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- 3. Danh sách đăng ký (Table Style) --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 px-8 py-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Danh sách đăng ký</h2>
                        </div>
                        {{-- Tabs nhỏ --}}
                        <div class="flex gap-2">
                            <span class="text-xs font-semibold px-2 py-1 bg-slate-100 text-slate-600 rounded">Cá nhân: {{ $dangkycanhan->count() }}</span>
                            <span class="text-xs font-semibold px-2 py-1 bg-slate-100 text-slate-600 rounded">Đội: {{ $dangkydoi->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="p-0">
                        {{-- Logic hiển thị table --}}
                        @if($dangkycanhan->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                                        <th class="px-6 py-4">Sinh viên</th>
                                        <th class="px-6 py-4">Lớp</th>
                                        <th class="px-6 py-4 text-center">Trạng thái</th>
                                        <th class="px-6 py-4 text-right">Ngày đăng ký</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($dangkycanhan as $dk)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-medium text-slate-700">{{ $dk->hoten }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $dk->lop }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-1 rounded text-[11px] font-bold uppercase
                                                @if($dk->trangthai == 'Approved') bg-green-100 text-green-700
                                                @elseif($dk->trangthai == 'Pending') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ $dk->trangthai }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 text-right font-mono">
                                            {{ \Carbon\Carbon::parse($dk->ngaydangky)->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        {{-- Đăng ký đội --}}
                        @if($dangkydoi->count() > 0)
                        <div class="px-6 py-6 space-y-4 bg-slate-50/50">
                            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Các đội tham gia</h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($dangkydoi as $dk)
                                <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:border-blue-300 transition group">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-bold text-blue-900 group-hover:text-blue-600">{{ $dk->tendoithi }}</h5>
                                        <span class="w-2 h-2 rounded-full 
                                            @if($dk->trangthai == 'Approved') bg-green-500
                                            @elseif($dk->trangthai == 'Pending') bg-yellow-500
                                            @else bg-red-500
                                            @endif"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-slate-500 mt-3">
                                        <span class="flex items-center gap-1"><i class="fas fa-users"></i> {{ $dk->soluongthanhvien }} tv</span>
                                        <span>{{ \Carbon\Carbon::parse($dk->ngaydangky)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($dangkycanhan->count() == 0 && $dangkydoi->count() == 0)
                        <div class="text-center py-16">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                <i class="fas fa-inbox text-slate-300 text-2xl"></i>
                            </div>
                            <p class="text-slate-500">Chưa có dữ liệu đăng ký nào.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column (Sidebar) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Actions Card --}}
                <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-6 sticky top-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Thao tác quản lý</h3>
                    <div class="space-y-3">
                        <a href="{{ route('giangvien.cuocthi.edit', $cuocthi->macuocthi) }}" 
                            class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition shadow-md hover:shadow-lg gap-2">
                            <i class="fas fa-edit"></i> Chỉnh sửa cuộc thi
                        </a>
                        
                        <form action="{{ route('giangvien.cuocthi.destroy', $cuocthi->macuocthi) }}" method="POST"
                            onsubmit="return confirm('Hành động này không thể hoàn tác. Bạn chắc chắn muốn xóa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="flex items-center justify-center w-full px-4 py-3 bg-white border border-red-200 text-red-600 hover:bg-red-50 rounded-lg font-semibold transition gap-2">
                                <i class="fas fa-trash-alt"></i> Xóa cuộc thi
                            </button>
                        </form>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Thông tin tổ chức</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <i class="fas fa-calendar-alt mt-1 text-slate-400 w-4"></i>
                                <div>
                                    <span class="block text-xs text-slate-500">Bắt đầu</span>
                                    <span class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('H:i - d/m/Y') }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-calendar-check mt-1 text-slate-400 w-4"></i>
                                <div>
                                    <span class="block text-xs text-slate-500">Kết thúc</span>
                                    <span class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('H:i - d/m/Y') }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt mt-1 text-slate-400 w-4"></i>
                                <div>
                                    <span class="block text-xs text-slate-500">Địa điểm</span>
                                    <span class="font-medium text-slate-800">{{ $cuocthi->diadiem }}</span>
                                </div>
                            </li>
                            @if($cuocthi->dutrukinhphi)
                            <li class="flex items-start gap-3">
                                <i class="fas fa-coins mt-1 text-slate-400 w-4"></i>
                                <div>
                                    <span class="block text-xs text-slate-500">Dự trù kinh phí</span>
                                    <span class="font-bold text-green-600">{{ number_format($cuocthi->dutrukinhphi) }} đ</span>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100">
                         <div class="bg-blue-50/50 rounded-lg p-4 border border-blue-100">
                            <p class="text-xs text-blue-800 font-semibold mb-2">Hình thức tham gia</p>
                            <div class="flex items-center gap-2 text-blue-600 font-bold">
                                @if($cuocthi->hinhthucthamgia == 'CaNhan')
                                    <i class="fas fa-user"></i> Cá nhân
                                @elseif($cuocthi->hinhthucthamgia == 'DoiNhom')
                                    <i class="fas fa-users"></i> Đội nhóm
                                @else
                                    <i class="fas fa-layer-group"></i> Hỗn hợp
                                @endif
                            </div>
                            @if($cuocthi->soluongthanhvien)
                                <p class="text-xs text-blue-500 mt-1">Tối đa {{ $cuocthi->soluongthanhvien }} thành viên/đội</p>
                            @endif
                         </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection