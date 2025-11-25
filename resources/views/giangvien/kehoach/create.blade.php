@extends('layouts.client')

@section('title', 'Tạo Kế hoạch Cuộc thi')

@section('content')
{{-- HEADER --}}
<section class="relative bg-gradient-to-br from-purple-700 via-purple-600 to-pink-500 text-white py-16">
    <div class="container mx-auto px-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('giangvien.kehoach.index') }}" 
                class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black">Tạo Kế hoạch Cuộc thi</h1>
                <p class="text-purple-100 mt-1">Đề xuất kế hoạch tổ chức cuộc thi mới</p>
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

            <form action="{{ route('giangvien.kehoach.store') }}" method="POST" class="p-8">
                @csrf

                {{-- Cuộc thi --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-trophy text-purple-600 mr-2"></i>
                        Chọn cuộc thi <span class="text-red-500">*</span>
                    </label>
                    <select name="macuocthi" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <option value="">-- Chọn cuộc thi --</option>
                        @foreach($cuocthis as $ct)
                            <option value="{{ $ct->macuocthi }}" {{ old('macuocthi') == $ct->macuocthi ? 'selected' : '' }}>
                                {{ $ct->tencuocthi }} ({{ $ct->loaicuocthi }})
                            </option>
                        @endforeach
                    </select>
                    @if($cuocthis->count() == 0)
                        <p class="mt-2 text-sm text-red-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tất cả cuộc thi đã có kế hoạch. Vui lòng tạo cuộc thi mới trước.
                        </p>
                    @endif
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Chỉ hiển thị các cuộc thi chưa có kế hoạch
                    </p>
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
                               value="{{ old('namhoc', '2024-2025') }}"
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
                            <option value="1" {{ old('hocky') == '1' ? 'selected' : '' }}>Học kỳ 1</option>
                            <option value="2" {{ old('hocky') == '2' ? 'selected' : '' }}>Học kỳ 2</option>
                            <option value="3" {{ old('hocky') == '3' ? 'selected' : '' }}>Học kỳ 3 (Hè)</option>
                        </select>
                    </div>
                </div>

                {{-- Ghi chú --}}
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-purple-600 mr-2"></i>
                        Ghi chú
                    </label>
                    <textarea name="ghichu" 
                              rows="5"
                              placeholder="Nhập ghi chú hoặc lý do đề xuất kế hoạch này..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none">{{ old('ghichu') }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">Không bắt buộc</p>
                </div>

                {{-- Thông tin hướng dẫn --}}
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl p-6 mb-8">
                    <h4 class="font-bold text-blue-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <span>Lưu ý</span>
                    </h4>
                    <ul class="space-y-2 text-blue-700 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Kế hoạch sau khi tạo sẽ ở trạng thái <strong>"Chờ duyệt"</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Bạn có thể chỉnh sửa kế hoạch khi đang chờ duyệt hoặc bị từ chối</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Mỗi cuộc thi chỉ có thể có <strong>một kế hoạch duy nhất</strong></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Trưởng bộ môn sẽ xem xét và phê duyệt kế hoạch của bạn</span>
                        </li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col-reverse md:flex-row gap-4 justify-end pt-6 border-t border-gray-200">
                    <a href="{{ route('giangvien.kehoach.index') }}" 
                        class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition text-center">
                        <i class="fas fa-times mr-2"></i>Hủy bỏ
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105"
                            {{ $cuocthis->count() == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-2"></i>Tạo kế hoạch
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

@endsection