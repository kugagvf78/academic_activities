@extends('layouts.client')
@section('title', 'Chi tiết Phân công')

@section('content')
{{-- 📄 HERO SECTION: Phong cách Academic Management (Indigo/Blue) - Tinh chỉnh để hiện đại và sắc nét hơn --}}
<section class="relative bg-gradient-to-br from-indigo-900 to-blue-800 text-white pt-20 pb-16 overflow-hidden">
    {{-- Background Effect giữ lại, nhưng làm mờ hơn --}}
    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="container mx-auto px-6 relative z-10">
        {{-- Breadcrumb gọn gàng --}}
        <div class="mb-5 text-sm font-medium text-blue-200">
            <a href="{{ route('giangvien.phancong.index') }}" class="hover:text-white transition">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại Danh sách
            </a>
            <span class="mx-2">/</span>
            <span>Chi tiết Phân công</span>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-3">
            <i class="fas fa-file-alt mr-3 text-cyan-400"></i> Chi tiết Phân công
        </h1>
        <p class="text-indigo-200 text-xl font-light">
            Công việc và vai trò được phân công cho giảng viên.
        </p>
    </div>
    
    {{-- Đường cắt trắng sắc nét, thay đổi góc cắt --}}
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-white" style="clip-path: polygon(0% 40%, 100% 0, 100% 100%, 0% 100%);"></div>
</section>

