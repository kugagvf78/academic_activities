<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm danh - {{ $hoatdong->tenhoatdong }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-100 via-pink-100 to-blue-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl shadow-2xl p-8 text-white mb-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                    <i class="fas fa-user-check text-4xl"></i>
                </div>
                <h1 class="text-2xl font-black mb-2">Điểm danh</h1>
                <p class="text-purple-100 text-sm">{{ $hoatdong->tenhoatdong }}</p>
            </div>

            {{-- Thông tin hoạt động --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-purple-600"></i>
                        <div class="flex-1">
                            <div class="text-xs text-gray-500">Thời gian</div>
                            <div class="text-sm font-semibold text-gray-800">
                                {{ $hoatdong->thoigianbatdau->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    @if($hoatdong->diadiem)
                    <div class="flex items-center gap-3">
                        <i class="fas fa-map-marker-alt text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-xs text-gray-500">Địa điểm</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $hoatdong->diadiem }}</div>
                        </div>
                    </div>
                    @endif

                    @if($hoatdong->diemrenluyen)
                    <div class="flex items-center gap-3">
                        <i class="fas fa-star text-yellow-500"></i>
                        <div class="flex-1">
                            <div class="text-xs text-gray-500">Điểm rèn luyện</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $hoatdong->diemrenluyen }} điểm</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Form điểm danh --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Nhập thông tin điểm danh</h2>

                <form id="checkInForm" class="space-y-4">
                    <input type="hidden" name="qr_code" value="{{ $qrCode }}">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mã sinh viên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            name="masinhvien" 
                            id="masinhvien"
                            placeholder="Nhập mã sinh viên của bạn" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 transition">
                    </div>

                    <button type="submit" 
                        id="submitBtn"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 rounded-xl font-bold hover:from-purple-700 hover:to-pink-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span id="btnText">Điểm danh</span>
                    </button>
                </form>

                {{-- Message area --}}
                <div id="messageArea" class="mt-4 hidden"></div>
            </div>

            {{-- Hướng dẫn --}}
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4">
                <div class="flex gap-3">
                    <i class="fas fa-info-circle text-blue-600 text-lg mt-1"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Lưu ý:</p>
                        <ul class="space-y-1 text-blue-700">
                            <li>• Bạn phải đăng ký hoạt động trước khi điểm danh</li>
                            <li>• Mỗi sinh viên chỉ được điểm danh một lần</li>
                            <li>• Nhập chính xác mã sinh viên của bạn</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('checkInForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const messageArea = document.getElementById('messageArea');
        
        // Disable button
        submitBtn.disabled = true;
        btnText.textContent = 'Đang xử lý...';
        
        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("giangvien.hoatdong.check-in", $hoatdong->mahoatdong) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    masinhvien: formData.get('masinhvien'),
                    qr_code: formData.get('qr_code')
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Success message
                messageArea.className = 'mt-4 bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 rounded-xl p-4';
                messageArea.innerHTML = `
                    <div class="flex gap-3">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        <div class="flex-1">
                            <p class="font-bold text-green-800 mb-2">Điểm danh thành công!</p>
                            <div class="text-sm text-green-700 space-y-1">
                                <p><strong>Họ tên:</strong> ${data.data.hoten}</p>
                                <p><strong>Mã SV:</strong> ${data.data.masinhvien}</p>
                                <p><strong>Lớp:</strong> ${data.data.malop}</p>
                                <p><strong>Thời gian:</strong> ${data.data.time}</p>
                            </div>
                        </div>
                    </div>
                `;
                messageArea.classList.remove('hidden');
                form.reset();
            } else {
                // Error message
                messageArea.className = 'mt-4 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-4';
                messageArea.innerHTML = `
                    <div class="flex gap-3">
                        <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        <div class="flex-1">
                            <p class="font-bold text-red-800 mb-1">Không thể điểm danh!</p>
                            <p class="text-sm text-red-700">${data.message}</p>
                        </div>
                    </div>
                `;
                messageArea.classList.remove('hidden');
            }
            
        } catch (error) {
            console.error('Error:', error);
            messageArea.className = 'mt-4 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-xl p-4';
            messageArea.innerHTML = `
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                    <div class="flex-1">
                        <p class="font-bold text-red-800">Có lỗi xảy ra!</p>
                        <p class="text-sm text-red-700">Vui lòng thử lại sau.</p>
                    </div>
                </div>
            `;
            messageArea.classList.remove('hidden');
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            btnText.textContent = 'Điểm danh';
        }
    });
    </script>
</body>
</html>