@extends('layouts.client')
@section('title', 'Quản lý Ban')

@section('content')

{{-- 🎯 HERO SECTION: Phong cách Academic Management --}}
<section class="relative bg-gradient-to-br from-slate-900 to-purple-900 text-white py-16 overflow-hidden">
    {{-- Background Effect giữ lại --}}
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">/</span>
                    <span class="text-white/90 text-sm font-medium">Quản lý tổ chức</span>
                </div>
                <h1 class="text-3xl font-bold mb-2 tracking-tight">
                    <i class="fas fa-users-cog mr-3 text-purple-400"></i>
                    Quản lý Ban Cuộc thi
                </h1>
                <p class="text-purple-200 text-lg font-light">
                    Tạo, điều chỉnh và quản lý thành phần các ban tổ chức thuộc bộ môn
                </p>
            </div>
            
            {{-- Stat Box Gọn --}}
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 text-center">
                    <div class="text-3xl font-bold mb-1">{{ $cuocThiList->count() }}</div>
                    <div class="text-sm text-purple-200">Tổng Cuộc thi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Đường cắt trắng sạch --}}
    <div class="absolute bottom-0 left-0 right-0 h-8 bg-white" style="clip-path: polygon(100% 0, 0% 0%, 0% 100%, 100% 100%);"></div>
</section>

{{-- 📊 STATS BAR: Gọn gàng, border-separated --}}
<section class="container mx-auto px-6 -mt-4 relative z-20 mb-10">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-3 gap-4 divide-x divide-gray-100">
        {{-- Tổng cuộc thi --}}
        <div class="flex items-center gap-4 px-4">
            <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-trophy"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-800 leading-none">{{ $cuocThiList->count() }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Tổng cuộc thi</div>
            </div>
        </div>
        {{-- Tổng số ban --}}
        <div class="flex items-center gap-4 px-4">
            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-800 leading-none">{{ $cuocThiList->sum(function($ct) { return $ct->bans->count(); }) }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Tổng số ban</div>
            </div>
        </div>
        {{-- Tổng phân công --}}
        <div class="flex items-center gap-4 px-4">
            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-briefcase"></i>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-800 leading-none">{{ $cuocThiList->sum(function($ct) { return $ct->bans->sum('phancongs_count'); }) }}</div>
                <div class="text-xs text-gray-500 uppercase font-semibold mt-1">Tổng phân công</div>
            </div>
        </div>
    </div>
</section>


{{-- 📋 DANH SÁCH CUỘC THI & BAN --}}
<section class="container mx-auto px-6 pb-12">
    @if($cuocThiList->count() > 0)
        <div class="space-y-10">
            @foreach($cuocThiList as $cuocThi)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                {{-- Header Cuộc thi (Sử dụng màu Tím đồng nhất) --}}
                <div class="bg-purple-700 text-white p-5">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        
                        <div class="flex-1 flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-trophy text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-1">{{ $cuocThi->tencuocthi }}</h3>
                                <div class="flex flex-wrap items-center gap-x-4 text-sm text-purple-200">
                                    <span><i class="far fa-calendar mr-1"></i>{{ $cuocThi->thoigianbatdau ? \Carbon\Carbon::parse($cuocThi->thoigianbatdau)->format('d/m/Y') : 'N/A' }}</span>
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $cuocThi->diadiem ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('giangvien.phancong.ban.create', $cuocThi->macuocthi) }}" 
                            class="bg-white text-purple-700 px-5 py-2.5 rounded-lg font-semibold hover:bg-purple-50 transition shadow-md whitespace-nowrap">
                            <i class="fas fa-plus mr-2"></i>Thêm Ban mới
                        </a>
                    </div>
                </div>

                {{-- Danh sách Ban --}}
                <div class="p-6">
                    @if($cuocThi->bans->count() > 0)
                        <div class="mb-4 text-sm text-gray-600 font-medium">
                            <i class="fas fa-layer-group text-purple-500 mr-1"></i>
                            Tổng cộng **{{ $cuocThi->bans->count() }}** ban thuộc cuộc thi này.
                        </div>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($cuocThi->bans as $ban)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:border-purple-400 hover:shadow-md transition group">
                                
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg text-gray-800 group-hover:text-purple-700 transition mb-1">
                                            {{ $ban->tenban }}
                                        </h4>
                                        <p class="text-xs text-gray-500 font-medium">{{ $ban->maban }}</p>
                                    </div>
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-medium flex-shrink-0">
                                        <i class="fas fa-user-friends text-xs mr-1"></i>{{ $ban->phancongs_count ?? 0 }} người
                                    </span>
                                </div>

                                @if($ban->mota)
                                <p class="text-sm text-gray-600 mt-2 mb-3 line-clamp-2">{{ $ban->mota }}</p>
                                @endif

                                <div class="flex justify-end pt-3 border-t border-gray-100 gap-2">
                                    <a href="{{ route('giangvien.phancong.ban.edit', $ban->maban) }}" 
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-amber-600 bg-amber-50 hover:bg-amber-100 transition"
                                        title="Sửa Ban">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('giangvien.phancong.ban.destroy', $ban->maban) }}" 
                                        method="POST" 
                                        class="inline-block"
                                        onsubmit="return confirmDeleteBan('{{ $ban->tenban }}', {{ $ban->phancongs_count ?? 0 }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-100 transition"
                                            title="Xóa Ban">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty state cho ban --}}
                        <div class="text-center py-10 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="mb-4">
                                <i class="fas fa-users-slash text-5xl text-gray-300"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Cuộc thi này chưa có ban tổ chức</h4>
                            <a href="{{ route('giangvien.phancong.ban.create', $cuocThi->macuocthi) }}" 
                                class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition">
                                <i class="fas fa-plus"></i>
                                <span>Tạo ban đầu tiên</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        {{-- Empty state cho cuộc thi --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-trophy text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có cuộc thi nào</h4>
                <p class="text-gray-500 mb-8">
                    Bộ môn của bạn chưa có cuộc thi nào để quản lý ban.
                </p>
                <a href="{{ route('giangvien.phancong.index') }}" 
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại Quản lý Phân công</span>
                </a>
            </div>
        </div>
    @endif
</section>

@push('scripts')
<script>
function confirmDeleteBan(tenban, soLuongPhanCong) {
    if (soLuongPhanCong > 0) {
        alert(`Không thể xóa ban "${tenban}" vì đang có ${soLuongPhanCong} giảng viên được phân công.\n\nVui lòng xóa các phân công trước khi xóa ban.`);
        return false;
    }
    
    return confirm(`Bạn có chắc chắn muốn xóa ban "${tenban}"?\n\nHành động này không thể hoàn tác.`);
}
</script>
@endpush

@endsection