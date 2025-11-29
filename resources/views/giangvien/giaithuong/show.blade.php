@extends('layouts.client')
@section('title', 'Chi tiết Giải thưởng')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-orange-600 to-red-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Chi tiết giải thưởng</span>
        </div>
        <h1 class="text-3xl font-black flex items-center gap-3">
            <i class="fas fa-trophy"></i>
            {{ $giaithuong->tengiai }}
        </h1>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 70C120 60 240 40 360 35C480 30 600 40 720 45C840 50 960 50 1080 45C1200 40 1320 30 1380 25L1440 20V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- CONTENT --}}
<section class="container mx-auto px-6 py-12 -mt-8 relative z-10">
    <div class="max-w-5xl mx-auto">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Thông tin cuộc thi --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-blue-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-trophy text-blue-600"></i>
                            Thông tin Cuộc thi
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl flex-shrink-0">
                                <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 mb-1">Tên cuộc thi</div>
                                <div class="font-semibold text-gray-900">{{ $giaithuong->tencuocthi }}</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-graduation-cap text-gray-400 mt-1"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Năm học</div>
                                    <div class="font-semibold text-gray-900">{{ $giaithuong->namhoc }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-book text-gray-400 mt-1"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Học kỳ</div>
                                    <div class="font-semibold text-gray-900">{{ $giaithuong->hocky }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-tag text-gray-400 mt-1"></i>
                            <div>
                                <div class="text-sm text-gray-500">Loại cuộc thi</div>
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mt-1">
                                    {{ $giaithuong->loaicuocthi }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thông tin người đạt giải --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 px-6 py-4 border-b border-emerald-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-award text-emerald-600"></i>
                            Người đạt giải
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($giaithuong->loaidangky === 'CaNhan')
                            {{-- Cá nhân --}}
                            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full flex-shrink-0">
                                    <i class="fas fa-user text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-lg text-gray-900 mb-1">{{ $nguoidatgiai->hoten }}</div>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div><i class="fas fa-id-card text-blue-500 w-4"></i> MSSV: {{ $nguoidatgiai->masinhvien }}</div>
                                        <div><i class="fas fa-users text-blue-500 w-4"></i> Lớp: {{ $nguoidatgiai->malop }}</div>
                                        @if($nguoidatgiai->email)
                                        <div><i class="fas fa-envelope text-blue-500 w-4"></i> {{ $nguoidatgiai->email }}</div>
                                        @endif
                                        @if($nguoidatgiai->sodienthoai)
                                        <div><i class="fas fa-phone text-blue-500 w-4"></i> {{ $nguoidatgiai->sodienthoai }}</div>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-semibold">
                                    Cá nhân
                                </span>
                            </div>
                        @else
                            {{-- Đội nhóm --}}
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 p-4 bg-purple-50 rounded-xl border border-purple-100">
                                    <div class="flex items-center justify-center w-16 h-16 bg-purple-600 text-white rounded-full flex-shrink-0">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-lg text-gray-900 mb-1">{{ $nguoidatgiai->tendoithi }}</div>
                                        <div class="text-sm text-gray-600">
                                            <i class="fas fa-user-friends text-purple-500"></i>
                                            Số thành viên: {{ $nguoidatgiai->sothanhvien }}
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-purple-600 text-white rounded-full text-xs font-semibold">
                                        Đội nhóm
                                    </span>
                                </div>

                                @if(isset($nguoidatgiai->thanhviens) && $nguoidatgiai->thanhviens->count() > 0)
                                <div class="border border-gray-200 rounded-xl overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                        <h4 class="font-semibold text-gray-700 text-sm">Danh sách thành viên</h4>
                                    </div>
                                    <div class="divide-y divide-gray-100">
                                        @foreach($nguoidatgiai->thanhviens as $tv)
                                        <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $tv->hoten }}</div>
                                                <div class="text-sm text-gray-500">MSSV: {{ $tv->masinhvien }}</div>
                                            </div>
                                            @if($tv->vaitro === 'TruongDoi')
                                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">
                                                <i class="fas fa-crown"></i> Trưởng đội
                                            </span>
                                            @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                                Thành viên
                                            </span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div class="space-y-6">
                {{-- Thông tin giải --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-medal text-amber-600"></i>
                            Chi tiết Giải
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Tên giải</div>
                            <div class="font-bold text-xl text-amber-600">{{ $giaithuong->tengiai }}</div>
                        </div>

                        @if($giaithuong->giaithuong)
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Giải thưởng</div>
                            <div class="text-gray-900 whitespace-pre-line">{{ $giaithuong->giaithuong }}</div>
                        </div>
                        @endif

                        @if($giaithuong->diemrenluyen)
                        <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                            <div class="text-sm text-gray-500 mb-1">Điểm rèn luyện</div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-star text-green-600 text-2xl"></i>
                                <span class="font-bold text-3xl text-green-600">{{ number_format($giaithuong->diemrenluyen, 1) }}</span>
                            </div>
                        </div>
                        @endif

                        <div>
                            <div class="text-sm text-gray-500 mb-1">Ngày trao giải</div>
                            <div class="flex items-center gap-2 text-gray-900">
                                <i class="far fa-calendar-check text-orange-500"></i>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($giaithuong->ngaytrao)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-3">
                    <a href="{{ route('giangvien.giaithuong.edit', $giaithuong->madatgiai) }}" 
                        class="block w-full bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 py-3 rounded-xl font-semibold text-center hover:from-amber-700 hover:to-orange-700 transition shadow-md hover:shadow-lg">
                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                    </a>
                    
                    <form method="POST" action="{{ route('giangvien.giaithuong.destroy', $giaithuong->madatgiai) }}"
                        onsubmit="return confirm('Bạn có chắc muốn xóa giải thưởng này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full bg-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-700 transition shadow-md hover:shadow-lg">
                            <i class="fas fa-trash-alt mr-2"></i>Xóa giải thưởng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection