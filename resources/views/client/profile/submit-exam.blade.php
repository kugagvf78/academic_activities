@extends('layouts.client')
@section('title', 'Nộp bài thi')

@section('content')
<section class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('profile.index') }}" class="text-gray-500 hover:text-blue-600 transition duration-150 ease-in-out text-sm font-medium">
                    <i class="fa-solid fa-user mr-1.5"></i>Hồ sơ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 text-xs mx-2"></i>
                    <span class="text-blue-600 font-semibold text-sm">Nộp bài thi</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Thông báo (Alerts) --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-green-500 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-exclamation text-red-500 mr-3 text-xl"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm" role="alert">
            <ul class="list-disc list-inside ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-5xl mx-auto">
        {{-- Thông tin cuộc thi (Header Card - Blue Navy Theme) --}}
        <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl shadow-xl p-8 text-white mb-8 transform hover:shadow-2xl transition duration-300">
            <div class="flex flex-col md:flex-row items-start justify-between gap-6 mb-6">
                <div class="flex-1">
                    <h1 class="text-4xl font-extrabold mb-2 leading-tight">{{ $dangky->tencuocthi }}</h1>
                    @if($loaidangky === 'DoiNhom')
                        <p class="text-blue-200 flex items-center gap-2 text-lg font-medium">
                            <i class="fa-solid fa-users text-blue-300"></i>
                            Đội thi: <span class="text-white font-semibold">{{ $dangky->tendoithi }}</span>
                        </p>
                    @else
                        <p class="text-blue-200 flex items-center gap-2 text-lg font-medium">
                            <i class="fa-solid fa-user text-blue-300"></i>
                            Hình thức: <span class="text-white font-semibold">Thi cá nhân</span>
                        </p>
                    @endif
                </div>
                <div class="bg-white/10 rounded-xl px-6 py-4 text-center backdrop-blur-sm border border-white/20 shadow-md">
                    <p class="text-blue-200 text-sm mb-1 uppercase tracking-wider">Hạn nộp bài</p>
                    <p class="text-3xl font-bold">{{ $submitDeadline->format('d/m/Y') }}</p>
                    <p class="text-base font-semibold">{{ $submitDeadline->format('H:i') }}</p>
                </div>
            </div>

            {{-- Timeline/Status Bar --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 border-t border-blue-600/50">
                @php
                    $start = \Carbon\Carbon::parse($dangky->thoigianbatdau);
                    $end = \Carbon\Carbon::parse($dangky->thoigianketthuc);
                @endphp
                
                {{-- Bắt đầu --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fa-solid fa-calendar-check text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-300 uppercase tracking-wider">Bắt đầu</p>
                        <p class="font-semibold text-white">{{ $start->format('d/m/Y') }}</p>
                        <p class="text-sm text-blue-200">{{ $start->format('H:i') }}</p>
                    </div>
                </div>

                {{-- Kết thúc --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fa-solid fa-flag-checkered text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-300 uppercase tracking-wider">Kết thúc</p>
                        <p class="font-semibold text-white">{{ $end->format('d/m/Y') }}</p>
                        <p class="text-sm text-blue-200">{{ $end->format('H:i') }}</p>
                    </div>
                </div>

                {{-- Countdown --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fa-solid fa-clock text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-300 uppercase tracking-wider">Thời gian còn lại</p>
                        <p class="font-extrabold text-xl text-yellow-300" id="countdown-timer" 
                        data-deadline="{{ $submitDeadline->timestamp }}">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang tải...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Đề thi (nếu có) --}}
        @if($dangky->madethi && $dangky->filedethi)
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8 shadow-lg hover:shadow-xl transition duration-300">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-red-200">
                        <i class="fa-solid fa-file-pdf text-red-600 text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-gray-800 mb-1">{{ $dangky->tendethi ?? 'Đề thi' }}</h3>
                        <p class="text-sm text-gray-500 flex items-center flex-wrap gap-x-4">
                            @if($dangky->thoigianlambai)
                                <span class="flex items-center">
                                    <i class="fa-solid fa-clock-o mr-1 text-red-500"></i>Thời gian: <span class="font-semibold ml-1">{{ $dangky->thoigianlambai }} phút</span>
                                </span>
                            @endif
                            @if($dangky->diemtoida)
                                <span class="flex items-center">
                                    <i class="fa-solid fa-star mr-1 text-red-500"></i>Điểm tối đa: <span class="font-semibold ml-1">{{ $dangky->diemtoida }}</span>
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ Storage::url($dangky->filedethi) }}" 
                   target="_blank"
                   class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition duration-150 ease-in-out flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-download"></i>
                    Tải đề thi
                </a>
            </div>
        </div>
        @endif

        {{-- Main Content: Form nộp bài và Hướng dẫn --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form nộp bài --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-8 shadow-2xl">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 shadow-inner">
                        <i class="fa-solid fa-upload text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Khu vực nộp bài</h2>
                        <p class="text-sm text-gray-500">Vui lòng kiểm tra kỹ file trước khi upload</p>
                    </div>
                </div>

                <form action="{{ route('profile.competition.submit', ['id' => $id, 'loaidangky' => $loaidangky]) }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                    onsubmit="return confirm('Bạn có chắc chắn muốn nộp bài? Hành động này không thể hoàn tác và bạn chỉ được nộp MỘT LẦN DUY NHẤT!');">
                    @csrf

                    {{-- Upload file --}}
                    <div class="mb-8">
                        <label class="block text-base font-semibold text-gray-700 mb-3">
                            File bài thi <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="border-4 border-dashed border-gray-200 rounded-xl p-10 text-center cursor-pointer 
                            hover:border-blue-500 hover:bg-blue-50 transition duration-300 ease-in-out"
                            onclick="document.getElementById('filebaithi').click();"
                            ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-blue-50');"
                            ondragleave="this.classList.remove('border-blue-500', 'bg-blue-50');"
                            ondrop="event.preventDefault(); this.classList.remove('border-blue-500', 'bg-blue-50'); handleFileDrop(event);">
                            
                            <input type="file" 
                                name="filebaithi" 
                                id="filebaithi" 
                                accept=".pdf,.doc,.docx,.zip,.rar"
                                class="hidden"
                                onchange="displayFileName(this)"
                                required>
                            
                            <div id="upload-area">
                                <i class="fa-solid fa-cloud-arrow-up text-6xl text-gray-400 mb-4"></i>
                                <p class="text-gray-700 font-bold mb-2 text-lg">
                                    Kéo thả file vào đây
                                </p>
                                <p class="text-gray-500 font-medium mb-4">
                                    Hoặc <span class="text-blue-600 hover:text-blue-700 cursor-pointer underline">chọn file</span> từ máy tính của bạn
                                </p>
                                <p class="text-xs text-gray-400">
                                    Định dạng cho phép: **PDF, DOC, DOCX, ZIP, RAR**. Dung lượng tối đa: **10MB**.
                                </p>
                            </div>

                            <div id="file-info" class="hidden">
                                <i class="fa-solid fa-file-circle-check text-6xl text-green-500 mb-4"></i>
                                <p class="text-gray-800 font-bold mb-1 text-lg">
                                    <span id="file-name"></span>
                                </p>
                                <p class="text-sm text-gray-500 mb-3">
                                    <span id="file-size"></span>
                                </p>
                                <button type="button" 
                                        onclick="event.stopPropagation(); clearFile();"
                                        class="mt-3 text-sm text-red-600 hover:text-red-700 font-semibold transition duration-150">
                                    <i class="fa-solid fa-xmark mr-1"></i>Xóa và chọn file khác
                                </button>
                            </div>
                        </div>

                        <p class="mt-4 text-sm text-gray-600">
                            <i class="fa-solid fa-info-circle text-blue-500 mr-1"></i>
                            <strong>Quan trọng:</strong> Hệ thống chỉ chấp nhận file có định dạng chuẩn và không vượt quá dung lượng quy định.
                        </p>
                    </div>

                    {{-- Xác nhận & Lưu ý --}}
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-5 mb-8">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-exclamation-triangle text-yellow-600 text-2xl mt-0.5 flex-shrink-0"></i>
                            <div>
                                <p class="font-bold text-yellow-800 mb-2 text-lg">Cảnh báo và Quy định nộp bài</p>
                                <ul class="text-sm text-yellow-700 space-y-1.5 list-disc list-inside">
                                    <li>Bạn chỉ được nộp bài **MỘT LẦN DUY NHẤT** trước hạn.</li>
                                    <li>Sau khi nộp, bạn **KHÔNG THỂ SỬA ĐỔI** hoặc nộp lại bài làm.</li>
                                    <li>Vui lòng kiểm tra kỹ nội dung và định dạng file trước khi xác nhận nộp.</li>
                                    <li>Hạn chót: **{{ $submitDeadline->format('d/m/Y H:i') }}**. Hệ thống sẽ tự động khóa.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-4 justify-end pt-4 border-t border-gray-100">
                        <a href="{{ route('profile.index') }}" 
                        class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i>Quay lại Hồ sơ
                        </a>
                        <button type="submit"
                                class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition duration-150 ease-in-out flex items-center gap-2 shadow-lg shadow-green-500/50 hover:shadow-xl">
                            <i class="fa-solid fa-paper-plane"></i>Nộp bài thi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hướng dẫn (Sidebar) - Phiên bản Nâng cấp --}}
            <div class="lg:col-span-1">
                <div class="sticky top-8 bg-white border border-blue-200 rounded-2xl shadow-2xl overflow-hidden">
                    {{-- Header màu nổi bật --}}
                    <div class="bg-blue-600 p-6 text-white">
                        <h3 class="font-extrabold text-xl mb-1 flex items-center gap-3">
                            <i class="fa-solid fa-lightbulb text-yellow-300 text-2xl"></i>
                            Hướng dẫn Nộp Bài
                        </h3>
                        <p class="text-sm text-blue-100">Vui lòng đọc kỹ các bước trước khi thực hiện.</p>
                    </div>
                    
                    {{-- Nội dung Hướng dẫn --}}
                    <div class="p-6">
                        <ol class="text-sm space-y-3 list-decimal list-inside text-gray-700">
                            <li class="pl-2">
                                <span class="font-semibold text-blue-700">Tải đề thi</span> và hoàn thành bài làm theo yêu cầu.
                            </li>
                            <li class="pl-2">
                                Lưu bài làm dưới dạng file **PDF** hoặc **ZIP/RAR** (đối với nhiều file hoặc code).
                            </li>
                            <li class="pl-2">
                                <span class="font-semibold text-blue-700">Đặt tên file rõ ràng</span> theo quy tắc (ví dụ: `[MãSV]_BaiThi_[TenMon].pdf`).
                            </li>
                            <li class="pl-2">
                                Sử dụng khu vực upload để **Kéo thả hoặc Chọn file** bài làm của bạn.
                            </li>
                            <li class="pl-2">
                                **Kiểm tra kỹ lưỡng** tên file, dung lượng, và các thông tin cảnh báo.
                            </li>
                            <li class="pl-2">
                                Nhấn nút **"Nộp bài thi"** (màu xanh lá) và xác nhận.
                            </li>
                            <li class="pl-2">
                                **Lưu lại** thông báo xác nhận thành công (screenshot/email) để đối chiếu.
                            </li>
                        </ol>
                    </div>

                    {{-- Footer/Ghi chú --}}
                    <div class="p-4 bg-blue-50 border-t border-blue-200">
                        <p class="text-xs font-medium text-blue-700 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-blue-500"></i>
                            Hệ thống Quản lý Bài thi - Vui lòng tuân thủ quy định để đảm bảo quyền lợi của bạn.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// ===== File upload functions =====
function displayFileName(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        // Sử dụng một hàm riêng để format dung lượng nếu cần độ chính xác cao hơn, 
        // nhưng hiện tại toFixed(2) là đủ.
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

        document.getElementById('upload-area').classList.add('hidden');
        document.getElementById('file-info').classList.remove('hidden');
        document.getElementById('file-name').textContent = fileName;
        document.getElementById('file-size').textContent = `Kích thước: ${fileSize} MB`;
    }
}

function clearFile() {
    document.getElementById('filebaithi').value = '';
    document.getElementById('upload-area').classList.remove('hidden');
    document.getElementById('file-info').classList.add('hidden');
}

function handleFileDrop(event) {
    const files = event.dataTransfer.files;
    if (files.length === 0) return;
    
    const file = files[0];
    const input = document.getElementById('filebaithi');
    
    // Validate file type
    const allowedTypes = [
        'application/pdf', 
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/zip', 
        'application/x-rar-compressed'
    ];
    
    // Kiểm tra MIME type hoặc đuôi file (cho các trình duyệt không cung cấp MIME type chính xác)
    const fileName = file.name.toLowerCase();
    const isAllowedExtension = fileName.endsWith('.pdf') || fileName.endsWith('.doc') || 
                               fileName.endsWith('.docx') || fileName.endsWith('.zip') || 
                               fileName.endsWith('.rar');
    
    if (allowedTypes.includes(file.type) || isAllowedExtension) {
        // Gán FileList cho input file
        input.files = files;
        displayFileName(input);
    } else {
        alert('File không đúng định dạng! Vui lòng chọn file PDF, DOC, DOCX, ZIP hoặc RAR.');
    }
}


// ===== Countdown Timer (Cải tiến màu sắc) =====
function startCountdown() {
    const countdownEl = document.getElementById('countdown-timer');
    if (!countdownEl) return;

    const deadline = parseInt(countdownEl.dataset.deadline) * 1000; // Convert to milliseconds

    function updateCountdown() {
        const now = Date.now();
        const timeLeft = deadline - now;
        
        // Reset classes
        countdownEl.classList.remove('text-red-500', 'text-yellow-300', 'text-red-400');
        countdownEl.classList.add('text-yellow-300'); // Default

        if (timeLeft <= 0) {
            countdownEl.innerHTML = '<span class="text-red-400 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i>ĐÃ HẾT HẠN NỘP</span>';
            clearInterval(timerInterval); // Dừng timer
            return;
        }

        // Tính toán thời gian
        const totalSeconds = Math.floor(timeLeft / 1000);
        const days = Math.floor(totalSeconds / (60 * 60 * 24));
        const hours = Math.floor((totalSeconds % (60 * 60 * 24)) / (60 * 60));
        const minutes = Math.floor((totalSeconds % (60 * 60)) / 60);
        const seconds = totalSeconds % 60;

        // Format hiển thị
        let display = '';
        
        if (days > 0) {
            display += `${days} ngày `;
        }
        
        display += `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        countdownEl.textContent = display;

        // Đổi màu khi còn ít thời gian
        if (timeLeft < 3600000) { // < 1 giờ (3,600,000 ms)
            countdownEl.classList.remove('text-yellow-300');
            countdownEl.classList.add('text-red-400'); 
        } else if (timeLeft < 86400000) { // < 1 ngày (86,400,000 ms)
            countdownEl.classList.remove('text-yellow-300');
            countdownEl.classList.add('text-yellow-300'); // Giữ nguyên yellow-300
        }
    }

    // Cập nhật ngay lập tức
    updateCountdown();
    
    // Cập nhật mỗi giây
    const timerInterval = setInterval(updateCountdown, 1000);
}

// Khởi động countdown khi trang load
document.addEventListener('DOMContentLoaded', startCountdown);
</script>
@endsection