@extends('layouts.client')

@section('title', 'Chi tiết đề thi - ' . $dethi->tendethi)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('giangvien.dethi.index') }}" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <span class="text-white/60">|</span>
                    <span class="text-white/90 text-sm">Chi tiết đề thi</span>
                </div>
                <h1 class="text-4xl font-black mb-2">
                    {{ $dethi->tendethi }}
                </h1>
                <p class="text-cyan-100 flex items-center gap-2">
                    <i class="fas fa-hashtag text-sm"></i>{{ $dethi->madethi }}
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $dethi->sobaithi }}</div>
                        <div class="text-sm text-cyan-100">Bài thi</div>
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
    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-2xl"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Cột trái - Thông tin chi tiết --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Thông tin đề thi --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-5">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-file-alt"></i>
                        Thông tin đề thi
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- Thông tin cuộc thi --}}
                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-200">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-trophy text-yellow-500"></i>
                                Thông tin cuộc thi
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Tên cuộc thi</span>
                                    <p class="font-bold text-gray-800 mt-1">{{ $dethi->tencuocthi }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Loại cuộc thi</span>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                                            {{ $dethi->loaicuocthi }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Thời gian</span>
                                    <div class="mt-1 text-gray-800 font-medium">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar-alt text-blue-500"></i>
                                            {{ \Carbon\Carbon::parse($dethi->thoigianbatdau)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-400 my-1 ml-6">đến</div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar-check text-green-500"></i>
                                            {{ \Carbon\Carbon::parse($dethi->thoigianketthuc)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Chi tiết đề thi --}}
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                Chi tiết đề thi
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Loại đề</span>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @if($dethi->loaidethi == 'LyThuyet') bg-blue-100 text-blue-700
                                            @elseif($dethi->loaidethi == 'ThucHanh') bg-purple-100 text-purple-700
                                            @elseif($dethi->loaidethi == 'VietBao') bg-green-100 text-green-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            @if($dethi->loaidethi == 'LyThuyet') Lý thuyết
                                            @elseif($dethi->loaidethi == 'ThucHanh') Thực hành
                                            @elseif($dethi->loaidethi == 'VietBao') Viết báo
                                            @else Khác
                                            @endif
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Thời gian làm bài</span>
                                    <p class="font-bold text-gray-800 mt-1 flex items-center gap-2">
                                        <i class="fas fa-clock text-blue-500"></i>
                                        {{ $dethi->thoigianlambai }} phút
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Điểm tối đa</span>
                                    <p class="font-bold text-gray-800 mt-1 flex items-center gap-2">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        {{ $dethi->diemtoida }} điểm
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Trạng thái</span>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @if($dethi->status_color == 'green') bg-green-100 text-green-700
                                            @elseif($dethi->status_color == 'yellow') bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ $dethi->status_label }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- File đề thi --}}
                    @if($dethi->filedethi)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-paperclip text-green-500"></i>
                                File đề thi đính kèm
                            </h3>
                            
                            @php
                                $extension = pathinfo($dethi->filedethi, PATHINFO_EXTENSION);
                            @endphp

                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <i class="fas fa-file-pdf text-red-500 text-4xl"></i>
                                        <div>
                                            <p class="font-bold text-gray-800">{{ basename($dethi->filedethi) }}</p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Định dạng: <span class="font-semibold uppercase">{{ $extension }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('giangvien.dethi.download-file', $dethi->madethi) }}" 
                                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center gap-2">
                                        <i class="fas fa-download"></i>
                                        <span>Tải xuống</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Thông tin người tạo --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Người tạo</span>
                                    <p class="font-bold text-gray-800">{{ $dethi->nguoitao_ten }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar text-white"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Ngày tạo</span>
                                    <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($dethi->ngaytao)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danh sách bài thi --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-100 to-cyan-100 px-6 py-5 border-b border-blue-200">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-file-invoice text-blue-600"></i>
                            <span>Danh sách bài thi</span>
                            <span class="px-4 py-1.5 bg-blue-600 text-white rounded-full text-sm font-bold shadow-md">
                                {{ $dethi->sobaithi }} bài
                            </span>
                        </h2>
                        
                        @if($baithiList->count() > 0)
                            <button type="button" 
                                id="downloadSelectedBtn"
                                onclick="downloadSelected()"
                                class="hidden px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl items-center gap-2">
                                <i class="fas fa-download"></i>
                                Tải đã chọn (<span id="selectedCount">0</span>)
                            </button>
                        @endif
                    </div>
                </div>

                @if($baithiList->count() > 0)
                    <form id="downloadForm" action="{{ route('giangvien.dethi.download-multiple', $dethi->madethi) }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left">
                                            <input type="checkbox" 
                                                id="selectAll" 
                                                onchange="toggleSelectAll(this)"
                                                class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sinh viên</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Đội thi</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Thời gian nộp</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Điểm</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($baithiList as $bt)
                                        <tr class="hover:bg-blue-50/50 transition duration-200">
                                            <td class="px-6 py-4">
                                                @if($bt->filebaithi)
                                                    <input type="checkbox" 
                                                        name="baithi_ids[]" 
                                                        value="{{ $bt->mabaithi }}"
                                                        onchange="updateSelectedCount()"
                                                        class="baithi-checkbox w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                                @else
                                                    <span class="text-gray-300" title="Không có file">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($bt->masinhvien)
                                                    <div class="font-bold text-gray-800">{{ $bt->sinhvien_ten }}</div>
                                                    <div class="text-sm text-gray-500 flex items-center gap-1">
                                                        <i class="fas fa-id-badge text-xs"></i>
                                                        {{ $bt->masinhvien }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">--</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($bt->tendoithi)
                                                    <span class="font-semibold text-gray-800">{{ $bt->tendoithi }}</span>
                                                @else
                                                    <span class="text-gray-400 italic">Cá nhân</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($bt->thoigiannop)
                                                    <div class="flex items-center gap-2 text-gray-700">
                                                        <i class="fas fa-clock text-blue-500"></i>
                                                        {{ \Carbon\Carbon::parse($bt->thoigiannop)->format('d/m/Y H:i') }}
                                                    </div>
                                                @else
                                                    <span class="text-yellow-600 font-semibold">Chưa nộp</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($bt->diemso !== null)
                                                    <span class="px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                                        {{ $bt->diemso }} điểm
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 italic">Chưa chấm</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($bt->filebaithi)
                                                    <a href="{{ route('giangvien.dethi.download-baithi', [$dethi->madethi, $bt->mabaithi]) }}" 
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition font-semibold"
                                                        title="Tải file bài thi">
                                                        <i class="fas fa-download"></i>
                                                        <span>Tải</span>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-sm italic">Không có file</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                    {{-- JavaScript cho checkbox --}}
                    <script>
                        function toggleSelectAll(checkbox) {
                            const checkboxes = document.querySelectorAll('.baithi-checkbox');
                            checkboxes.forEach(cb => {
                                cb.checked = checkbox.checked;
                            });
                            updateSelectedCount();
                        }

                        function updateSelectedCount() {
                            const checkedBoxes = document.querySelectorAll('.baithi-checkbox:checked');
                            const count = checkedBoxes.length;
                            const downloadBtn = document.getElementById('downloadSelectedBtn');
                            const countSpan = document.getElementById('selectedCount');
                            
                            countSpan.textContent = count;
                            
                            if (count > 0) {
                                downloadBtn.classList.remove('hidden');
                                downloadBtn.classList.add('flex');
                            } else {
                                downloadBtn.classList.add('hidden');
                                downloadBtn.classList.remove('flex');
                            }

                            // Cập nhật trạng thái checkbox "Chọn tất cả"
                            const allCheckboxes = document.querySelectorAll('.baithi-checkbox');
                            const selectAllCheckbox = document.getElementById('selectAll');
                            selectAllCheckbox.checked = allCheckboxes.length > 0 && 
                                                        allCheckboxes.length === checkedBoxes.length;
                        }

                        function downloadSelected() {
                            const checkedBoxes = document.querySelectorAll('.baithi-checkbox:checked');
                            
                            if (checkedBoxes.length === 0) {
                                alert('Vui lòng chọn ít nhất một bài thi để tải');
                                return;
                            }

                            // Hiển thị loading
                            const btn = document.getElementById('downloadSelectedBtn');
                            const originalHTML = btn.innerHTML;
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo file...';
                            btn.disabled = true;

                            // Submit form
                            document.getElementById('downloadForm').submit();

                            // Reset button sau 3 giây
                            setTimeout(() => {
                                btn.innerHTML = originalHTML;
                                btn.disabled = false;
                            }, 3000);
                        }
                    </script>
                @else
                    <div class="px-6 py-16 text-center">
                        <div class="max-w-sm mx-auto">
                            <i class="fas fa-inbox text-8xl text-gray-300 mb-4"></i>
                            <p class="text-xl font-bold text-gray-700 mb-2">Chưa có bài thi nào</p>
                            <p class="text-gray-500">Sinh viên sẽ nộp bài thi ở đây</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Cột phải - Hành động & Thống kê --}}
        <div class="space-y-6">
            {{-- Thao tác --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-blue-600"></i>
                    Thao tác
                </h2>
                
                <div class="space-y-3">
                    @if($dethi->sobaithi == 0)
                        <a href="{{ route('giangvien.dethi.edit', $dethi->madethi) }}" 
                            class="block w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-center">
                            <i class="fas fa-edit mr-2"></i>
                            Chỉnh sửa
                        </a>
                        
                        <form action="{{ route('giangvien.dethi.destroy', $dethi->madethi) }}" 
                            method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa đề thi này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="w-full px-6 py-3.5 bg-gradient-to-r from-red-600 to-red-600 hover:from-red-700 hover:to-red-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                                <i class="fas fa-trash mr-2"></i>
                                Xóa đề thi
                            </button>
                        </form>
                    @else
                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-300 rounded-xl p-5">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-lock text-yellow-600 text-2xl mt-0.5"></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-bold mb-2">Không thể chỉnh sửa</p>
                                    <p>Đề thi đã có bài nộp nên không thể sửa hoặc xóa.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('giangvien.dethi.index') }}" 
                        class="block w-full px-6 py-3.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại
                    </a>
                </div>
            </div>

            {{-- Thống kê --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 shadow-md">
                <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                    Thống kê
                </h3>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-alt text-white text-lg"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Tổng bài thi</span>
                            </div>
                            <span class="text-2xl font-black text-blue-600">{{ $dethi->sobaithi }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-circle text-white text-lg"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Đã chấm điểm</span>
                            </div>
                            <span class="text-2xl font-black text-green-600">
                                {{ $baithiList->where('diemso', '!=', null)->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-clock text-white text-lg"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Chưa chấm</span>
                            </div>
                            <span class="text-2xl font-black text-yellow-600">
                                {{ $baithiList->where('diemso', null)->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông tin bổ sung --}}
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin thêm
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl">
                        <i class="fas fa-code text-blue-500"></i>
                        <div class="text-sm">
                            <span class="text-gray-500">Mã đề:</span>
                            <span class="font-bold text-gray-800 ml-2">{{ $dethi->madethi }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl">
                        <i class="fas fa-trophy text-yellow-500"></i>
                        <div class="text-sm">
                            <span class="text-gray-500">Mã cuộc thi:</span>
                            <span class="font-bold text-gray-800 ml-2">{{ $dethi->macuocthi }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection