@extends('layouts.client')
@section('title', 'Chỉnh sửa Giải thưởng')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-600 via-orange-600 to-red-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.show', $giaithuong->madatgiai) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Chỉnh sửa giải thưởng</span>
        </div>
        <h1 class="text-3xl font-black flex items-center gap-3">
            <i class="fas fa-edit"></i>
            Chỉnh sửa Giải thưởng
        </h1>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 70C120 60 240 40 360 35C480 30 600 40 720 45C840 50 960 50 1080 45C1200 40 1320 30 1380 25L1440 20V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- FORM --}}
<section class="container mx-auto px-6 py-12 -mt-8 relative z-10">
    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('giangvien.giaithuong.update', $giaithuong->madatgiai) }}" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-8 py-5 border-b border-orange-100">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-trophy text-orange-600"></i>
                    Thông tin Giải thưởng
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Chỉ có thể chỉnh sửa một số thông tin cơ bản
                </p>
            </div>

            <div class="p-8 space-y-6">
                {{-- Thông tin không thể sửa --}}
                <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-start gap-3 mb-3">
                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-700 mb-1">Thông tin cố định</h4>
                            <p class="text-sm text-gray-600">Các thông tin sau không thể thay đổi</p>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Loại đăng ký</div>
                            <div class="font-semibold text-gray-800">
                                @if($giaithuong->loaidangky === 'CaNhan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                    <i class="fas fa-user"></i> Cá nhân
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                                    <i class="fas fa-users"></i> Đội nhóm
                                </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Mã giải thưởng</div>
                            <div class="font-mono text-sm font-semibold text-gray-800">{{ $giaithuong->madatgiai }}</div>
                        </div>
                    </div>
                </div>

                {{-- Tên giải --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tên giải <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tengiai" value="{{ old('tengiai', $giaithuong->tengiai) }}" required
                        placeholder="VD: Giải Nhất, Giải Xuất sắc..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('tengiai') border-red-500 @enderror">
                    @error('tengiai')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Giải thưởng --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Giải thưởng
                    </label>
                    <textarea name="giaithuong" rows="4"
                        placeholder="Mô tả chi tiết về giải thưởng (tiền mặt, quà tặng, chứng chỉ...)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('giaithuong') border-red-500 @enderror">{{ old('giaithuong', $giaithuong->giaithuong) }}</textarea>
                    @error('giaithuong')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Điểm rèn luyện --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Điểm rèn luyện
                    </label>
                    <div class="relative">
                        <input type="number" name="diemrenluyen" value="{{ old('diemrenluyen', $giaithuong->diemrenluyen) }}" 
                            step="0.1" min="0" max="100"
                            placeholder="VD: 10"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('diemrenluyen') border-red-500 @enderror">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    @error('diemrenluyen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> Điểm rèn luyện thêm cho sinh viên đạt giải
                    </p>
                </div>

                {{-- Ngày trao giải --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ngày trao giải <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="ngaytrao" value="{{ old('ngaytrao', \Carbon\Carbon::parse($giaithuong->ngaytrao)->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('ngaytrao') border-red-500 @enderror">
                    @error('ngaytrao')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thông báo --}}
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-amber-800 text-sm mb-1">Lưu ý</h4>
                            <p class="text-sm text-amber-700">
                                Không thể thay đổi cuộc thi và người đạt giải sau khi đã tạo. 
                                Nếu cần thay đổi, vui lòng xóa và tạo giải thưởng mới.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
                <a href="{{ route('giangvien.giaithuong.show', $giaithuong->madatgiai) }}" 
                    class="px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold border border-gray-300 hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
                <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-orange-600 to-red-500 text-white rounded-xl font-bold hover:from-orange-700 hover:to-red-600 transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</section>

@endsection