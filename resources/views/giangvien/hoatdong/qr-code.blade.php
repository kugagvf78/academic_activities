@extends('layouts.client')

@section('title', 'Mã QR Điểm danh')

@section('content')
<div class="container mx-auto px-6 py-8">
    {{-- Back button --}}
    <div class="mb-6">
        <a href="{{ route('giangvien.hoatdong.show', $hoatdong->mahoatdong) }}" 
            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 font-semibold transition">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-xl p-8 text-white mb-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                    <i class="fas fa-qrcode text-4xl"></i>
                </div>
                <h1 class="text-3xl font-black mb-2">Mã QR Điểm danh</h1>
                <p class="text-purple-100">{{ $hoatdong->tenhoatdong }}</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            {{-- QR Code --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Mã QR Code</h3>
                
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 mb-6">
                    <div class="flex justify-center">
                        {!! $qrCodeSvg !!}
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-sm text-gray-600 mb-1">Mã QR</div>
                        <div class="font-mono text-sm font-bold text-gray-800 break-all">{{ $qrCode }}</div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-sm text-gray-600 mb-1">Link điểm danh</div>
                        <div class="font-mono text-xs text-gray-700 break-all">{{ $url }}</div>
                    </div>
                </div>

                <button onclick="printQR()" 
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-bold hover:from-purple-700 hover:to-pink-700 transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-print mr-2"></i>In mã QR
                </button>
            </div>

            {{-- Hướng dẫn --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-purple-600"></i>
                        <span>Hướng dẫn sử dụng</span>
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">1</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-700">In hoặc hiển thị mã QR tại địa điểm diễn ra hoạt động</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">2</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-700">Sinh viên đã đăng ký quét mã QR bằng điện thoại</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">3</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-700">Sinh viên nhập mã sinh viên của mình để điểm danh</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">4</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-700">Hệ thống tự động ghi nhận điểm danh</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-500 rounded-xl p-6">
                    <div class="flex gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                        <div>
                            <h4 class="font-bold text-yellow-800 mb-2">Lưu ý quan trọng</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Mã QR chỉ hoạt động trong thời gian diễn ra hoạt động</li>
                                <li>• Mỗi sinh viên chỉ được điểm danh một lần</li>
                                <li>• Sinh viên phải đăng ký hoạt động trước khi điểm danh</li>
                                <li>• Giữ mã QR ở nơi dễ thấy và quét</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-800 mb-3">Thông tin hoạt động</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian:</span>
                            <span class="font-semibold text-gray-800">{{ $hoatdong->thoigianbatdau->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Địa điểm:</span>
                            <span class="font-semibold text-gray-800">{{ $hoatdong->diadiem ?? 'Chưa xác định' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Số lượng đăng ký:</span>
                            <span class="font-semibold text-purple-600">{{ $hoatdong->soluong_dangky }}/{{ $hoatdong->soluong }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Theo dõi điểm danh realtime --}}
        <div class="mt-8 bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Điểm danh gần đây</h3>
                <button onclick="refreshAttendance()" 
                    class="text-purple-600 hover:text-purple-800 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Làm mới
                </button>
            </div>

            <div id="recentAttendance" class="space-y-3">
                <p class="text-center text-gray-500 py-8">Chờ sinh viên điểm danh...</p>
            </div>
        </div>
    </div>
</div>

<script>
function printQR() {
    window.print();
}

// Auto refresh attendance every 5 seconds
setInterval(refreshAttendance, 5000);

function refreshAttendance() {
    // TODO: Implement AJAX call to get recent attendance
    console.log('Refreshing attendance...');
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .bg-white.rounded-2xl.shadow-xl.border.border-gray-100.p-8,
    .bg-white.rounded-2xl.shadow-xl.border.border-gray-100.p-8 * {
        visibility: visible;
    }
    .bg-white.rounded-2xl.shadow-xl.border.border-gray-100.p-8 {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection