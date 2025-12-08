@extends('layouts.client')
@section('title', 'Tạo Ban mới')

@section('content')
{{-- 🎯 HERO SECTION: Phong cách Academic --}}
<section class="relative bg-gradient-to-br from-indigo-900 to-blue-800 text-white pt-20 pb-24 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="container mx-auto px-6 relative z-10">
        {{-- Breadcrumb --}}
        <div class="mb-5 text-sm font-medium text-blue-200 flex items-center gap-2">
            <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" class="hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quản lý Ban
            </a>
            <span class="text-white/40">/</span>
            <span class="text-white">Tạo mới</span>
        </div>

        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">
            <i class="fas fa-plus-circle mr-3 text-cyan-400"></i>
            Thiết lập Ban Tổ chức
        </h1>
        <p class="text-indigo-200 text-lg font-light max-w-2xl">
            Tạo ban chuyên môn mới cho cuộc thi: <strong class="text-white">{{ $cuocThi->tencuocthi }}</strong>
        </p>
    </div>

    {{-- Đường cắt hiện đại --}}
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-white" style="clip-path: polygon(0% 100%, 100% 0, 100% 100%, 0% 100%);"></div>
</section>

{{-- 📝 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-12 -mt-20 relative z-20">
    <div class="max-w-4xl mx-auto">
        
        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden mb-8">
            
            {{-- Header Card --}}
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-5">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-2xl shadow-lg flex items-center justify-center text-white shrink-0">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Thông tin Ban chức năng</h2>
                    <p class="text-sm text-gray-600 mt-1">Vui lòng điền đầy đủ thông tin bên dưới để khởi tạo ban mới.</p>
                </div>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('giangvien.phancong.ban.store') }}" method="POST" class="p-8 space-y-8">
                @csrf
                <input type="hidden" name="macuocthi" value="{{ $cuocThi->macuocthi }}">

                {{-- Group 1: Tên và Loại Ban --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <i class="fas fa-tag text-indigo-500"></i> Định danh
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tên Ban <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400 group-focus-within:text-indigo-500 transition"></i>
                            </div>
                            <select name="tenban" id="tenban" required
                                class="w-full pl-11 pr-10 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm bg-gray-50 hover:bg-white cursor-pointer appearance-none">
                                <option value="">-- Chọn tên ban tiêu chuẩn --</option>
                                @php
                                    $standardNames = [
                                        'Ban Học thuật', 'Ban Tổ chức', 'Ban Chuyên môn', 
                                        'Ban Giám khảo', 'Ban Đề thi', 'Ban Giám khảo Sơ khảo',
                                        'Ban Giám khảo Chung kết', 'Ban Hậu cần'
                                    ];
                                @endphp
                                @foreach($standardNames as $name)
                                    <option value="{{ $name }}" {{ old('tenban') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                                <option value="khac">✨ Khác (Nhập tên tùy chỉnh)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        @error('tenban')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input tùy chỉnh (Animation slide down) --}}
                    <div id="customBanName" class="hidden transition-all duration-300 ease-in-out">
                        <label class="block text-sm font-semibold text-indigo-700 mb-2">
                            Nhập tên ban tùy chỉnh <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-pen text-indigo-400"></i>
                            </div>
                            <input type="text" 
                                name="tenban_custom" 
                                id="tenban_custom"
                                value="{{ old('tenban_custom') }}"
                                placeholder="VD: Ban Truyền thông, Ban Kỹ thuật..."
                                class="w-full pl-11 px-4 py-3 border border-indigo-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-indigo-50/50 shadow-inner">
                        </div>
                        <p class="text-xs text-indigo-500 mt-1 italic">* Tên này sẽ được lưu vào hệ thống cho cuộc thi hiện tại.</p>
                    </div>
                </div>

                {{-- Group 2: Mô tả --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <i class="fas fa-align-left text-blue-500"></i> Thông tin chi tiết
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mô tả nhiệm vụ
                        </label>
                        <textarea name="mota" rows="4"
                            placeholder="Mô tả các trách nhiệm chính, phạm vi công việc của ban này..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm bg-white resize-y">{{ old('mota') }}</textarea>
                        @error('mota')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Context Box --}}
                <div class="bg-blue-50/80 border border-blue-200 rounded-xl p-5 flex items-start gap-4">
                    <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                    <div class="text-sm">
                        <p class="font-bold text-blue-800 mb-1">Đang tạo ban cho cuộc thi:</p>
                        <p class="text-gray-700 font-medium text-base mb-1">{{ $cuocThi->tencuocthi }}</p>
                        @if($cuocThi->thoigianbatdau)
                        <p class="text-gray-500"><i class="far fa-clock mr-1"></i> Bắt đầu: {{ \Carbon\Carbon::parse($cuocThi->thoigianbatdau)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" 
                        class="w-full sm:w-auto px-6 py-3 rounded-xl font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition text-center">
                        <i class="fas fa-times mr-2"></i>Hủy bỏ
                    </a>
                    <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 ml-auto">
                        <i class="fas fa-save mr-2"></i>Hoàn tất tạo Ban
                    </button>
                </div>
            </form>
        </div>

        {{-- Helper Section: Tags Cloud --}}
        <div class="mt-8">
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 text-center">Các tên ban phổ biến</h4>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['Ban Học thuật', 'Ban Tổ chức', 'Ban Chuyên môn', 'Ban Giám khảo', 'Ban Đề thi', 'Ban Hậu cần', 'Ban Truyền thông', 'Ban Kỹ thuật'] as $tag)
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white text-gray-600 border border-gray-200 shadow-sm select-none hover:border-indigo-300 hover:text-indigo-600 transition cursor-default">
                    <i class="fas fa-check-circle text-gray-300 mr-1.5"></i> {{ $tag }}
                </span>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- JAVASCRIPT --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectBan = document.getElementById('tenban');
    const customBanDiv = document.getElementById('customBanName');
    const customBanInput = document.getElementById('tenban_custom');

    function toggleCustomInput() {
        if (selectBan.value === 'khac') {
            customBanDiv.classList.remove('hidden');
            // Animation effect logic could go here if using JS animation libraries
            customBanInput.required = true;
            selectBan.removeAttribute('name');
            customBanInput.setAttribute('name', 'tenban');
            customBanInput.focus();
        } else {
            customBanDiv.classList.add('hidden');
            customBanInput.required = false;
            customBanInput.removeAttribute('name');
            selectBan.setAttribute('name', 'tenban');
        }
    }

    selectBan.addEventListener('change', toggleCustomInput);
    
    // Check on load (for validation errors redirection)
    if(selectBan.value === 'khac') {
        toggleCustomInput();
    }
});
</script>
@endpush

@endsection