@extends('layouts.client')
@section('title', 'Quản lý Ban')

@section('content')
{{-- 🎯 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-purple-700 via-indigo-600 to-blue-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Quản lý tổ chức</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    <i class="fas fa-users-cog mr-3"></i>
                    Quản lý Ban Cuộc thi
                </h1>
                <p class="text-purple-100">
                    Tạo và quản lý các ban tổ chức cho cuộc thi trong bộ môn
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $cuocThiList->count() }}</div>
                        <div class="text-sm text-purple-100">Cuộc thi</div>
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

{{-- 📊 THỐNG KÊ NHANH --}}
<section class="container mx-auto px-6 -mt-8 relative z-20 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-purple-700">{{ $cuocThiList->count() }}</div>
            <div class="text-sm text-gray-500">Tổng cuộc thi</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-indigo-500">
            <div class="text-2xl font-bold text-indigo-700">{{ $cuocThiList->sum(function($ct) { return $ct->bans->count(); }) }}</div>
            <div class="text-sm text-gray-500">Tổng số ban</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-700">{{ $cuocThiList->sum(function($ct) { return $ct->bans->sum('phancongs_count'); }) }}</div>
            <div class="text-sm text-gray-500">Tổng phân công</div>
        </div>
    </div>
</section>

{{-- 📋 DANH SÁCH CUỘC THI & BAN --}}
<section class="container mx-auto px-6 pb-12">
    @if($cuocThiList->count() > 0)
        <div class="space-y-8">
            @foreach($cuocThiList as $cuocThi)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                {{-- Header Cuộc thi --}}
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-trophy text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-1">{{ $cuocThi->tencuocthi }}</h3>
                                <div class="flex items-center gap-4 text-sm text-purple-100">
                                    <span><i class="far fa-calendar mr-1"></i>{{ $cuocThi->thoigianbatdau ? \Carbon\Carbon::parse($cuocThi->thoigianbatdau)->format('d/m/Y') : 'N/A' }}</span>
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $cuocThi->diadiem ?? 'N/A' }}</span>
                                    <span class="bg-white/20 px-3 py-1 rounded-full">
                                        <i class="fas fa-users mr-1"></i>{{ $cuocThi->bans->count() }} ban
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('giangvien.phancong.ban.create', $cuocThi->macuocthi) }}" 
                            class="bg-white text-purple-600 px-6 py-2.5 rounded-xl font-semibold hover:bg-purple-50 transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Thêm Ban
                        </a>
                    </div>
                </div>

                {{-- Danh sách Ban --}}
                <div class="p-6">
                    @if($cuocThi->bans->count() > 0)
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($cuocThi->bans as $ban)
                            <div class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-xl p-5 hover:border-purple-300 hover:shadow-lg transition group">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                            <i class="fas fa-user-friends"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 group-hover:text-purple-600 transition">
                                                {{ $ban->tenban }}
                                            </h4>
                                            <p class="text-xs text-gray-500">{{ $ban->maban }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($ban->mota)
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $ban->mota }}</p>
                                @endif

                                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-medium">
                                            <i class="fas fa-users text-xs mr-1"></i>{{ $ban->phancongs_count ?? 0 }} người
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('giangvien.phancong.ban.edit', $ban->maban) }}" 
                                            class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center hover:bg-amber-200 transition"
                                            title="Sửa">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('giangvien.phancong.ban.destroy', $ban->maban) }}" 
                                            method="POST" 
                                            class="inline-block"
                                            onsubmit="return confirmDeleteBan('{{ $ban->tenban }}', {{ $ban->phancongs_count ?? 0 }})">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition"
                                                title="Xóa">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty state cho ban --}}
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <i class="fas fa-users text-6xl text-gray-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-700 mb-2">Chưa có ban nào</h4>
                            <p class="text-gray-500 mb-6">Cuộc thi này chưa có ban tổ chức nào</p>
                            <a href="{{ route('giangvien.phancong.ban.create', $cuocThi->macuocthi) }}" 
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
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
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-trophy text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có cuộc thi nào</h4>
                <p class="text-gray-500 mb-8">
                    Bộ môn của bạn chưa có cuộc thi nào. Vui lòng tạo cuộc thi trước khi quản lý ban.
                </p>
                <a href="{{ route('giangvien.phancong.index') }}" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
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