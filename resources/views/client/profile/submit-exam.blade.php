@extends('layouts.client')
@section('title', 'Nộp bài thi')

@section('content')
<section class="container mx-auto px-6 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('profile.index') }}" class="text-gray-700 hover:text-blue-600">
                    <i class="fa-solid fa-user mr-2"></i>Hồ sơ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Nộp bài thi</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        {{-- Thông tin cuộc thi --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-3">{{ $dangky->tencuocthi }}</h1>
                    @if($loaidangky === 'DoiNhom')
                        <p class="text-blue-100 flex items-center gap-2 text-lg">
                            <i class="fa-solid fa-users"></i>
                            Đội: {{ $dangky->tendoithi }}
                        </p>
                    @else
                        <p class="text-blue-100 flex items-center gap-2 text-lg">
                            <i class="fa-solid fa-user"></i>
                            Thi cá nhân
                        </p>
                    @endif
                </div>
                <div class="bg-white/20 rounded-xl px-6 py-4 text-center backdrop-blur-sm">
                    <p class="text-blue-100 text-sm mb-1">Hạn nộp bài</p>
                    <p class="text-2xl font-bold">{{ $submitDeadline->format('d/m/Y') }}</p>
                    <p class="text-sm">{{ $submitDeadline->format('H:i') }}</p>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-blue-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-200">Bắt đầu</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($dangky->thoigianbatdau)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-flag-checkered"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-200">Kết thúc</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($dangky->thoigianketthuc)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-200">Còn lại</p>
                        <p class="font-semibold">
                            @php
                                $hoursLeft = now()->diffInHours($submitDeadline, false);
                            @endphp
                            @if($hoursLeft > 0)
                                {{ $hoursLeft }} giờ
                            @else
                                Đã hết hạn
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Đề thi (nếu có) --}}
        @if($dangky->madethi && $dangky->filedethi)
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-file-pdf text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">{{ $dangky->tendethi ?? 'Đề thi' }}</h3>
                        <p class="text-sm text-gray-500">
                            @if($dangky->thoigianlambai)
                                <i class="fa-solid fa-clock mr-1"></i>Thời gian: {{ $dangky->thoigianlambai }} phút
                            @endif
                            @if($dangky->diemtoida)
                                <i class="fa-solid fa-star ml-3 mr-1"></i>Điểm tối đa: {{ $dangky->diemtoida }}
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ Storage::url($dangky->filedethi) }}" 
                   target="_blank"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-download"></i>
                    Tải đề thi
                </a>
            </div>
        </div>
        @endif

        {{-- Form nộp bài --}}
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-200">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-arrow-up text-green-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Nộp bài thi</h2>
                    <p class="text-sm text-gray-500">Upload file bài làm của bạn</p>
                </div>
            </div>

            <form action="{{ route('profile.competition.submit', ['id' => $id, 'loaidangky' => $loaidangky]) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  onsubmit="return confirm('Bạn có chắc chắn muốn nộp bài? Sau khi nộp sẽ không thể sửa đổi!');">
                @csrf

                {{-- Upload file --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        File bài thi <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition"
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
                            <i class="fa-solid fa-cloud-arrow-up text-5xl text-gray-400 mb-4"></i>
                            <p class="text-gray-700 font-medium mb-2">
                                Kéo thả file vào đây hoặc
                                <label for="filebaithi" class="text-blue-600 hover:text-blue-700 cursor-pointer underline">
                                    chọn file
                                </label>
                            </p>
                            <p class="text-sm text-gray-500">
                                Định dạng: PDF, DOC, DOCX, ZIP, RAR (Tối đa 10MB)
                            </p>
                        </div>

                        <div id="file-info" class="hidden">
                            <i class="fa-solid fa-file-check text-5xl text-green-500 mb-4"></i>
                            <p class="text-gray-700 font-medium mb-2">
                                <span id="file-name"></span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <span id="file-size"></span>
                            </p>
                            <button type="button" 
                                    onclick="clearFile()"
                                    class="mt-3 text-sm text-red-600 hover:text-red-700 font-medium">
                                <i class="fa-solid fa-xmark mr-1"></i>Xóa file
                            </button>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        <i class="fa-solid fa-info-circle text-blue-500 mr-1"></i>
                        <strong>Lưu ý:</strong> Đảm bảo file của bạn đúng định dạng và không vượt quá dung lượng cho phép.
                    </p>
                </div>

                {{-- Xác nhận --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                        <div>
                            <p class="font-semibold text-yellow-800 mb-1">Lưu ý quan trọng</p>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Bạn chỉ được nộp bài <strong>MỘT LẦN DUY NHẤT</strong>.</li>
                                <li>• Sau khi nộp, bạn <strong>KHÔNG THỂ SỬA ĐỔI</strong> bài làm.</li>
                                <li>• Vui lòng kiểm tra kỹ file trước khi nộp.</li>
                                <li>• Hạn nộp bài: <strong>{{ $submitDeadline->format('d/m/Y H:i') }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 justify-end">
                    <a href="{{ route('profile.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Nộp bài thi
                    </button>
                </div>
            </form>
        </div>

        {{-- Hướng dẫn --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-6">
            <h3 class="font-bold text-blue-800 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-lightbulb"></i>
                Hướng dẫn nộp bài
            </h3>
            <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                <li>Tải đề thi và làm bài theo yêu cầu.</li>
                <li>Lưu bài làm dưới dạng file PDF hoặc nén thành ZIP/RAR nếu có nhiều file.</li>
                <li>Đặt tên file rõ ràng (VD: BaiThi_TenBan.pdf).</li>
                <li>Upload file bài làm vào form trên.</li>
                <li>Kiểm tra kỹ và nhấn "Nộp bài thi".</li>
                <li>Chờ giảng viên chấm điểm và công bố kết quả.</li>
            </ol>
        </div>
    </div>
</section>

<script>
function displayFileName(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
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
    const file = event.dataTransfer.files[0];
    const input = document.getElementById('filebaithi');
    
    // Validate file type
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/x-rar-compressed'];
    
    if (allowedTypes.includes(file.type)) {
        input.files = event.dataTransfer.files;
        displayFileName(input);
    } else {
        alert('File không đúng định dạng! Vui lòng chọn file PDF, DOC, DOCX, ZIP hoặc RAR.');
    }
}
</script>
@endsection