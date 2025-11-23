@extends('layouts.client')

@section('title', 'Chỉnh sửa cuộc thi')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.cuocthi.show', $cuocthi->macuocthi) }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Chỉnh sửa cuộc thi</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    Chỉnh sửa cuộc thi
                </h1>
                <p class="text-cyan-100">{{ $cuocthi->tencuocthi }}</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <i class="fas fa-edit text-4xl mb-2"></i>
                        <div class="text-sm text-cyan-100">Cập nhật</div>
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
    <form method="POST" action="{{ route('giangvien.cuocthi.update', $cuocthi->macuocthi) }}">
        @csrf
        @method('PUT')
        
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Cột trái - Thông tin chính --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Thông tin cơ bản --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Thông tin cơ bản
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        {{-- Tên cuộc thi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tên cuộc thi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tencuocthi" value="{{ old('tencuocthi', $cuocthi->tencuocthi) }}" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('tencuocthi') border-red-500 @enderror"
                                placeholder="VD: Cuộc thi Lập trình 2024">
                            @error('tencuocthi')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Loại cuộc thi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại cuộc thi <span class="text-red-500">*</span>
                            </label>
                            <select name="loaicuocthi" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('loaicuocthi') border-red-500 @enderror">
                                <option value="">-- Chọn loại cuộc thi --</option>
                                <option value="CuocThi" {{ old('loaicuocthi', $cuocthi->loaicuocthi) == 'CuocThi' ? 'selected' : '' }}>Cuộc thi</option>
                                <option value="HoiThao" {{ old('loaicuocthi', $cuocthi->loaicuocthi) == 'HoiThao' ? 'selected' : '' }}>Hội thảo</option>
                            </select>
                            @error('loaicuocthi')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Mô tả --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Mô tả cuộc thi
                            </label>
                            <textarea name="mota" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('mota') border-red-500 @enderror"
                                placeholder="Mô tả chi tiết về cuộc thi...">{{ old('mota', $cuocthi->mota) }}</textarea>
                            @error('mota')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Mục đích --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Mục đích
                            </label>
                            <textarea name="mucdich" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('mucdich') border-red-500 @enderror"
                                placeholder="Mục đích tổ chức cuộc thi...">{{ old('mucdich', $cuocthi->mucdich) }}</textarea>
                            @error('mucdich')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Đối tượng tham gia --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Đối tượng tham gia
                            </label>
                            <input type="text" name="doituongthamgia" value="{{ old('doituongthamgia', $cuocthi->doituongthamgia) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('doituongthamgia') border-red-500 @enderror"
                                placeholder="VD: Sinh viên năm 2, 3, 4">
                            @error('doituongthamgia')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Thời gian & Địa điểm --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i>
                            Thời gian & Địa điểm
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        {{-- Thời gian --}}
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Thời gian bắt đầu <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="thoigianbatdau" 
                                    value="{{ old('thoigianbatdau', \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('Y-m-d\TH:i')) }}"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('thoigianbatdau') border-red-500 @enderror">
                                @error('thoigianbatdau')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Thời gian kết thúc <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="thoigianketthuc" 
                                    value="{{ old('thoigianketthuc', \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('Y-m-d\TH:i')) }}"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('thoigianketthuc') border-red-500 @enderror">
                                @error('thoigianketthuc')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Địa điểm --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Địa điểm
                            </label>
                            <input type="text" name="diadiem" value="{{ old('diadiem', $cuocthi->diadiem) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('diadiem') border-red-500 @enderror"
                                placeholder="VD: Hội trường A, Tòa nhà B">
                            @error('diadiem')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Chi tiết tham gia --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-users"></i>
                            Chi tiết tham gia
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        {{-- Hình thức tham gia --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Hình thức tham gia
                            </label>
                            <select name="hinhthucthamgia"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('hinhthucthamgia') border-red-500 @enderror">
                                <option value="">-- Chọn hình thức --</option>
                                <option value="CaNhan" {{ old('hinhthucthamgia', $cuocthi->hinhthucthamgia) == 'CaNhan' ? 'selected' : '' }}>Cá nhân</option>
                                <option value="DoiNhom" {{ old('hinhthucthamgia', $cuocthi->hinhthucthamgia) == 'DoiNhom' ? 'selected' : '' }}>Đội nhóm</option>
                                <option value="CaHai" {{ old('hinhthucthamgia', $cuocthi->hinhthucthamgia) == 'CaHai' ? 'selected' : '' }}>Cả hai</option>
                            </select>
                            @error('hinhthucthamgia')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Số lượng thành viên --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Số lượng thành viên/đội (nếu có)
                            </label>
                            <input type="number" name="soluongthanhvien" value="{{ old('soluongthanhvien', $cuocthi->soluongthanhvien) }}"
                                min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('soluongthanhvien') border-red-500 @enderror"
                                placeholder="VD: 3">
                            @error('soluongthanhvien')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Dự trù kinh phí --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Dự trù kinh phí (VNĐ)
                            </label>
                            <input type="number" name="dutrukinhphi" value="{{ old('dutrukinhphi', $cuocthi->dutrukinhphi) }}"
                                min="0" step="1000"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition @error('dutrukinhphi') border-red-500 @enderror"
                                placeholder="VD: 50000000">
                            @error('dutrukinhphi')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải - Hành động & Thông tin --}}
            <div class="space-y-6">
                {{-- Hành động --}}
                <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cog text-blue-600"></i>
                        Hành động
                    </h2>
                    
                    <div class="space-y-3">
                        <button type="submit" 
                            class="w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Cập nhật
                        </button>
                        
                        <a href="{{ route('giangvien.cuocthi.show', $cuocthi->macuocthi) }}" 
                            class="block w-full px-6 py-3.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg text-center">
                            <i class="fas fa-times mr-2"></i>
                            Hủy
                        </a>
                    </div>
                </div>

                {{-- Cảnh báo --}}
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-300 rounded-2xl p-6 shadow-md">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mt-0.5"></i>
                        <div>
                            <h3 class="font-bold text-yellow-800 mb-3">Lưu ý</h3>
                            <ul class="space-y-2 text-sm text-yellow-700">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs mt-1.5"></i>
                                    <span>Kiểm tra kỹ thông tin trước khi lưu</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-circle text-xs mt-1.5"></i>
                                    <span>Thay đổi có thể ảnh hưởng đến người đăng ký</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection