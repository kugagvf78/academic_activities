@extends('layouts.client')
@section('title', 'Chi tiết Phân công')

@section('content')
{{-- 📄 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Quản lý phân công</span>
        </div>
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-file-alt mr-3"></i>Chi tiết Phân công
        </h1>
        <p class="text-blue-100">Thông tin chi tiết về công việc được phân công</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- 📋 CONTENT SECTION --}}
<section class="container mx-auto px-6 py-12">
    <div class="max-w-5xl mx-auto">
        {{-- THÔNG TIN CUỘC THI - THÊM MỚI --}}
        @if($phanCong->ban && $phanCong->ban->cuocthi)
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-xl p-6 mb-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trophy text-3xl"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm text-purple-100 mb-1">Cuộc thi</div>
                    <h2 class="text-2xl font-bold">{{ $phanCong->ban->cuocthi->tencuocthi }}</h2>
                    @if($phanCong->ban->cuocthi->mota)
                    <p class="text-purple-100 text-sm mt-2">{{ $phanCong->ban->cuocthi->mota }}</p>
                    @endif
                </div>
                <div class="text-right">
                    @if($phanCong->ban->cuocthi->ngaybatdau)
                    <div class="text-xs text-purple-100">Bắt đầu</div>
                    <div class="font-semibold">{{ \Carbon\Carbon::parse($phanCong->ban->cuocthi->ngaybatdau)->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white p-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <i class="fas fa-briefcase text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-blue-100 mb-1">Mã phân công</div>
                            <div class="text-2xl font-bold">{{ $phanCong->maphancong }}</div>
                        </div>
                    </div>
                    
                    @if($isTruongBoMon)
                    <div class="flex gap-2">
                        <a href="{{ route('giangvien.phancong.edit', $phanCong->maphancong) }}" 
                            class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            <span>Chỉnh sửa</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-8">
                    {{-- LEFT COLUMN --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-info-circle text-indigo-500"></i>
                                Thông tin chung
                            </h3>
                            
                            <div class="space-y-4">
                                {{-- Giảng viên --}}
                                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-4">
                                    <div class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-user text-indigo-500 mr-1"></i>
                                        Giảng viên
                                    </div>
                                    <div class="text-lg font-bold text-gray-800">
                                        {{ $phanCong->giangvien->nguoiDung->hoten ?? 'N/A' }}
                                    </div>
                                    @if($phanCong->giangvien->chucvu)
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $phanCong->giangvien->chucvu }}
                                    </div>
                                    @endif
                                </div>

                                {{-- Công việc --}}
                                <div class="bg-gradient-to-r from-cyan-50 to-teal-50 rounded-xl p-4">
                                    <div class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-briefcase text-cyan-500 mr-1"></i>
                                        Công việc
                                    </div>
                                    <div class="text-lg font-bold text-gray-800">
                                        {{ $phanCong->congviec->tencongviec ?? 'N/A' }}
                                    </div>
                                    @if($phanCong->congviec->mota)
                                    <div class="text-sm text-gray-600 mt-2">
                                        {{ $phanCong->congviec->mota }}
                                    </div>
                                    @endif
                                </div>

                                {{-- Ban --}}
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4">
                                    <div class="text-sm text-gray-600 mb-1">
                                        <i class="fas fa-users text-blue-500 mr-1"></i>
                                        Ban
                                    </div>
                                    <div class="text-lg font-bold text-gray-800">
                                        {{ $phanCong->ban->tenban ?? 'N/A' }}
                                    </div>
                                    @if($phanCong->ban->mota)
                                    <div class="text-sm text-gray-600 mt-2">
                                        {{ $phanCong->ban->mota }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-clipboard-list text-purple-500"></i>
                                Chi tiết phân công
                            </h3>
                            
                            <div class="space-y-4">
                                {{-- Vai trò --}}
                                <div class="border-l-4 border-purple-500 bg-purple-50 rounded-r-xl p-4">
                                    <div class="text-sm text-gray-600 mb-2">
                                        <i class="fas fa-user-tag text-purple-500 mr-1"></i>
                                        Vai trò
                                    </div>
                                    <div class="inline-block bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold">
                                        {{ $phanCong->vaitro ?? 'N/A' }}
                                    </div>
                                </div>

                                {{-- Ngày phân công --}}
                                <div class="border-l-4 border-teal-500 bg-teal-50 rounded-r-xl p-4">
                                    <div class="text-sm text-gray-600 mb-1">
                                        <i class="far fa-calendar text-teal-500 mr-1"></i>
                                        Ngày phân công
                                    </div>
                                    <div class="text-lg font-bold text-gray-800">
                                        {{ $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </div>

                                {{-- Thông tin công việc --}}
                                @if($phanCong->congviec)
                                <div class="border-l-4 border-amber-500 bg-amber-50 rounded-r-xl p-4">
                                    <div class="text-sm text-gray-600 mb-3">
                                        <i class="fas fa-clock text-amber-500 mr-1"></i>
                                        Thời gian công việc
                                    </div>
                                    <div class="space-y-2">
                                        @if($phanCong->congviec->thoigianbatdau)
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-600">Bắt đầu:</span>
                                            <span class="font-semibold text-gray-800">
                                                {{ \Carbon\Carbon::parse($phanCong->congviec->thoigianbatdau)->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        @endif
                                        @if($phanCong->congviec->thoigianketthuc)
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-gray-600">Kết thúc:</span>
                                            <span class="font-semibold text-gray-800">
                                                {{ \Carbon\Carbon::parse($phanCong->congviec->thoigianketthuc)->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        @endif
                                        @if($phanCong->congviec->trangthai)
                                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-amber-200">
                                            <span class="text-sm text-gray-600">Trạng thái:</span>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                {{ $phanCong->congviec->trangthai == 'Completed' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $phanCong->congviec->trangthai == 'In Progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $phanCong->congviec->trangthai == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            ">
                                                {{ $phanCong->congviec->trangthai }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
                <div class="flex gap-3 justify-end">
                    @if($isTruongBoMon)
                    <a href="{{ route('giangvien.phancong.edit', $phanCong->maphancong) }}" 
                        class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg font-medium transition inline-flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        <span>Chỉnh sửa</span>
                    </a>
                    
                    <form action="{{ route('giangvien.phancong.destroy', $phanCong->maphancong) }}" 
                        method="POST" 
                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition inline-flex items-center gap-2">
                            <i class="fas fa-trash"></i>
                            <span>Xóa</span>
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('giangvien.phancong.index') }}" 
                        class="bg-white hover:bg-gray-100 text-gray-700 px-6 py-2 rounded-lg font-medium transition inline-flex items-center gap-2 border border-gray-300">
                        <i class="fas fa-arrow-left"></i>
                        <span>Quay lại</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection