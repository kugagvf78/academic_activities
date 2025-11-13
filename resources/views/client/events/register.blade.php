@extends('layouts.client')
@section('title', 'Đăng ký tham gia cuộc thi')

@section('content')

{{-- HEADER SECTION --}}
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white pt-24 pb-28 overflow-hidden">
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Đăng ký tham gia cuộc thi</h1>
        <p class="text-blue-100 text-lg">Tham gia ngay để khẳng định bản lĩnh và chinh phục đỉnh cao tri thức</p>
    </div>

    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#ffffff" d="M0,64L80,74.7C160,85,320,107,480,117.3C640,128,800,128,960,117.3C1120,107,1280,85,1360,74.7L1440,64V120H0Z" />
        </svg>
    </div>
</section>

{{-- TOAST NOTIFICATION - FIXED POSITION TOP RIGHT --}}
@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show"
     x-init="
        setTimeout(() => { show = false }, 5000);
     "
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-full"
     class="fixed top-6 right-6 z-50 min-w-[320px] max-w-md">
    
    <div class="bg-white rounded-xl shadow-2xl border border-green-100 overflow-hidden">
        {{-- Progress bar --}}
        <div class="h-1 bg-green-500 animate-[shrink_5s_linear_forwards]"></div>
        
        <div class="p-4 flex items-start gap-3">
            {{-- Icon --}}
            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
            </div>
            
            {{-- Content --}}
            <div class="flex-1 pt-0.5">
                <h4 class="font-bold text-gray-900 mb-1">Đăng xuất thành công!</h4>
                <p class="text-sm text-gray-600">{{ session('success') }}</p>
            </div>
            
            {{-- Close button --}}
            <button @click="show = false" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes shrink {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
@endif

{{-- FORM SECTION --}}
<section class="container mx-auto px-6 py-16">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-10"
         x-data="registrationForm()">

        {{-- ERROR MESSAGE --}}
        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <p class="font-semibold mb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Có lỗi xảy ra:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Title --}}
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Thông tin đăng ký cuộc thi</h2>
            <p class="text-gray-500">Vui lòng điền đầy đủ thông tin để hoàn tất đăng ký.</p>
        </div>

        <form action="{{ route('client.events.register.submit', $slug) }}" method="POST">
            @csrf

            {{-- Thông tin cuộc thi --}}
            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-2">Tên cuộc thi</label>
                <input type="text" value="{{ $cuocthi->tencuocthi }}" readonly
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-600 font-medium">
            </div>

            {{-- Hình thức tham gia --}}
            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-3">Hình thức thi</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="individual" x-model="type"
                            class="text-blue-600 focus:ring-blue-500" required>
                        <span class="text-gray-700 font-medium">Cá nhân</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="team" x-model="type"
                            class="text-blue-600 focus:ring-blue-500" required>
                        <span class="text-gray-700 font-medium">Theo nhóm</span>
                    </label>
                </div>
            </div>

            {{-- Tên đội (BẮT BUỘC cho cả cá nhân và nhóm) --}}
            <div class="mb-8">
                <label class="block font-semibold text-gray-700 mb-2">
                    Tên đội thi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="team_name" x-model="teamName" required
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                    placeholder="Nhập tên đội của bạn"
                    value="{{ old('team_name') }}">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fa-solid fa-info-circle"></i>
                    <span x-show="type === 'individual'">Tên đội cho cá nhân (ví dụ: "Đội Nguyễn Văn A")</span>
                    <span x-show="type === 'team'">Tên đội cho nhóm của bạn</span>
                </p>
            </div>

            {{-- Thông tin thí sinh chính --}}
            <div class="mb-10">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                    <span x-show="type === 'individual'">Thông tin thí sinh</span>
                    <span x-show="type === 'team'">Thông tin trưởng nhóm</span>
                </h3>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Họ và tên <span class="text-red-500">*</span></label>
                        <input type="text" name="main_name" value="{{ old('main_name') }}" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                            placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Mã số sinh viên <span class="text-red-500">*</span></label>
                        <input type="text" name="main_student_code" value="{{ old('main_student_code') }}" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                            placeholder="2024001234"
                            @blur="checkStudentCode($event.target.value)">
                        <p x-show="studentCodeError" x-text="studentCodeError" class="text-red-500 text-xs mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Email sinh viên <span class="text-red-500">*</span></label>
                        <input type="email" name="main_email" value="{{ old('main_email') }}" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                            placeholder="student@example.com">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" name="main_phone" value="{{ old('main_phone') }}" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                            placeholder="0912345678">
                    </div>
                </div>
            </div>

            {{-- Thành viên nhóm (CHỈ HIỆN KHI CHỌN TEAM) --}}
            <div x-show="type === 'team'" x-transition class="mb-10">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                    Thành viên nhóm
                    <span class="text-sm font-normal text-gray-500">(Ngoài trưởng nhóm)</span>
                </h3>

                <template x-for="(member, index) in members" :key="index">
                    <div class="grid md:grid-cols-3 gap-5 mb-5 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" :name="'members[' + index + '][name]'" x-model="member.name" 
                                :required="type === 'team'"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                                placeholder="Nguyễn Văn B">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Mã SV <span class="text-red-500">*</span></label>
                            <input type="text" :name="'members[' + index + '][student_code]'" x-model="member.student_code" 
                                :required="type === 'team'"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                                placeholder="2024001235">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" :name="'members[' + index + '][email]'" x-model="member.email" 
                                :required="type === 'team'"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                                placeholder="student2@example.com">
                        </div>
                    </div>
                </template>

                {{-- Add/Remove Buttons --}}
                <div class="flex justify-between items-center mt-4">
                    <button type="button" @click="addMember()"
                        class="text-blue-600 hover:text-blue-800 text-sm font-semibold inline-flex items-center">
                        <i class="fa-solid fa-user-plus mr-1"></i>Thêm thành viên
                    </button>

                    <button type="button" @click="removeMember()" x-show="members.length > 0"
                        class="text-red-600 hover:text-red-700 text-sm font-semibold inline-flex items-center">
                        <i class="fa-solid fa-user-minus mr-1"></i>Xóa thành viên
                    </button>
                </div>
            </div>

            {{-- Ghi chú --}}
            <div class="mt-10">
                <label class="block text-gray-600 text-sm mb-1">Ghi chú (nếu có)</label>
                <textarea name="note" rows="3"
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500"
                    placeholder="Nhập ghi chú nếu có...">{{ old('note') }}</textarea>
            </div>

            {{-- Submit Button --}}
            <div class="mt-10 text-center">
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md hover:shadow-xl transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Gửi đăng ký
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
function registrationForm() {
    return {
        type: 'individual',
        teamName: '',
        members: [],
        studentCodeError: '',

        init() {
            @if(old('type'))
                this.type = '{{ old('type') }}';
            @endif
            
            @if(old('team_name'))
                this.teamName = '{{ old('team_name') }}';
            @endif
            
            @if(old('type') === 'team' && old('members'))
                @foreach(old('members') as $index => $member)
                    this.members.push({
                        name: '{{ $member['name'] ?? '' }}',
                        student_code: '{{ $member['student_code'] ?? '' }}',
                        email: '{{ $member['email'] ?? '' }}'
                    });
                @endforeach
            @endif

            this.$watch('type', (value) => {
                if (value === 'team') {
                    if (this.members.length === 0) {
                        this.addMember();
                    }
                } else {
                    this.members = [];
                }
            });
        },

        addMember() {
            this.members.push({ name: '', student_code: '', email: '' });
        },

        removeMember() {
            if (this.members.length > 0) {
                this.members.pop();
            }
        },

        async checkStudentCode(code) {
            if (!code) {
                this.studentCodeError = '';
                return;
            }

            try {
                const response = await fetch('{{ route("client.events.check.student") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ student_code: code })
                });

                const data = await response.json();

                if (!data.exists) {
                    this.studentCodeError = 'Mã sinh viên không tồn tại trong hệ thống';
                } else {
                    this.studentCodeError = '';
                }
            } catch (error) {
                console.error('Error:', error);
                this.studentCodeError = 'Lỗi kiểm tra mã sinh viên';
            }
        }
    }
}
</script>
@endpush

@endsection