@extends('layouts.client')
@section('title', 'Quản lý Giải thưởng')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-orange-600 to-red-500 text-white py-24 overflow-hidden">
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
                    <span class="text-white/90 text-sm">Quản lý Giải thưởng</span>
                </div>
                <h1 class="text-4xl font-black mb-2 flex items-center gap-3">
                    <i class="fas fa-trophy"></i>
                    Quản lý Giải thưởng
                </h1>
                <p class="text-orange-100">Quản lý và trao giải thưởng cho các cuộc thi</p>
            </div>
            <div class="hidden lg:grid grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 text-center min-w-[120px]">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['total'] }}</div>
                    <div class="text-sm text-orange-100">Tổng giải</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 text-center min-w-[120px]">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['canhan'] }}</div>
                    <div class="text-sm text-orange-100">Cá nhân</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 text-center min-w-[120px]">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['doinh'] }}</div>
                    <div class="text-sm text-orange-100">Đội nhóm</div>
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

{{-- ACTIONS & FILTER --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-orange-100 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4 items-end">
            {{-- Form Filter --}}
            <form method="GET" action="{{ route('giangvien.giaithuong.index') }}" class="flex-1 grid lg:grid-cols-4 md:grid-cols-2 gap-4">
                <div class="lg:col-span-2 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Tìm theo tên cuộc thi, tên giải..." 
                        class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                </div>

                <div>
                    <select name="macuocthi" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                        <option value="">Tất cả cuộc thi</option>
                        @foreach($cuocthiList as $ct)
                        <option value="{{ $ct->macuocthi }}" {{ request('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                            {{ $ct->tencuocthi }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="loaidangky" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                        <option value="">Tất cả loại</option>
                        <option value="CaNhan" {{ request('loaidangky') == 'CaNhan' ? 'selected' : '' }}>Cá nhân</option>
                        <option value="DoiNhom" {{ request('loaidangky') == 'DoiNhom' ? 'selected' : '' }}>Đội nhóm</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-red-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-orange-700 hover:to-red-600 transition shadow-md hover:shadow-lg">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                    @if(request()->hasAny(['search', 'macuocthi', 'loaidangky', 'namhoc']))
                    <a href="{{ route('giangvien.giaithuong.index') }}" 
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-rotate-right"></i>
                    </a>
                    @endif
                </div>
            </form>

            {{-- Button Thêm mới --}}
            <div>
                <a href="{{ route('giangvien.giaithuong.create') }}" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:from-emerald-700 hover:to-green-700 transition shadow-md hover:shadow-lg whitespace-nowrap">
                    <i class="fas fa-plus-circle"></i>
                    <span>Thêm giải thưởng</span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- DANH SÁCH GIẢI THƯỞNG --}}
<section class="container mx-auto px-6 py-12">
    @if($giaithuongList->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-orange-50 to-red-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Cuộc thi
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Người đạt giải
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Loại
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Tên giải
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Điểm RL
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Ngày trao
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($giaithuongList as $item)
                        <tr class="hover:bg-orange-50/30 transition group">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 mb-1">{{ $item->tencuocthi }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $item->namhoc }} - Học kỳ {{ $item->hocky }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $item->ten_nguoi_dat_giai ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->loaidangky === 'CaNhan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-user"></i>
                                    Cá nhân
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-users"></i>
                                    Đội nhóm
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-trophy text-amber-500"></i>
                                    <span class="font-semibold text-gray-900">{{ $item->tengiai }}</span>
                                </div>
                                @if($item->giaithuong)
                                <div class="text-xs text-gray-500 mt-1">{{ $item->giaithuong }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->diemrenluyen)
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                    <i class="fas fa-star text-xs"></i>
                                    {{ number_format($item->diemrenluyen, 1) }}
                                </span>
                                @else
                                <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">
                                    <i class="far fa-calendar-check text-orange-500 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($item->ngaytrao)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('giangvien.giaithuong.show', $item->madatgiai) }}" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                        title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('giangvien.giaithuong.edit', $item->madatgiai) }}" 
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" 
                                        title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('giangvien.giaithuong.destroy', $item->madatgiai) }}" 
                                        class="inline-block"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa giải thưởng này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" 
                                            title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $giaithuongList->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-trophy text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có giải thưởng</h4>
                <p class="text-gray-500 mb-8">
                    @if(request()->hasAny(['search', 'macuocthi', 'loaidangky']))
                        Không tìm thấy giải thưởng nào phù hợp với bộ lọc của bạn.
                    @else
                        Hiện tại chưa có giải thưởng nào được trao.
                    @endif
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @if(request()->hasAny(['search', 'macuocthi', 'loaidangky']))
                    <a href="{{ route('giangvien.giaithuong.index') }}" 
                        class="bg-gradient-to-r from-orange-600 to-red-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-rotate-right mr-2"></i>Xóa bộ lọc
                    </a>
                    @endif
                    <a href="{{ route('giangvien.giaithuong.create') }}" 
                        class="bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i>Thêm giải thưởng
                    </a>
                </div>
            </div>
        </div>
    @endif
</section>

@endsection