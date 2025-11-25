<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã QR không hợp lệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-100 via-orange-100 to-yellow-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            {{-- Error Card --}}
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-6">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-600"></i>
                </div>
                
                <h1 class="text-3xl font-black text-gray-800 mb-3">
                    Mã QR không hợp lệ
                </h1>
                
                <p class="text-gray-600 mb-6">
                    Mã QR này đã hết hạn hoặc không tồn tại. Vui lòng liên hệ với giảng viên để được hỗ trợ.
                </p>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4 mb-6 text-left">
                    <div class="flex gap-3">
                        <i class="fas fa-lightbulb text-yellow-600 text-lg mt-1"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-semibold mb-2">Có thể do:</p>
                            <ul class="space-y-1">
                                <li>• Mã QR đã hết hạn</li>
                                <li>• Link không chính xác</li>
                                <li>• Hoạt động đã kết thúc</li>
                                <li>• Mã QR đã bị thay đổi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <button onclick="window.history.back()" 
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-xl font-bold hover:from-blue-700 hover:to-cyan-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </button>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="mt-6 bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <span>Cần hỗ trợ?</span>
                </h3>
                <p class="text-sm text-gray-600">
                    Vui lòng liên hệ với giảng viên phụ trách hoạt động hoặc ban tổ chức để được cấp mã QR mới.
                </p>
            </div>
        </div>
    </div>
</body>
</html>