{{-- 📋 CONTENT SECTION --}}
<section class="container mx-auto px-6 py-16 -mt-16">
    <div class="max-w-6xl mx-auto">
        
        {{-- THÔNG TIN CUỘC THI - Nổi bật và gọn gàng hơn --}}
        @if($phanCong->ban && $phanCong->ban->cuocthi)
        <div class="bg-white rounded-xl shadow-xl border border-purple-300 overflow-hidden mb-12 transform hover:shadow-2xl transition duration-300">
            <div class="bg-purple-600 text-white p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="flex items-center gap-4 mb-3 md:mb-0">
                    <i class="fas fa-trophy text-3xl text-purple-200"></i>
                    <div>
                        <div class="text-sm font-light text-purple-200">Thuộc Cuộc thi</div>
                        <h2 class="text-2xl font-bold">{{ $phanCong->ban->cuocthi->tencuocthi }}</h2>
                    </div>
                </div>
                <div class="text-right">
                    @if($phanCong->ban->cuocthi->ngaybatdau)
                    <div class="text-xs text-purple-200">Ngày bắt đầu</div>
                    <div class="font-bold text-xl">{{ \Carbon\Carbon::parse($phanCong->ban->cuocthi->ngaybatdau)->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
            @if($phanCong->ban->cuocthi->mota)
            <div class="p-4 md:p-6 text-base text-gray-700 border-t border-purple-100 bg-purple-50">
                <i class="fas fa-quote-left text-purple-400 mr-2"></i>
                {{ $phanCong->ban->cuocthi->mota }}
            </div>
            @endif
        </div>
        @endif

        {{-- Main Card - Tăng cường sự chuyên nghiệp --}}
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform hover:shadow-3xl transition duration-300">
            
            {{-- Header Card - Sắc nét hơn --}}
            <div class="bg-gradient-to-r from-indigo-700 to-blue-700 text-white p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center shadow-inner">
                        <i class="fas fa-clipboard-list text-3xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-blue-200 mb-1 font-medium">Mã phân công</div>
                        <div class="text-2xl font-extrabold tracking-wide">{{ $phanCong->maphancong }}</div>
                    </div>
                </div>
                
                {{-- Nút Chỉnh sửa chỉ dành cho Trưởng Bộ Môn --}}
                @if($isTruongBoMon)
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('giangvien.phancong.edit', $phanCong->maphancong) }}" 
                        class="bg-white/30 hover:bg-white/40 backdrop-blur-sm text-white px-5 py-2.5 rounded-xl font-semibold transition inline-flex items-center gap-2 text-base shadow-lg">
                        <i class="fas fa-edit"></i>
                        <span>Chỉnh sửa</span>
                    </a>
                </div>
                @endif
            </div>

            {{-- Body --}}
            <div class="p-6 md:p-10">
                <div class="grid lg:grid-cols-3 gap-8">
                    
                    {{-- LEFT COLUMN: Thông tin chung --}}
                    <div class="lg:col-span-1 space-y-6">
                        <h3 class="text-xl font-bold text-gray-800 pb-3 border-b-4 border-indigo-400/50 flex items-center gap-3">
                            <i class="fas fa-info-circle text-indigo-600"></i>
                            Thông tin Chung
                        </h3>
                        
                        <div class="space-y-5">
                            {{-- Giảng viên --}}
                            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 shadow-sm hover:shadow-md transition duration-300">
                                <div class="text-sm text-gray-600 mb-2 flex items-center gap-2 font-medium">
                                    <i class="fas fa-user-tie text-indigo-600 text-lg"></i>
                                    Giảng viên
                                </div>
                                <div class="text-xl font-extrabold text-gray-900">
                                    {{ $phanCong->giangvien->nguoiDung->hoten ?? 'N/A' }}
                                </div>
                                @if($phanCong->giangvien->chucvu)
                                <div class="text-sm text-indigo-500 mt-1 font-semibold">
                                    {{ $phanCong->giangvien->chucvu }}
                                </div>
                                @endif
                            </div>

                            {{-- Ban --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 shadow-sm hover:shadow-md transition duration-300">
                                <div class="text-sm text-gray-600 mb-2 flex items-center gap-2 font-medium">
                                    <i class="fas fa-users-cog text-blue-600 text-lg"></i>
                                    Ban Công tác
                                </div>
                                <div class="text-xl font-extrabold text-gray-900">
                                    {{ $phanCong->ban->tenban ?? 'N/A' }}
                                </div>
                                @if($phanCong->ban->mota)
                                <div class="text-sm text-gray-600 mt-2 line-clamp-2 italic">
                                    {{ $phanCong->ban->mota }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Chi tiết phân công --}}
                    <div class="lg:col-span-2 space-y-6">
                        <h3 class="text-xl font-bold text-gray-800 pb-3 border-b-4 border-green-400/50 flex items-center gap-3">
                            <i class="fas fa-clipboard-check text-green-600"></i>
                            Chi tiết Công việc
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-5">
                            {{-- Vai trò --}}
                            <div class="border-l-4 border-purple-500 bg-purple-50 rounded-lg p-4 shadow-sm">
                                <div class="text-sm text-gray-600 mb-2 flex items-center gap-2 font-medium">
                                    <i class="fas fa-user-tag text-purple-600"></i>
                                    Vai trò trong Ban
                                </div>
                                <div class="inline-block bg-purple-600 text-white px-4 py-1.5 rounded-full font-bold text-base shadow-md">
                                    {{ $phanCong->vaitro ?? 'N/A' }}
                                </div>
                            </div>
                            
                            {{-- Ngày phân công --}}
                            <div class="border-l-4 border-teal-500 bg-teal-50 rounded-lg p-4 shadow-sm">
                                <div class="text-sm text-gray-600 mb-2 flex items-center gap-2 font-medium">
                                    <i class="far fa-calendar-alt text-teal-600"></i>
                                    Ngày được Phân công
                                </div>
                                <div class="text-xl font-bold text-gray-800">
                                    {{ $phanCong->ngayphancong ? \Carbon\Carbon::parse($phanCong->ngayphancong)->format('d/m/Y') : 'N/A' }}
                                </div>
                            </div>
                            
                            {{-- Công việc chính --}}
                            <div class="md:col-span-2 border-l-4 border-cyan-500 bg-cyan-50 rounded-lg p-5 shadow-md">
                                <div class="text-sm text-gray-600 mb-2 flex items-center gap-2 font-medium">
                                    <i class="fas fa-briefcase text-cyan-600"></i>
                                    Tên Công việc
                                </div>
                                <div class="text-xl font-extrabold text-gray-900 mb-2">
                                    {{ $phanCong->congviec->tencongviec ?? 'N/A' }}
                                </div>
                                @if($phanCong->congviec->mota)
                                <div class="text-base text-gray-700 border-t border-cyan-200 pt-3">
                                    {{ $phanCong->congviec->mota }}
                                </div>
                                @endif
                            </div>
                            
                            {{-- Thời gian công việc & Trạng thái --}}
                            @if($phanCong->congviec)
                            <div class="md:col-span-2 border-l-4 border-amber-500 bg-amber-50 rounded-lg p-5 shadow-md">
                                <div class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-amber-200 flex items-center gap-2">
                                    <i class="fas fa-clock text-amber-600"></i>
                                    Thời gian Thực hiện & Trạng thái
                                </div>
                                <div class="space-y-3 text-base">
                                    @if($phanCong->congviec->thoigianbatdau)
                                    <div class="flex justify-between items-center pb-1">
                                        <span class="text-gray-600 font-medium">Bắt đầu:</span>
                                        <span class="font-bold text-gray-900 bg-white px-3 py-1 rounded-md shadow-inner">
                                            {{ \Carbon\Carbon::parse($phanCong->congviec->thoigianbatdau)->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @endif
                                    @if($phanCong->congviec->thoigianketthuc)
                                    <div class="flex justify-between items-center border-t border-amber-100 pt-2 pb-1">
                                        <span class="text-gray-600 font-medium">Kết thúc (Dự kiến):</span>
                                        <span class="font-bold text-gray-900 bg-white px-3 py-1 rounded-md shadow-inner">
                                            {{ \Carbon\Carbon::parse($phanCong->congviec->thoigianketthuc)->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @endif
                                    @if($phanCong->congviec->trangthai)
                                    <div class="flex justify-between items-center pt-3 border-t-2 border-amber-300">
                                        <span class="text-lg font-bold text-gray-700">Trạng thái:</span>
                                        {{-- Thay đổi Badge để trông chuyên nghiệp hơn --}}
                                        <span class="px-4 py-1.5 rounded-full text-sm font-extrabold shadow-lg
                                            {{ $phanCong->congviec->trangthai == 'Completed' ? 'bg-green-600 text-white' : '' }}
                                            {{ $phanCong->congviec->trangthai == 'In Progress' ? 'bg-blue-600 text-white' : '' }}
                                            {{ $phanCong->congviec->trangthai == 'Pending' ? 'bg-yellow-500 text-white' : '' }}
                                            {{ $phanCong->congviec->trangthai == 'Cancelled' ? 'bg-red-600 text-white' : '' }}
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

            {{-- Footer Actions - Gọn gàng, nổi bật --}}
            <div class="bg-gray-50 px-6 md:px-10 py-6 border-t-2 border-gray-100 flex flex-wrap gap-4 justify-end">
                
                <a href="{{ route('giangvien.phancong.index') }}" 
                    class="bg-white hover:bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-medium transition inline-flex items-center gap-2 border border-gray-300 shadow-sm text-base">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </a>

                @if($isTruongBoMon)
                <a href="{{ route('giangvien.phancong.edit', $phanCong->maphancong) }}" 
                    class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-semibold transition inline-flex items-center gap-2 shadow-lg hover:shadow-xl text-base">
                    <i class="fas fa-edit"></i>
                    <span>Chỉnh sửa</span>
                </a>
                
                <form action="{{ route('giangvien.phancong.destroy', $phanCong->maphancong) }}" 
                    method="POST" 
                    onsubmit="return confirm('Xác nhận xóa phân công này? Hành động này không thể hoàn tác.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transition inline-flex items-center gap-2 shadow-lg hover:shadow-xl text-base">
                        <i class="fas fa-trash-alt"></i>
                        <span>Xóa Phân công</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection