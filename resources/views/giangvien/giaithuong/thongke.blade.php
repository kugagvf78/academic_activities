@extends('layouts.client')
@section('title', 'Thống kê giải thưởng - ' . $cuocthi->tencuocthi)

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-500 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6z\'/%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('giangvien.giaithuong.show', $cuocthi->macuocthi) }}" class="text-white/80 hover:text-white transition">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span class="text-white/60">|</span>
            <span class="text-white/90 text-sm">Thống kê giải thưởng</span>
        </div>
        
        <h1 class="text-4xl font-black mb-3">
            <i class="fas fa-chart-pie mr-3"></i>Thống kê giải thưởng
        </h1>
        <p class="text-indigo-100 text-lg">{{ $cuocthi->tencuocthi }}</p>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80L60 75C120 70 240 60 360 55C480 50 600 50 720 52.5C840 55 960 60 1080 62.5C1200 65 1320 65 1380 65L1440 65V80H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- TỔNG QUAN --}}
<section class="container mx-auto px-6 -mt-12 relative z-20 mb-8">
    <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-xl border border-purple-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-purple-100 to-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-trophy text-2xl text-purple-600"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1">{{ $tongCoCau }}</div>
            <div class="text-sm font-medium text-gray-600">Loại giải thưởng</div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-blue-100 to-cyan-100 p-3 rounded-xl">
                    <i class="fas fa-award text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1">{{ $tongGiaiDaGan }}</div>
            <div class="text-sm font-medium text-gray-600">Tổng giải đã gán</div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-6 hover:shadow-2xl transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-3 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1">{{ $giaiApproved }}</div>
            <div class="text-sm font-medium text-gray-600">Đã gán & Duyệt</div>
        </div>
    </div>
</section>

{{-- THỐNG KÊ THEO LOẠI ĐĂNG KÝ --}}
<section class="container mx-auto px-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-users text-purple-600 mr-2"></i>Thống kê theo loại đăng ký
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-blue-800 uppercase tracking-wide">Cá nhân</div>
                            <div class="text-xs text-blue-600">Giải dành cho cá nhân</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-blue-700">
                        {{ $theoLoai['CaNhan'] ?? 0 }}
                    </div>
                </div>
                <div class="text-sm text-blue-700 font-medium">
                    {{ $tongGiaiDaGan > 0 ? number_format((($theoLoai['CaNhan'] ?? 0) / $tongGiaiDaGan) * 100, 1) : 0 }}% tổng số giải
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-purple-800 uppercase tracking-wide">Đội nhóm</div>
                            <div class="text-xs text-purple-600">Giải dành cho đội/nhóm</div>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-purple-700">
                        {{ $theoLoai['DoiNhom'] ?? 0 }}
                    </div>
                </div>
                <div class="text-sm text-purple-700 font-medium">
                    {{ $tongGiaiDaGan > 0 ? number_format((($theoLoai['DoiNhom'] ?? 0) / $tongGiaiDaGan) * 100, 1) : 0 }}% tổng số giải
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CHI TIẾT TỪNG CƠ CẤU GIẢI --}}
<section class="container mx-auto px-6 pb-12">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-8 py-6 border-b border-purple-100">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-list text-purple-600 mr-2"></i>Chi tiết từng loại giải
            </h2>
        </div>

        @if($theoCoCau->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tên giải</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Số lượng</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Đã gán</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Tiền thưởng</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($theoCoCau as $index => $cocau)
                        @php
                            $percentage = $cocau->chophepdonghang ? 100 : ($cocau->soluong > 0 ? ($cocau->da_gan / $cocau->soluong) * 100 : 0);
                            $colorClass = $percentage >= 100 ? 'emerald' : ($percentage >= 50 ? 'blue' : 'amber');
                        @endphp
                        <tr class="hover:bg-purple-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $index + 1 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gradient-to-br from-amber-100 to-yellow-100 p-2 rounded-lg">
                                        <i class="fas fa-trophy text-amber-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $cocau->tengiai }}</div>
                                        @if($cocau->chophepdonghang)
                                            <div class="text-xs text-purple-600 font-medium">
                                                <i class="fas fa-infinity mr-1"></i>Cho phép đồng hạng
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($cocau->chophepdonghang)
                                    <span class="text-sm font-bold text-purple-600">∞</span>
                                @else
                                    <span class="text-sm font-bold text-gray-900">{{ $cocau->soluong }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold border border-blue-200">
                                    {{ $cocau->da_gan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-green-600">{{ number_format($cocau->tienthuong) }} đ</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-{{$colorClass}}-400 to-{{$colorClass}}-600 h-full rounded-full transition-all duration-500" 
                                            style="width: {{ min($percentage, 100) }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-{{$colorClass}}-600 min-w-[45px] text-right">
                                        {{ number_format(min($percentage, 100), 0) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gradient-to-r from-gray-100 to-blue-100">
                        <tr class="font-bold">
                            <td colspan="3" class="px-6 py-4 text-right text-gray-800">TỔNG CỘNG:</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-black text-sm">
                                    {{ $tongGiaiDaGan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-green-700 font-black text-base">
                                    {{ number_format($tongTienThuong) }} đ
                                </span>
                            </td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-16 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Chưa có cơ cấu giải thưởng nào</p>
            </div>
        @endif
    </div>
</section>

@endsection