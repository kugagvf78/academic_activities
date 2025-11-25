@extends('layouts.client')

@section('title', 'Chi tiết Hoạt động')

@section('content')
<div class="container mx-auto px-6 py-8">
    {{-- Back button --}}
    <div class="mb-6">
        <a href="{{ route('giangvien.hoatdong.index') }}" 
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 font-semibold transition">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        {{-- Thông tin hoạt động --}}
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-black text-gray-800 mb-2">{{ $hoatdong->tenhoatdong }}</h2>
                        <div class="flex items-center gap-4 text-sm">
                            @if($hoatdong->loaihoatdong == 'CoVu')
                                <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full font-semibold">
                                    <i class="fas fa-bullhorn mr-1"></i>Cổ vũ
                                </span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">
                                    <i class="fas fa-tools mr-1"></i>Hỗ trợ Kỹ thuật
                                </span>
                            @endif
                            
                            @php
                                $now = now();
                                $start = $hoatdong->thoigianbatdau;
                                $end = $hoatdong->thoigianketthuc;
                                
                                if ($now->lt($start)) {
                                    $statusColor = 'yellow';
                                    $statusLabel = 'Sắp diễn ra';
                                } elseif ($now->between($start, $end)) {
                                    $statusColor = 'green';
                                    $statusLabel = 'Đang diễn ra';
                                } else {
                                    $statusColor = 'gray';
                                    $statusLabel = 'Đã kết thúc';
                                }
                            @endphp
                            
                            <span class="px-3 py-1 rounded-full font-semibold
                                @if($statusColor == 'green') bg-green-100 text-green-700
                                @elseif($statusColor == 'yellow') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        @if($statusLabel == 'Đang diễn ra')
                            <a href="{{ route('giangvien.hoatdong.generate-qr', $hoatdong->mahoatdong) }}" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition">
                                <i class="fas fa-qrcode mr-2"></i>Tạo QR
                            </a>
                        @endif
                        <a href="{{ route('giangvien.hoatdong.edit', $hoatdong->mahoatdong) }}" 
                            class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">
                            <i class="fas fa-edit mr-2"></i>Sửa
                        </a>
                        <a href="{{ route('giangvien.hoatdong.export-attendance', $hoatdong->mahoatdong) }}" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>

                <div class="space-y-4 border-t pt-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-trophy text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Cuộc thi</div>
                            <div class="font-bold text-gray-800">{{ $hoatdong->cuocthi->tencuocthi }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Thời gian</div>
                            <div class="font-bold text-gray-800">
                                {{ $hoatdong->thoigianbatdau->format('d/m/Y H:i') }} - {{ $hoatdong->thoigianketthuc->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    @if($hoatdong->diadiem)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Địa điểm</div>
                            <div class="font-bold text-gray-800">{{ $hoatdong->diadiem }}</div>
                        </div>
                    </div>
                    @endif

                    @if($hoatdong->diemrenluyen)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Điểm rèn luyện</div>
                            <div class="font-bold text-gray-800">{{ $hoatdong->diemrenluyen }} điểm</div>
                        </div>
                    </div>
                    @endif

                    @if($hoatdong->mota)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-500 mb-1">Mô tả</div>
                            <div class="text-gray-700">{{ $hoatdong->mota }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Thống kê --}}
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="text-sm font-semibold mb-2 opacity-90">Tổng đăng ký</div>
                <div class="text-5xl font-black mb-2">{{ $stats['total'] }}</div>
                <div class="text-sm opacity-90">/ {{ $hoatdong->soluong }} chỗ</div>
                <div class="mt-4 bg-white/20 rounded-full h-2">
                    <div class="bg-white rounded-full h-2 transition-all duration-500" 
                        style="width: {{ $hoatdong->soluong > 0 ? ($stats['total'] / $hoatdong->soluong * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="text-sm font-semibold text-gray-600 mb-4">Điểm danh</div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Đã điểm danh</span>
                        <span class="text-2xl font-bold text-green-600">{{ $stats['checked_in'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Chưa điểm danh</span>
                        <span class="text-2xl font-bold text-orange-600">{{ $stats['not_checked_in'] }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="text-sm text-gray-500 mb-2">Tỷ lệ điểm danh</div>
                    <div class="text-3xl font-bold text-purple-600">
                        {{ $stats['total'] > 0 ? round(($stats['checked_in'] / $stats['total']) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách đăng ký --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
            <h3 class="text-xl font-bold text-gray-800">Danh sách đăng ký ({{ $dangkys->count() }})</h3>
        </div>

        @if($dangkys->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Mã SV</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Họ tên</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Lớp</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Điểm danh</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Thời gian điểm danh</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dangkys as $index => $dk)
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $dk->sinhvien->masinhvien }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $dk->sinhvien->nguoidung->hoten }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $dk->sinhvien->malop ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($dk->diemdanhqr)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Đã điểm danh</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold">
                                            <i class="fas fa-clock"></i>
                                            <span>Chưa điểm danh</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $dk->thoigiandiemdanh ? $dk->thoigiandiemdanh->format('d/m/Y H:i:s') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Chưa có sinh viên nào đăng ký</p>
            </div>
        @endif
    </div>
</div>
@endsection