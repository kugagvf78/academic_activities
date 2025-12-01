@extends('layouts.client')
@section('title', 'Danh sách giải đã gán - ' . $cocau->tengiai)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.show', $cocau->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Danh sách giải đã gán</span>
        </div>
        
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-award mr-3"></i>{{ $cocau->tengiai }}
        </h1>
        <p class="text-cyan-100">{{ $cocau->cuocthi->tencuocthi }}</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- THÔNG TIN CƠ CẤU --}}
<section class="container mx-auto px-6 -mt-12 relative z-20 mb-8">
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
        <div class="grid md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-black text-blue-600 mb-1">
                    {{ $cocau->chophepdonghang ? '∞' : $cocau->soluong }}
                </div>
                <div class="text-sm font-medium text-gray-600">
                    {{ $cocau->chophepdonghang ? 'Không giới hạn' : 'Số lượng slot' }}
                </div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-black text-emerald-600 mb-1">{{ $gangiaiList->count() }}</div>
                <div class="text-sm font-medium text-gray-600">Đã gán</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-black text-green-600 mb-1">{{ number_format($cocau->tienthuong) }}</div>
                <div class="text-sm font-medium text-gray-600">Tiền thưởng (VNĐ)</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-black text-amber-600 mb-1">
                    {{ $cocau->giaykhen ? 'Có' : 'Không' }}
                </div>
                <div class="text-sm font-medium text-gray-600">Giấy khen</div>
            </div>
        </div>
    </div>
</section>

{{-- DANH SÁCH GIẢI ĐÃ GÁN --}}
<section class="container mx-auto px-6 pb-12">
    @if($gangiaiList->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-blue-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Người nhận</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Loại</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Xếp hạng</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Đồng hạng</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Người duyệt</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ngày duyệt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($gangiaiList as $index => $gangiai)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $index + 1 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gradient-to-br from-blue-100 to-cyan-100 p-2.5 rounded-lg">
                                        <i class="fas {{ $gangiai->loaidangky == 'CaNhan' ? 'fa-user' : 'fa-users' }} text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            @if($gangiai->loaidangky == 'CaNhan' && $gangiai->dangkycanhan)
                                                {{ $gangiai->dangkycanhan->sinhvien->nguoidung->hoten ?? 'N/A' }}
                                            @elseif($gangiai->loaidangky == 'DoiNhom' && $gangiai->dangkydoi)
                                                {{ $gangiai->dangkydoi->tendoi ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        @if($gangiai->loaidangky == 'CaNhan' && $gangiai->dangkycanhan)
                                            <div class="text-xs text-gray-500">
                                                MSSV: {{ $gangiai->dangkycanhan->sinhvien->masinhvien ?? 'N/A' }}
                                            </div>
                                        @elseif($gangiai->loaidangky == 'DoiNhom' && $gangiai->dangkydoi)
                                            <div class="text-xs text-gray-500">
                                                {{ $gangiai->dangkydoi->thanhvien->count() ?? 0 }} thành viên
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($gangiai->loaidangky == 'CaNhan')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold border border-blue-200">
                                        Cá nhân
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold border border-purple-200">
                                        Đội nhóm
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-amber-100 to-yellow-100 rounded-lg border border-amber-200">
                                    <span class="text-sm font-black text-amber-700">{{ $gangiai->xephangthucte }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($gangiai->ladongkang)
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold border border-purple-200">
                                        <i class="fas fa-equals mr-1"></i>Đồng hạng
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="bg-green-100 text-green-700 border-green-200 px-3 py-1 rounded-full text-xs font-semibold border">
                                    <i class="fas fa-check-circle mr-1"></i>Đã gán
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($gangiai->nguoiduyet)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $gangiai->nguoiduyet ?? 'N/A' }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Chưa duyệt</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($gangiai->ngayduyet)
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($gangiai->ngayduyet)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($gangiai->ngayduyet)->format('H:i') }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Thống kê chi tiết --}}
        <div class="mt-8 grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-green-100 p-6">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-4 rounded-xl">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-800">
                            {{ $gangiaiList->where('trangthai', 'Approved')->count() }}
                        </div>
                        <div class="text-sm font-medium text-gray-600">Đã gán</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-red-100 p-6">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-red-100 to-rose-100 p-4 rounded-xl">
                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-800">
                            {{ $gangiaiList->where('trangthai', 'Rejected')->count() }}
                        </div>
                        <div class="text-sm font-medium text-gray-600">Từ chối</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-award text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có giải nào được gán</h4>
                <p class="text-gray-500 mb-8">
                    Cơ cấu giải thưởng này chưa có giải nào được gán cho sinh viên/đội.
                </p>
                <a href="{{ route('giangvien.giaithuong.show', $cocau->macuocthi) }}" 
                    class="inline-block bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại cơ cấu giải
                </a>
            </div>
        </div>
    @endif
</section>

@endsection