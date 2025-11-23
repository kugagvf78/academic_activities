@extends('layouts.client')
@section('title', 'Chấm điểm - ' . ($cuocthi->tencuocthi ?? 'Cuộc thi'))

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white py-16 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.chamdiem.index') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <h1 class="text-3xl font-black mb-2">{{ $cuocthi->tencuocthi }}</h1>
        <p class="text-cyan-100">
            <i class="far fa-calendar mr-2"></i>
            {{ \Carbon\Carbon::parse($cuocthi->thoigianbatdau)->format('d/m/Y') }}
            -
            {{ \Carbon\Carbon::parse($cuocthi->thoigianketthuc)->format('d/m/Y') }}
        </p>
    </div>
</section>

{{-- ACTIONS BAR --}}
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- Thống kê --}}
            <div class="flex items-center gap-6">
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $baithiList->total() }}</div>
                    <div class="text-sm text-gray-500">Tổng bài thi</div>
                </div>
                <div class="w-px h-12 bg-gray-200"></div>
                <div>
                    @php
                        $daCham = DB::table('baithi as bt')
                            ->join('dethi as dt', 'bt.madethi', '=', 'dt.madethi')
                            ->join('ketquathi as kq', 'bt.mabaithi', '=', 'kq.mabaithi')
                            ->where('dt.macuocthi', $cuocthi->macuocthi)
                            ->whereNotNull('kq.diem')
                            ->count();
                    @endphp
                    <div class="text-2xl font-bold text-green-600">{{ $daCham }}</div>
                    <div class="text-sm text-gray-500">Đã chấm</div>
                </div>
                <div class="w-px h-12 bg-gray-200"></div>
                <div>
                    @php
                        $chuaCham = $baithiList->total() - $daCham;
                    @endphp
                    <div class="text-2xl font-bold text-orange-600">{{ $chuaCham }}</div>
                    <div class="text-sm text-gray-500">Chưa chấm</div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                {{-- Export template --}}
                <a href="{{ route('giangvien.chamdiem.export-template', $cuocthi->macuocthi) }}" 
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-download"></i>
                    <span>Tải file mẫu</span>
                </a>

                {{-- Import button --}}
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-5 py-2.5 rounded-xl font-semibold transition inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-file-import"></i>
                    <span>Import điểm</span>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- DANH SÁCH BÀI THI --}}
