@extends('layouts.client')
@section('title', 'Chi tiết Phân công')

@section('content')
{{-- 📝 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-blue-600 to-cyan-500 text-white py-12 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <h1 class="text-3xl font-black">
            <i class="fas fa-clipboard-list mr-3"></i>Chi tiết Phân công
        </h1>
    </div>
</section>

{{-- 📄 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-12">
    <div class="grid lg:grid-cols-3 gap-8">
        {{-- LEFT: Thông tin phân công --}}
        <div class="lg:col-span-2">
            {{-- Thông tin chính --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-blue-500 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-info-circle mr-2"></i>Thông tin Phân công
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600 mb-1 block">Công việc</label>
                        <p class="text-lg font-bold text-gray-800">{{ $phanCong->congviec->tencongviec ?? 'N/A' }}</p>
                    </div>

                    @if($phanCong->congviec && $phanCong->congviec->mota)
                    <div>
                        <label class="text-sm font-semibold text-gray-600 mb-1 block">Mô tả công việc</label>
                        <p class="text-gray-700 leading-relaxed">{{ $phanCong->congviec->mota }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm font-semibold text-gray-600 mb-1 block">Vai trò được giao</label>
                        <div class="inline-block bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-semibold">
                            {{ $phanCong->vaitro ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 mb-1 block">Ban</label>
                            <p class="text-gray-800 font-medium">{{ $phanCong->ban->tenban ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 mb-1 block">Ngày phân công</label>
                            <p class="text-gray-800">
                                <i class="far fa-calendar text-indigo-500 mr-2"></i>
                                {{ $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Thông tin giảng viên --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Thông tin giảng viên --}}
            @if($phanCong->giangvien)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-500 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">
                        <i class="fas fa-user-tie mr-2"></i>Giảng viên phụ trách
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($phanCong->giangvien->nguoiDung->hoten ?? 'GV', 0, 2) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">{{ $phanCong->giangvien->nguoiDung->hoten ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $phanCong->giangvien->chucvu ?? 'Giảng viên' }}</div>
                        </div>
                    </div>

                    @if($phanCong->giangvien->nguoiDung->email)
                    <div class="pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600 mb-1">Email</div>
                        <a href="mailto:{{ $phanCong->giangvien->nguoiDung->email }}" 
                            class="text-blue-600 hover:text-blue-700 font-medium">
                            {{ $phanCong->giangvien->nguoiDung->email }}
                        </a>
                    </div>
                    @endif

                    @if($phanCong->giangvien->nguoiDung->sodienthoai)
                    <div class="pt-4 border-t border-gray-200 mt-4">
                        <div class="text-sm text-gray-600 mb-1">Điện thoại</div>
                        <a href="tel:{{ $phanCong->giangvien->nguoiDung->sodienthoai }}" 
                            class="text-blue-600 hover:text-blue-700 font-medium">
                            {{ $phanCong->giangvien->nguoiDung->sodienthoai }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Quick actions --}}
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-6 border border-indigo-100">
                <h4 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-bolt mr-2"></i>Thao tác nhanh
                </h4>
                <div class="space-y-2">
                    <a href="{{ route('giangvien.phancong.index') }}" 
                        class="block w-full bg-white hover:bg-gray-50 text-gray-700 px-4 py-3 rounded-lg font-medium transition border border-gray-200 text-center">
                        <i class="fas fa-list mr-2"></i>Danh sách phân công
                    </a>
                    <a href="{{ route('giangvien.profile.index') }}" 
                        class="block w-full bg-white hover:bg-gray-50 text-gray-700 px-4 py-3 rounded-lg font-medium transition border border-gray-200 text-center">
                        <i class="fas fa-home mr-2"></i>Trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection