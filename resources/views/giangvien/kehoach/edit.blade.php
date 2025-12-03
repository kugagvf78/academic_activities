@extends('layouts.client')

@section('title', 'Chỉnh sửa Kế hoạch - ' . $kehoach->makehoach)

@section('content')
{{-- HEADER --}}
<section class="relative bg-gradient-to-br from-purple-700 via-purple-600 to-pink-500 text-white py-16">
    <div class="container mx-auto px-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('giangvien.kehoach.show', $kehoach->makehoach) }}" 
                class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black">Chỉnh sửa Kế hoạch</h1>
                <p class="text-purple-100 mt-1">Mã: <span class="font-mono">{{ $kehoach->makehoach }}</span></p>
            </div>
        </div>
    </div>
</section>

{{-- FORM --}}
<section class="container mx-auto px-6 -mt-8 pb-12 relative z-10">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            
            {{-- Error Messages --}}
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-6 m-6 rounded-xl">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="font-bold text-red-800 mb-2">Có lỗi xảy ra:</h4>
                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-6 m-6 rounded-xl">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        <span class="text-red-700 font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('giangvien.kehoach.update', $kehoach->makehoach) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                {{-- Thông tin trạng thái --}}
                @if($kehoach->trangthaiduyet == 'Rejected')
                <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl p-6 mb-8">
                    <h4 class="font-bold text-red-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <span>Lưu ý quan trọng</span>
                    </h4>
                    <ul class="space-y-2 text-red-700 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle mt-0.5"></i>
                            <span>Kế hoạch này đã bị <strong>từ chối</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Sau khi cập nhật, trạng thái sẽ chuyển về <strong>"Chờ duyệt"</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Vui lòng xem xét kỹ và điều chỉnh thông tin trước khi cập nhật</span>
                        </li>
                    </ul>
                </div>
                @endif

                {{-- SECTION 1: Thông tin cơ bản --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3">
                        <i class="fas fa-info-circle text-purple-600"></i>
                        <span>Thông tin cơ bản</span>
                    </h3>

                    {{-- Tên cuộc thi --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-trophy text-purple-600 mr-2"></i>
                            Tên cuộc thi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="tencuocthi" 
                               value="{{ old('tencuocthi', $kehoach->tencuocthi) }}"
                               required
                               placeholder="Nhập tên cuộc thi"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    </div>

                    {{-- Loại cuộc thi --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tag text-purple-600 mr-2"></i>
                            Loại cuộc thi <span class="text-red-500">*</span>
                        </label>
                        <select name="loaicuocthi" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                            
                            <option value="">-- Chọn loại cuộc thi --</option>
                            
                            <option value="CuocThi" 
                                    {{ old('loaicuocthi', $kehoach->loaicuocthi ?? '') == 'CuocThi' ? 'selected' : '' }}>
                                Cuộc thi
                            </option>
                            
                            <option value="HoiThao" 
                                    {{ old('loaicuocthi', $kehoach->loaicuocthi ?? '') == 'HoiThao' ? 'selected' : '' }}>
                                Hội thảo
                            </option>
                            
                        </select>
                    </div>

                    {{-- Năm học và Học kỳ --}}
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        {{-- Năm học --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                                Năm học <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="namhoc" 
                                   value="{{ old('namhoc', $kehoach->namhoc) }}"
                                   required
                                   placeholder="VD: 2024-2025"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                            <p class="mt-2 text-sm text-gray-500">Định dạng: YYYY-YYYY</p>
                        </div>

                        {{-- Học kỳ --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-book text-purple-600 mr-2"></i>
                                Học kỳ <span class="text-red-500">*</span>
                            </label>
                            <select name="hocky" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                                <option value="">-- Chọn học kỳ --</option>
                                <option value="1" {{ old('hocky', $kehoach->hocky) == '1' ? 'selected' : '' }}>Học kỳ 1</option>
                                <option value="2" {{ old('hocky', $kehoach->hocky) == '2' ? 'selected' : '' }}>Học kỳ 2</option>
                                <option value="3" {{ old('hocky', $kehoach->hocky) == '3' ? 'selected' : '' }}>Học kỳ 3 (Hè)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: Mô tả chi tiết --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3">
                        <i class="fas fa-file-alt text-purple-600"></i>
                        <span>Mô tả chi tiết</span>
                    </h3>

                    {{-- Mô tả --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-purple-600 mr-2"></i>
                            Mô tả cuộc thi
                        </label>
                        <textarea name="mota" 
                                  rows="4"
                                  placeholder="Mô tả chi tiết về cuộc thi..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none">{{ old('mota', $kehoach->mota) }}</textarea>
                    </div>

                    {{-- Mục đích --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-bullseye text-purple-600 mr-2"></i>
                            Mục đích
                        </label>
                        <textarea name="mucdich" 
                                  rows="3"
                                  placeholder="Mục đích tổ chức cuộc thi..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none">{{ old('mucdich', $kehoach->mucdich) }}</textarea>
                    </div>

                    {{-- Đối tượng tham gia --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-users text-purple-600 mr-2"></i>
                            Đối tượng tham gia
                        </label>
                        <input type="text" 
                               name="doituongthamgia" 
                               value="{{ old('doituongthamgia', $kehoach->doituongthamgia) }}"
                               placeholder="VD: Sinh viên năm 2, 3"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    </div>
                </div>

                {{-- SECTION 3: Thời gian và địa điểm --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3">
                        <i class="fas fa-clock text-purple-600"></i>
                        <span>Thời gian và địa điểm</span>
                    </h3>

                    {{-- Thời gian --}}
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-calendar-check text-purple-600 mr-2"></i>
                                Thời gian bắt đầu <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="thoigianbatdau" 
                                   value="{{ old('thoigianbatdau', date('Y-m-d\TH:i', strtotime($kehoach->thoigianbatdau))) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-calendar-times text-purple-600 mr-2"></i>
                                Thời gian kết thúc <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="thoigianketthuc" 
                                   value="{{ old('thoigianketthuc', date('Y-m-d\TH:i', strtotime($kehoach->thoigianketthuc))) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        </div>
                    </div>

                    {{-- Địa điểm --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                            Địa điểm
                        </label>
                        <input type="text" 
                               name="diadiem" 
                               value="{{ old('diadiem', $kehoach->diadiem) }}"
                               placeholder="VD: Hội trường A, Phòng 301"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    </div>
                </div>

                {{-- SECTION 4: Quy mô và tài chính --}}
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                        <span>Quy mô và tài chính</span>
                    </h3>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        {{-- Số lượng thành viên --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user-friends text-purple-600 mr-2"></i>
                                Số lượng thành viên/đội
                            </label>
                            <input type="number" 
                                   name="soluongthanhvien" 
                                   value="{{ old('soluongthanhvien', $kehoach->soluongthanhvien) }}"
                                   min="1"
                                   placeholder="VD: 5"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        </div>

                        {{-- Hình thức tham gia --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-handshake text-purple-600 mr-2"></i>
                                Hình thức tham gia
                            </label>
                            <select name="hinhthucthamgia" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                                
                                <option value="">-- Chọn hình thức tham gia --</option>
                                
                                <option value="CaNhan" 
                                        {{ old('hinhthucthamgia', $kehoach->hinhthucthamgia ?? '') == 'CaNhan' ? 'selected' : '' }}>
                                    Cá nhân
                                </option>
                                
                                <option value="DoiNhom" 
                                        {{ old('hinhthucthamgia', $kehoach->hinhthucthamgia ?? '') == 'DoiNhom' ? 'selected' : '' }}>
                                    Nhóm
                                </option>
                                
                                <option value="CaHai" 
                                        {{ old('hinhthucthamgia', $kehoach->hinhthucthamgia ?? '') == 'CaHai' ? 'selected' : '' }}>
                                    Cả hai
                                </option>
                                
                            </select>
                        </div>
                    </div>

                    {{-- Dự trù kinh phí --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign text-purple-600 mr-2"></i>
                            Dự trù kinh phí (VNĐ)
                        </label>
                        <input type="number" 
                               name="dutrukinhphi" 
                               value="{{ old('dutrukinhphi', $kehoach->dutrukinhphi) }}"
                               min="0"
                               step="1000"
                               placeholder="VD: 5000000"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <p class="mt-2 text-sm text-gray-500">Để trống nếu chưa xác định</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col-reverse md:flex-row gap-4 justify-end pt-6 border-t border-gray-200">
                    <a href="{{ route('giangvien.kehoach.show', $kehoach->makehoach) }}" 
                        class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition text-center">
                        <i class="fas fa-times mr-2"></i>Hủy bỏ
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Lưu thay đổi
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

@endsection