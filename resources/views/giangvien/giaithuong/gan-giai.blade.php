@extends('layouts.client')
@section('title', 'Gán giải thưởng - ' . $cocau->tengiai)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-emerald-600 via-green-600 to-teal-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.show', $cocau->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Gán giải thưởng</span>
        </div>
        
        <h1 class="text-4xl font-black mb-2">
            <i class="fas fa-user-plus mr-3"></i>{{ $cocau->tengiai }}
        </h1>
        <p class="text-green-100">{{ $cocau->cuocthi->tencuocthi }}</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- THÔNG TIN CƠ CẤU & GÁN HÀNG LOẠT --}}
<section class="container mx-auto px-6 -mt-12 relative z-20 mb-8">
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Thống kê --}}
        <div class="bg-white rounded-2xl shadow-xl border border-emerald-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-emerald-600 mr-2"></i>Thống kê
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng slot:</span>
                    <span class="font-black text-gray-800">{{ $tongSlot ?? '∞' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Đã gán:</span>
                    <span class="font-black text-emerald-600">{{ $daGan }}</span>
                </div>
                @if($conLai !== null)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Còn lại:</span>
                    <span class="font-black text-amber-600">{{ $conLai }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Thông tin giải --}}
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Thông tin giải
            </h3>
            <div class="space-y-3">
                @if($cocau->tienthuong > 0)
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                    <span class="font-semibold">{{ number_format($cocau->tienthuong) }} VNĐ</span>
                </div>
                @endif
                @if($cocau->giaykhen)
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-certificate text-amber-600"></i>
                    <span class="font-semibold">Giấy khen</span>
                </div>
                @endif
                @if($cocau->chophepdonghang)
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-infinity text-purple-600"></i>
                    <span class="font-semibold">Cho phép đồng hạng</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Gán hàng loạt --}}
        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-xl border border-emerald-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-layer-group text-emerald-600 mr-2"></i>Gán hàng loạt
            </h3>
            <form action="{{ route('giangvien.giaithuong.gan-giai-hang-loat', $cocau->macocau) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Từ xếp hạng</label>
                        <input type="number" name="tu_xephang" min="1" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Đến xếp hạng</label>
                        <input type="number" name="den_xephang" min="1" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition">
                        <i class="fas fa-magic mr-2"></i>Gán tự động
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- DANH SÁCH KẾT QUẢ THI --}}
<section class="container mx-auto px-6 pb-12">
    @if($ketquaList->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-emerald-50 to-green-50 border-b border-emerald-100">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hạng</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Thí sinh</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Loại</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Điểm</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Giải KQ</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($ketquaList as $ketqua)
                        <tr class="hover:bg-emerald-50 transition">
                            {{-- Xếp hạng --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-100 to-yellow-100 rounded-xl border-2 border-amber-200">
                                    <span class="text-lg font-black text-amber-700">{{ $ketqua->xephang }}</span>
                                </div>
                            </td>

                            {{-- Thông tin thí sinh --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gradient-to-br from-blue-100 to-cyan-100 p-2.5 rounded-lg">
                                        <i class="fas {{ $ketqua->loaidangky == 'CaNhan' ? 'fa-user' : 'fa-users' }} text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            @if($ketqua->loaidangky == 'CaNhan')
                                                {{ $ketqua->ten_sinhvien ?? 'N/A' }}
                                            @else
                                                {{ $ketqua->tendoithi ?? 'N/A' }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($ketqua->loaidangky == 'CaNhan')
                                                MSSV: {{ $ketqua->masinhvien ?? 'N/A' }}
                                            @else
                                                Mã đội: {{ $ketqua->madoithi ?? 'N/A' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Loại đăng ký --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($ketqua->loaidangky == 'CaNhan')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold border border-blue-200">
                                        Cá nhân
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold border border-purple-200">
                                        Đội nhóm
                                    </span>
                                @endif
                            </td>

                            {{-- Điểm --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-br from-green-100 to-emerald-100 rounded-lg border border-green-200">
                                    <span class="text-lg font-black text-green-700">{{ number_format($ketqua->diem, 1) }}</span>
                                </div>
                            </td>

                            {{-- Giải từ kết quả --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($ketqua->giaithuong)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold border border-amber-200">
                                        {{ $ketqua->giaithuong }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            {{-- Trạng thái gán --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($ketqua->magangiai)
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i>Đã gán
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold border border-gray-200">
                                        Chưa gán
                                    </span>
                                @endif
                            </td>

                            {{-- Thao tác --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(!$ketqua->magangiai)
                                    {{-- Nút gán giải --}}
                                    <button type="button" 
                                        onclick="openGanGiaiModal('{{ $ketqua->loaidangky }}', '{{ $ketqua->loaidangky == 'CaNhan' ? $ketqua->madangkycanhan : $ketqua->madangkydoi }}', {{ $ketqua->xephang }}, '{{ $ketqua->loaidangky == 'CaNhan' ? $ketqua->ten_sinhvien : $ketqua->tendoithi }}')"
                                        class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition text-xs">
                                        <i class="fas fa-plus mr-1"></i>Gán giải
                                    </button>
                                @else
                                    {{-- Nút hủy gán - có thể hủy bất cứ lúc nào --}}
                                    <form action="{{ route('giangvien.giaithuong.huy-gan-giai', $ketqua->magangiai) }}" 
                                        method="POST" 
                                        class="inline-block"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy gán giải này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition text-xs">
                                            <i class="fas fa-times mr-1"></i>Hủy
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <i class="fas fa-clipboard-list text-8xl text-gray-300"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-700 mb-3">Chưa có kết quả thi</h4>
                <p class="text-gray-500 mb-8">
                    Cuộc thi này chưa có kết quả thi nào được chấm điểm.
                </p>
            </div>
        </div>
    @endif
</section>

{{-- MODAL GÁN GIẢI --}}
<div id="ganGiaiModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-green-600 text-white p-6">
            <h3 class="text-2xl font-black">
                <i class="fas fa-trophy mr-2"></i>Gán giải thưởng
            </h3>
        </div>
        
        <form action="{{ route('giangvien.giaithuong.store-gan-giai', $cocau->macocau) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="loaidangky" id="modal_loaidangky">
            <input type="hidden" name="madangky" id="modal_madangky">
            <input type="hidden" name="xephangthucte" id="modal_xephangthucte">

            <div class="mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Thông tin:</div>
                    <div id="modal_thongtin" class="text-gray-800 font-medium"></div>
                </div>

                <label class="flex items-center gap-2 mb-4">
                    <input type="checkbox" name="ladongkang" value="1" class="w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500">
                    <span class="text-sm font-semibold text-gray-700">Đồng hạng</span>
                </label>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú</label>
                    <textarea name="ghichu" rows="3" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="Nhập ghi chú (nếu có)..."></textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                    onclick="closeGanGiaiModal()"
                    class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold transition">
                    Hủy
                </button>
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition">
                    <i class="fas fa-check mr-2"></i>Xác nhận
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openGanGiaiModal(loaidangky, madangky, xephang, ten) {
    document.getElementById('modal_loaidangky').value = loaidangky;
    document.getElementById('modal_madangky').value = madangky;
    document.getElementById('modal_xephangthucte').value = xephang;
    document.getElementById('modal_thongtin').innerHTML = `
        <strong>${ten}</strong><br>
        Xếp hạng: <strong class="text-amber-600">#${xephang}</strong>
    `;
    document.getElementById('ganGiaiModal').classList.remove('hidden');
    document.getElementById('ganGiaiModal').classList.add('flex');
}

function closeGanGiaiModal() {
    document.getElementById('ganGiaiModal').classList.add('hidden');
    document.getElementById('ganGiaiModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('ganGiaiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGanGiaiModal();
    }
});
</script>
@endpush