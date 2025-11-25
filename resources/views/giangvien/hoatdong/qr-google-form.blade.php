@extends('layouts.client')

@section('title', 'Điểm danh Google Form')

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

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl shadow-xl p-8 text-white mb-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                <i class="fab fa-google text-4xl"></i>
            </div>
            <h1 class="text-3xl font-black mb-2">Điểm danh qua Google Form</h1>
            <p class="text-blue-100">{{ $hoatdong->tenhoatdong }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
        {{-- Cột trái: Hướng dẫn --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    <span>Bước 1: Tạo Google Form</span>
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-sm text-gray-700 mb-3">
                            <strong>Truy cập:</strong> 
                            <a href="https://forms.google.com" target="_blank" class="text-blue-600 hover:underline">
                                https://forms.google.com
                            </a>
                        </p>
                        
                        <p class="text-sm text-gray-700 mb-3">
                            <strong>Tạo form mới với các câu hỏi:</strong>
                        </p>
                        
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-green-600 mt-1"></i>
                                <div>
                                    <strong>Mã sinh viên</strong> (Câu trả lời ngắn, BẮT BUỘC)
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-green-600 mt-1"></i>
                                <div>
                                    <strong>Họ và tên</strong> (Câu trả lời ngắn, BẮT BUỘC)
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check text-green-600 mt-1"></i>
                                <div>
                                    <strong>Lớp</strong> (Câu trả lời ngắn)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 rounded-xl p-4 border-l-4 border-yellow-500">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Cài đặt quan trọng:
                        </p>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Bật "Collect email addresses"</li>
                            <li>• Bật "Limit to 1 response" (chỉ điểm danh 1 lần)</li>
                            <li>• Bật "Response receipts" để lấy timestamp</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-qrcode text-blue-600"></i>
                    <span>Bước 2: Tạo mã QR</span>
                </h3>
                
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Dán link Google Form của bạn vào đây:
                        </label>
                        <input type="url" 
                               id="googleFormUrl" 
                               placeholder="https://forms.gle/xxxxx hoặc https://docs.google.com/forms/d/e/..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 transition text-sm">
                    </div>

                    <button onclick="generateQRCode()" 
                            class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-xl font-bold hover:from-blue-700 hover:to-cyan-700 transition shadow-lg">
                        <i class="fas fa-magic mr-2"></i>Tạo mã QR
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-upload text-blue-600"></i>
                    <span>Bước 3: Import điểm danh</span>
                </h3>
                
                <p class="text-sm text-gray-600 mb-4">
                    Sau khi sinh viên điểm danh xong, export file từ Google Form và upload vào đây:
                </p>

                <form action="{{ route('giangvien.hoatdong.import-google-form', $hoatdong->mahoatdong) }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      class="space-y-3">
                    @csrf
                    
                    <input type="file" 
                           name="file" 
                           accept=".xlsx,.xls,.csv"
                           required
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 transition text-sm">
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
                        <i class="fas fa-upload mr-2"></i>Import ngay
                    </button>
                </form>
            </div>
        </div>

        {{-- Cột phải: Hiển thị QR Code --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Mã QR Code</h3>
                
                <div id="qrCodeContainer" class="hidden">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 mb-4">
                        <div class="flex justify-center" id="qrCodeDisplay"></div>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="text-xs text-gray-600 mb-1">Link Google Form:</div>
                            <div class="font-mono text-xs text-gray-800 break-all" id="displayUrl"></div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="printQR()" 
                                class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-bold hover:bg-purple-700 transition">
                            <i class="fas fa-print mr-2"></i>In mã QR
                        </button>
                        <button onclick="downloadQR()" 
                                class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                            <i class="fas fa-download mr-2"></i>Tải về
                        </button>
                    </div>
                </div>

                <div id="qrPlaceholder" class="text-center py-12">
                    <i class="fas fa-qrcode text-8xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Nhập link Google Form và click "Tạo mã QR"</p>
                </div>
            </div>

            {{-- Thống kê --}}
            <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="text-sm font-semibold mb-2 opacity-90">Thống kê điểm danh</div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="bg-white/20 rounded-xl p-4">
                        <div class="text-3xl font-black">{{ $stats['total'] }}</div>
                        <div class="text-sm opacity-90">Đã đăng ký</div>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4">
                        <div class="text-3xl font-black">{{ $stats['checked_in'] }}</div>
                        <div class="text-sm opacity-90">Đã điểm danh</div>
                    </div>
                </div>
            </div>

            {{-- Thông tin hoạt động --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h4 class="font-bold text-gray-800 mb-3">Thông tin hoạt động</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cuộc thi:</span>
                        <span class="font-semibold text-gray-800">{{ $hoatdong->cuocthi->tencuocthi }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Thời gian:</span>
                        <span class="font-semibold text-gray-800">{{ $hoatdong->thoigianbatdau->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Địa điểm:</span>
                        <span class="font-semibold text-gray-800">{{ $hoatdong->diadiem ?? 'Chưa xác định' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include QRCode.js library --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
let qrCodeInstance = null;

function generateQRCode() {
    const url = document.getElementById('googleFormUrl').value.trim();
    
    if (!url) {
        alert('Vui lòng nhập link Google Form!');
        return;
    }

    // Validate URL
    if (!url.includes('forms.gle') && !url.includes('docs.google.com/forms')) {
        alert('Link không hợp lệ! Vui lòng nhập link Google Form.');
        return;
    }

    // Clear old QR code
    const qrDisplay = document.getElementById('qrCodeDisplay');
    qrDisplay.innerHTML = '';

    // Generate new QR code
    qrCodeInstance = new QRCode(qrDisplay, {
        text: url,
        width: 300,
        height: 300,
        colorDark: "#1e40af",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Show QR code container
    document.getElementById('qrCodeContainer').classList.remove('hidden');
    document.getElementById('qrPlaceholder').classList.add('hidden');
    
    // Display URL
    document.getElementById('displayUrl').textContent = url;
}

function printQR() {
    window.print();
}

function downloadQR() {
    const canvas = document.querySelector('#qrCodeDisplay canvas');
    if (canvas) {
        const url = canvas.toDataURL("image/png");
        const link = document.createElement('a');
        link.download = 'qr-code-{{ $hoatdong->mahoatdong }}.png';
        link.href = url;
        link.click();
    } else {
        alert('Vui lòng tạo mã QR trước!');
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #qrCodeContainer, #qrCodeContainer * {
        visibility: visible;
    }
    #qrCodeContainer {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>
@endsection