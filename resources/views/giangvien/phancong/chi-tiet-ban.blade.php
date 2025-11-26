@extends('layouts.client')
@section('title', 'Chi tiết Ban - ' . $ban->tenban)

@section('content')
{{-- 🎯 HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white py-16 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.phancong.quan-ly-ban') }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">{{ $ban->cuocthi->tencuocthi ?? 'N/A' }}</span>
        </div>
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black mb-2">
                    <i class="fas fa-users mr-3"></i>{{ $ban->tenban }}
                </h1>
                <p class="text-indigo-100">{{ $ban->mota ?? 'Quản lý và phân công giảng viên' }}</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $phanCongList->total() }}</div>
                        <div class="text-sm text-indigo-100">Giảng viên</div>
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

{{-- ➕ FORM PHÂN CÔNG NHANH --}}
@if($isTruongBoMon && $giangVienChuaPhanCong->count() > 0)
<section class="container mx-auto px-6 -mt-8 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-indigo-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-indigo-600 mr-2"></i>
                Phân công nhanh
            </h3>
            <button onclick="toggleForm()" class="text-indigo-600 hover:text-indigo-700 font-medium">
                <i class="fas fa-chevron-down" id="toggle-icon"></i>
            </button>
        </div>

        <form id="quick-assign-form" action="{{ route('giangvien.phancong.phan-cong-nhieu') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="maban" value="{{ $ban->maban }}">
            
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Chọn giảng viên --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Chọn giảng viên <span class="text-red-500">*</span>
                    </label>
                    <div class="grid md:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-4 bg-gray-50 rounded-xl border border-gray-200">
                        @foreach($giangVienChuaPhanCong as $gv)
                        <label class="flex items-center gap-3 p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-400 cursor-pointer transition">
                            <input type="checkbox" name="giangvien_list[]" value="{{ $gv->magiangvien }}" 
                                class="w-5 h-5 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 text-sm">{{ $gv->nguoiDung->hoten ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $gv->chucvu ?? 'N/A' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <div class="mt-2 flex items-center gap-3">
                        <button type="button" onclick="selectAllGV()" 
                            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            <i class="fas fa-check-square mr-1"></i>Chọn tất cả
                        </button>
                        <button type="button" onclick="deselectAllGV()" 
                            class="text-sm text-gray-600 hover:text-gray-700 font-medium">
                            <i class="far fa-square mr-1"></i>Bỏ chọn tất cả
                        </button>
                    </div>
                </div>

                {{-- Công việc --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Công việc <span class="text-red-500">*</span>
                    </label>
                    <select name="macongviec" required id="congviec-select"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">-- Đang tải... --</option>
                    </select>
                </div>

                {{-- Vai trò --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Vai trò <span class="text-red-500">*</span>
                    </label>
                    <select name="vaitro" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">-- Chọn vai trò --</option>
                        <option value="Trưởng ban">Trưởng ban</option>
                        <option value="Phó ban">Phó ban</option>
                        <option value="Ủy viên">Ủy viên</option>
                        <option value="Thư ký">Thư ký</option>
                        <option value="Thành viên">Thành viên</option>
                    </select>
                </div>

                {{-- Ngày phân công --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ngày phân công
                    </label>
                    <input type="date" name="ngayphancong" value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div class="mt-6 flex gap-3 justify-end border-t pt-4">
                <button type="button" onclick="toggleForm()"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </button>
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-500 text-white rounded-xl font-semibold shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-purple-600 transition">
                    <i class="fas fa-user-plus mr-2"></i>Phân công đã chọn
                </button>
            </div>
        </form>
    </div>
</section>
@endif

{{-- 📋 DANH SÁCH GIẢNG VIÊN ĐÃ PHÂN CÔNG --}}
<section class="container mx-auto px-6 py-12">
    @if($phanCongList->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-500 text-white p-6">
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-list mr-2"></i>
                    Danh sách giảng viên ({{ $phanCongList->total() }})
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">STT</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Giảng viên</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vai trò</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Công việc</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày PC</th>
                            @if($isTruongBoMon)
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($phanCongList as $index => $pc)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ ($phanCongList->currentPage() - 1) * $phanCongList->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($pc->giangvien->nguoiDung->hoten ?? 'N', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800">{{ $pc->giangvien->nguoiDung->hoten ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $pc->giangvien->magiangvien }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if(str_contains($pc->vaitro, 'Trưởng')) bg-purple-100 text-purple-700
                                    @elseif(str_contains($pc->vaitro, 'Phó')) bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $pc->vaitro }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pc->congviec->tencongviec ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pc->ngayphancong ? \Carbon\Carbon::parse($pc->ngayphancong)->format('d/m/Y') : 'N/A' }}
                            </td>
                            @if($isTruongBoMon)
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('giangvien.phancong.show', $pc->maphancong) }}"
                                        class="text-indigo-600 hover:text-indigo-800 transition" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('giangvien.phancong.edit', $pc->maphancong) }}"
                                        class="text-amber-600 hover:text-amber-800 transition" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete('{{ $pc->maphancong }}')"
                                        class="text-red-600 hover:text-red-800 transition" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t">
                {{ $phanCongList->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <i class="fas fa-user-friends text-8xl text-gray-300 mb-6"></i>
            <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có giảng viên nào</h4>
            <p class="text-gray-500 mb-8">Ban này chưa có giảng viên được phân công.</p>
            @if($isTruongBoMon)
            <button onclick="toggleForm()"
                class="inline-block bg-gradient-to-r from-indigo-600 to-purple-500 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition">
                <i class="fas fa-user-plus mr-2"></i>Phân công giảng viên
            </button>
            @endif
        </div>
    @endif
</section>

{{-- Form xóa ẩn --}}
@if($isTruongBoMon)
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif

@push('scripts')
<script>
// Toggle form phân công
function toggleForm() {
    const form = document.getElementById('quick-assign-form');
    const icon = document.getElementById('toggle-icon');
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        form.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

// Select/Deselect all
function selectAllGV() {
    document.querySelectorAll('input[name="giangvien_list[]"]').forEach(cb => cb.checked = true);
}

function deselectAllGV() {
    document.querySelectorAll('input[name="giangvien_list[]"]').forEach(cb => cb.checked = false);
}

// Confirm delete
function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa phân công này?')) {
        const form = document.getElementById('delete-form');
        form.action = `/giang-vien/phan-cong/${id}`;
        form.submit();
    }
}

// Load công việc
document.addEventListener('DOMContentLoaded', async function() {
    const congviecSelect = document.getElementById('congviec-select');
    const macuocthi = '{{ $ban->macuocthi }}';
    
    try {
        const response = await fetch(`/giang-vien/phan-cong/api/congviec/${macuocthi}`);
        const cvList = await response.json();
        
        congviecSelect.innerHTML = '<option value="">-- Chọn công việc --</option>';
        cvList.forEach(cv => {
            const option = document.createElement('option');
            option.value = cv.macongviec;
            option.textContent = cv.tencongviec;
            congviecSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error:', error);
        congviecSelect.innerHTML = '<option value="">-- Lỗi khi tải dữ liệu --</option>';
    }
});
</script>
@endpush

@endsection