<section class="container mx-auto px-6 py-12">
    @if($baithiList->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
                <div class="grid lg:grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                    <div class="lg:col-span-1">STT</div>
                    <div class="lg:col-span-2">Mã bài thi</div>
                    <div class="lg:col-span-2">Thí sinh</div>
                    <div class="lg:col-span-2">Đề thi</div>
                    <div class="lg:col-span-1">Điểm</div>
                    <div class="lg:col-span-1">Hạng</div>
                    <div class="lg:col-span-2">Giải thưởng</div>
                    <div class="lg:col-span-1 text-center">Trạng thái</div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($baithiList as $index => $baithi)
                @php
                    $tenThiSinh = 'N/A';
                    $maThiSinh = 'N/A';
                    $loaiDangKy = $baithi->loaidangky ?? 'CaNhan';
                    
                    if ($loaiDangKy == 'CaNhan') {
                        $tenThiSinh = $baithi->sinhvien_ten ?? 'N/A';
                        $maThiSinh = $baithi->masinhvien ?? 'N/A';
                    } else {
                        $tenThiSinh = $baithi->tendoithi ?? 'N/A';
                        $maThiSinh = $baithi->madoithi ?? 'N/A';
                    }
                @endphp
                
                <div class="px-6 py-4 hover:bg-blue-50/50 transition">
                    <div class="grid lg:grid-cols-12 gap-4 items-center">
                        <div class="lg:col-span-1">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full font-semibold text-sm">
                                {{ $baithiList->firstItem() + $index }}
                            </span>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="font-mono text-sm text-gray-600">{{ $baithi->mabaithi }}</div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                    @if($loaiDangKy == 'CaNhan')
                                        <i class="fas fa-user text-xs"></i>
                                    @else
                                        <i class="fas fa-users text-xs"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">{{ $tenThiSinh }}</div>
                                    <div class="text-xs text-gray-500">{{ $maThiSinh }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="text-gray-700 text-sm line-clamp-1">{{ $baithi->tendethi ?? 'N/A' }}</div>
                        </div>

                        <div class="lg:col-span-1">
                            @if($baithi->diem !== null)
                                <div class="flex items-center gap-1">
                                    <span class="text-xl font-bold text-green-600">{{ $baithi->diem }}</span>
                                    <span class="text-gray-500 text-sm">/10</span>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-sm">Chưa chấm</span>
                            @endif
                        </div>

                        <div class="lg:col-span-1">
                            @if($baithi->xephang)
                                @php
                                    $rankColor = match(true) {
                                        $baithi->xephang == 1 => 'text-yellow-600',
                                        $baithi->xephang == 2 => 'text-gray-500',
                                        $baithi->xephang == 3 => 'text-orange-600',
                                        default => 'text-gray-700'
                                    };
                                    $rankIcon = match(true) {
                                        $baithi->xephang == 1 => 'fa-trophy',
                                        $baithi->xephang == 2 => 'fa-medal',
                                        $baithi->xephang == 3 => 'fa-medal',
                                        default => 'fa-ranking-star'
                                    };
                                @endphp
                                <div class="flex items-center gap-1 {{ $rankColor }}">
                                    <i class="fas {{ $rankIcon }}"></i>
                                    <span class="font-bold">{{ $baithi->xephang }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </div>

                        <div class="lg:col-span-2">
                            @if($baithi->giaithuong)
                                @php
                                    $awardConfig = [
                                        'Giải Nhất' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-trophy'],
                                        'Giải Nhì' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-medal'],
                                        'Giải Ba' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'fa-medal'],
                                        'Giải Khuyến Khích' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-award'],
                                    ];
                                    $award = $awardConfig[$baithi->giaithuong] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-award'];
                                @endphp
                                <span class="inline-flex items-center gap-1 {{ $award['bg'] }} {{ $award['text'] }} px-2 py-1 rounded-lg text-xs font-medium">
                                    <i class="fas {{ $award['icon'] }}"></i>
                                    <span>{{ $baithi->giaithuong }}</span>
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </div>

                        <div class="lg:col-span-1 text-center">
                            @if($baithi->diem !== null)
                                <span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @else
                                <span class="inline-block bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            {{ $baithiList->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-inbox text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có bài thi nào</h4>
                <p class="text-gray-500">Cuộc thi này chưa có bài thi nào được nộp.</p>
            </div>
        </div>
    @endif
</section>

{{-- IMPORT MODAL --}}
<div id="importModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-xl">
                <i class="fas fa-file-import mr-2"></i>Import điểm từ Excel
            </h3>
            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                class="text-white/80 hover:text-white transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <form action="{{ route('giangvien.chamdiem.import', $cuocthi->macuocthi) }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                {{-- Hướng dẫn --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <h4 class="font-bold text-blue-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Hướng dẫn import
                    </h4>
                    <ol class="space-y-2 text-sm text-gray-700 list-decimal list-inside">
                        <li>Nhấn nút <strong>"Tải file mẫu"</strong> để tải file Excel mẫu</li>
                        <li>Mở file Excel và điền điểm vào cột <strong>"Điểm (0-10)"</strong></li>
                        <li>Có thể thêm nhận xét vào cột <strong>"Nhận xét"</strong> (tùy chọn)</li>
                        <li><strong class="text-red-600">KHÔNG sửa</strong> các cột: STT, Mã bài thi, Mã SV/Đội, Tên thí sinh/Đội</li>
                        <li>Lưu file và tải lên ở dưới</li>
                    </ol>
                </div>

                {{-- File upload --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Chọn file Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-500 transition">
                        <input type="file" name="file" id="fileInput" accept=".xlsx,.xls" required
                            class="hidden" onchange="updateFileName(this)">
                        <label for="fileInput" class="cursor-pointer">
                            <div class="mb-3">
                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400"></i>
                            </div>
                            <div class="text-gray-600 mb-2">
                                <span class="text-blue-600 font-semibold">Nhấp để chọn file</span> hoặc kéo thả vào đây
                            </div>
                            <div class="text-sm text-gray-500">Chỉ hỗ trợ file Excel (.xlsx, .xls), tối đa 5MB</div>
                        </label>
                        <div id="fileName" class="mt-4 text-sm font-medium text-blue-600 hidden"></div>
                    </div>
                    @error('file')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Warning --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                        <div class="text-sm text-gray-700">
                            <strong class="text-yellow-800">Lưu ý:</strong> 
                            Nếu bài thi đã có điểm, điểm cũ sẽ bị ghi đè bởi điểm mới trong file Excel.
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition inline-flex items-center justify-center gap-2">
                        <i class="fas fa-upload"></i>
                        <span>Bắt đầu import</span>
                    </button>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateFileName(input) {
    const fileNameDiv = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileNameDiv.textContent = '📁 ' + input.files[0].name;
        fileNameDiv.classList.remove('hidden');
    } else {
        fileNameDiv.classList.add('hidden');
    }
}

// Xác nhận trước khi import
document.getElementById('importForm').addEventListener('submit', function(e) {
    if (!confirm('Xác nhận import điểm từ file Excel? Điểm cũ sẽ bị ghi đè nếu trùng.')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush

@endsection