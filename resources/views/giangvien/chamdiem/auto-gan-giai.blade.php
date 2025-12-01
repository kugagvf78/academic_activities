@extends('layouts.client')
@section('title', 'Tự động gán giải thưởng - ' . $cuocthi->tencuocthi)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.chamdiem.show-cuocthi', $cuocthi->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Tự động gán giải thưởng</span>
        </div>
        
        <h1 class="text-4xl font-black mb-3">
            <i class="fas fa-magic mr-3"></i>Tự động gán giải thưởng
        </h1>
        <p class="text-purple-100 text-lg">{{ $cuocthi->tencuocthi }}</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- TRẠNG THÁI --}}
<section class="container mx-auto px-6 -mt-12 relative z-20 mb-8">
    <div class="bg-white rounded-2xl shadow-xl border border-purple-100 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Trạng thái chấm điểm
        </h2>

        @if($check['can_auto'])
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200">
                    <div class="text-3xl font-black text-blue-700 mb-2">{{ $check['tong_bai_thi'] }}</div>
                    <div class="text-sm font-semibold text-blue-600">Tổng bài thi</div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                    <div class="text-3xl font-black text-green-700 mb-2">{{ $check['da_cham'] }}</div>
                    <div class="text-sm font-semibold text-green-600">Đã chấm</div>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border border-yellow-200">
                    <div class="text-3xl font-black text-yellow-700 mb-2">{{ $check['chua_cham'] }}</div>
                    <div class="text-sm font-semibold text-yellow-600">Chưa chấm</div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                    <div class="text-3xl font-black text-purple-700 mb-2">{{ $check['phan_tram_hoan_thanh'] }}%</div>
                    <div class="text-sm font-semibold text-purple-600">Hoàn thành</div>
                </div>
            </div>

            @if($check['chua_cham'] > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-yellow-800">Cảnh báo</p>
                            <p class="text-yellow-700 text-sm">Còn {{ $check['chua_cham'] }} bài chưa chấm. Kết quả gán giải có thể chưa chính xác.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-green-800">Sẵn sàng</p>
                            <p class="text-green-700 text-sm">Tất cả bài thi đã được chấm. Có thể gán giải tự động.</p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold text-red-800">Không thể gán giải tự động</p>
                        <p class="text-red-700 text-sm">{{ $check['reason'] }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- CƠ CẤU GIẢI THƯỞNG --}}
@if($check['can_auto'] && $cocauList->count() > 0)
<section class="container mx-auto px-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-8 py-6 border-b border-purple-100">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-trophy text-purple-600 mr-2"></i>Cơ cấu giải thưởng
            </h2>
        </div>

        <div class="p-8">
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($cocauList as $cocau)
                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-6 border border-amber-200">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $cocau->tengiai }}</h3>
                            <div class="text-sm text-gray-600">
                                @if($cocau->chophepdonghang)
                                    <i class="fas fa-infinity text-purple-600 mr-1"></i>Cho phép đồng hạng
                                @else
                                    <i class="fas fa-trophy text-amber-600 mr-1"></i>Số lượng: {{ $cocau->soluong }}
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-green-600">
                                {{ number_format($cocau->tienthuong) }} đ
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-amber-300">
                        <div>
                            <div class="text-xs text-gray-600 mb-1">Chờ duyệt</div>
                            <div class="text-xl font-bold text-yellow-600">{{ $cocau->da_gan_pending }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 mb-1">Đã duyệt</div>
                            <div class="text-xl font-bold text-green-600">{{ $cocau->da_gan_approved }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- PREVIEW TOP ĐIỂM --}}
@if($check['can_auto'] && $topDiem->count() > 0)
<section class="container mx-auto px-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-8 py-6 border-b border-blue-100">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-list-ol text-blue-600 mr-2"></i>Preview Top {{ $topDiem->count() }} điểm cao nhất
            </h2>
            <p class="text-sm text-gray-600 mt-1">Dự kiến gán giải dựa trên xếp hạng này</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hạng</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tên / Đội</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Điểm</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dự kiến gán</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $currentRank = 1;
                        $lastDiem = null;
                        $dongHangCount = 0;
                    @endphp
                    @foreach($topDiem as $index => $ketqua)
                        @php
                            // Tính xếp hạng
                            if ($lastDiem !== null && $ketqua->diem == $lastDiem) {
                                $dongHangCount++;
                            } else {
                                $currentRank += $dongHangCount;
                                $dongHangCount = 0;
                            }
                            $lastDiem = $ketqua->diem;
                            
                            // Dự kiến gán giải gì
                            $duKienGiai = '';
                            foreach($cocauList as $cocau) {
                                if ($cocau->chophepdonghang || $currentRank <= $cocau->soluong) {
                                    $duKienGiai = $cocau->tengiai;
                                    break;
                                }
                            }
                        @endphp
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full font-bold
                                    {{ $currentRank == 1 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white' : 
                                       ($currentRank == 2 ? 'bg-gradient-to-br from-gray-300 to-gray-500 text-white' :
                                       ($currentRank == 3 ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white' : 
                                       'bg-gray-100 text-gray-700')) }}">
                                    {{ $currentRank }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ketqua->baithi->madangkycanhan)
                                    <div class="font-semibold text-gray-900">
                                        {{ $ketqua->baithi->dangkycanhan->sinhvien->nguoidung->hoten ?? 'N/A' }}
                                    </div>
                                @else
                                    <div class="font-semibold text-gray-900">
                                        {{ $ketqua->baithi->dangkydoi->tendoithi ?? 'N/A' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($ketqua->baithi->madangkycanhan)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                        Cá nhân
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                        Đội nhóm
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-black text-green-600">{{ $ketqua->diem }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($duKienGiai)
                                    <span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-amber-100 to-yellow-100 border border-amber-300 text-amber-700 rounded-lg text-sm font-semibold">
                                        <i class="fas fa-trophy mr-2"></i>{{ $duKienGiai }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endif

{{-- ACTIONS --}}
@if($check['can_auto'])
<section class="container mx-auto px-6 pb-12">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-cog text-indigo-600 mr-2"></i>Thực hiện gán giải
        </h2>

        <form action="{{ route('giangvien.chamdiem.auto-gan-giai', $cuocthi->macuocthi) }}" method="POST" 
            onsubmit="return confirm('Bạn có chắc muốn tự động gán giải? Giải đã gán sẽ ở trạng thái Chờ duyệt.')">
            @csrf

            @if($check['chua_cham'] > 0)
                <input type="hidden" name="confirm" value="1">
            @endif

            <div class="flex gap-4">
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-magic mr-2"></i>Tự động gán giải ngay
                </button>

                @if($check['da_gan_giai'] > 0)
                <form action="{{ route('giangvien.chamdiem.xoa-gan-giai-tu-dong', $cuocthi->macuocthi) }}" method="POST" class="flex-1"
                    onsubmit="return confirm('Bạn có chắc muốn xóa tất cả giải chờ duyệt?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-trash mr-2"></i>Xóa giải chờ duyệt ({{ $check['da_gan_giai'] }})
                    </button>
                </form>
                @endif
            </div>
        </form>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Lưu ý:</strong> Giải được gán tự động sẽ ở trạng thái <strong>"Chờ duyệt"</strong>. 
                Bạn cần vào trang <a href="{{ route('giangvien.giaithuong.show', $cuocthi->macuocthi) }}" class="text-blue-600 underline font-semibold">Quản lý giải thưởng</a> 
                để duyệt/từ chối từng giải.
            </p>
        </div>
    </div>
</section>
@endif

@endsection