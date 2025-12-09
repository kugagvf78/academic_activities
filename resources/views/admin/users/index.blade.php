@extends('layouts.admin')

@section('page-title', 'Quản lý Người dùng')
@section('breadcrumb', 'Admin / Người dùng')

@section('content')
<div x-data="userManagement()" x-init="init()">
    
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Quản lý Người dùng
            </h1>
            <p class="text-slate-600 mt-1">Quản lý tất cả người dùng trong hệ thống</p>
        </div>
        {{-- <div class="flex gap-3">
            <button @click="openImportModal()" 
                class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-import"></i>
                <span>Import</span>
            </button>
            <button @click="openCreateModal()" 
                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Thêm người dùng</span>
            </button>
        </div> --}}
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 shadow-md border border-blue-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Tổng người dùng</p>
                    <p class="text-3xl font-bold text-blue-900 mt-1" x-text="stats.tong_nguoi_dung || 0"></p>
                </div>
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-users text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 shadow-md border border-green-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700 font-medium">Giảng viên</p>
                    <p class="text-3xl font-bold text-green-900 mt-1" x-text="stats.giang_vien || 0"></p>
                </div>
                <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-chalkboard-user text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 shadow-md border border-purple-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-700 font-medium">Sinh viên</p>
                    <p class="text-3xl font-bold text-purple-900 mt-1" x-text="stats.sinh_vien || 0"></p>
                </div>
                <div class="w-14 h-14 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-user-graduate text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-5 shadow-md border border-emerald-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-emerald-700 font-medium">Hoạt động</p>
                    <p class="text-3xl font-bold text-emerald-900 mt-1" x-text="stats.hoat_dong || 0"></p>
                </div>
                <div class="w-14 h-14 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-circle-check text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Section --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tìm kiếm</label>
                <div class="relative">
                    <input type="text" 
                        x-model="filters.search"
                        @input.debounce.500ms="loadUsers()"
                        placeholder="Tìm theo mã, tên, email, tên đăng nhập..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            {{-- Vai trò --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Vai trò</label>
                <select x-model="filters.vaitro" @change="loadUsers()"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Tất cả</option>
                    <option value="Admin">Admin</option>
                    <option value="GiangVien">Giảng viên</option>
                    <option value="SinhVien">Sinh viên</option>
                </select>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Trạng thái</label>
                <select x-model="filters.trangthai" @change="loadUsers()"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Tất cả</option>
                    <option value="Active">Hoạt động</option>
                    <option value="Inactive">Không hoạt động</option>
                </select>
            </div>

            {{-- Số bản ghi --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Hiển thị</label>
                <select x-model="filters.per_page" @change="loadUsers()"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <div class="flex items-end">
                <button @click="resetFilters()" 
                    class="w-full px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>Đặt lại</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        
        {{-- Loading State --}}
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-block w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-600 mt-3 font-medium">Đang tải dữ liệu...</p>
        </div>

        {{-- Table --}}
        <div x-show="!loading" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-50 to-slate-100 border-b-2 border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Mã người dùng
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Họ tên
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Số điện thoại
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Vai trò
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-if="users.data && users.data.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-semibold text-slate-600">Không tìm thấy người dùng nào</p>
                                    <p class="text-sm text-slate-500 mt-1">Thử thay đổi bộ lọc hoặc tìm kiếm</p>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-for="user in users.data" :key="user.manguoidung">
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-slate-800 bg-slate-100 px-2 py-1 rounded" x-text="user.manguoidung"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                        <span x-text="user.hoten.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800" x-text="user.hoten"></p>
                                        <p class="text-xs text-slate-500" x-text="user.tendangnhap"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <i class="fa-solid fa-envelope text-slate-400"></i>
                                    <span x-text="user.email"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <i class="fa-solid fa-phone text-slate-400"></i>
                                    <span x-text="user.sodienthoai || 'Chưa có'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                                    :class="{
                                        'bg-purple-100 text-purple-700': user.vaitro === 'Admin',
                                        'bg-blue-100 text-blue-700': user.vaitro === 'GiangVien',
                                        'bg-green-100 text-green-700': user.vaitro === 'SinhVien'
                                    }">
                                    <i class="fa-solid"
                                        :class="{
                                            'fa-user-shield': user.vaitro === 'Admin',
                                            'fa-chalkboard-user': user.vaitro === 'GiangVien',
                                            'fa-user-graduate': user.vaitro === 'SinhVien'
                                        }"></i>
                                    <span x-text="user.vaitro === 'GiangVien' ? 'Giảng viên' : (user.vaitro === 'SinhVien' ? 'Sinh viên' : user.vaitro)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                                    :class="user.trangthai === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                                    <i class="fa-solid"
                                        :class="user.trangthai === 'Active' ? 'fa-circle-check' : 'fa-circle-xmark'"></i>
                                    <span x-text="user.trangthai === 'Active' ? 'Hoạt động' : 'Không hoạt động'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewUser(user)" 
                                        class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                        title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button @click="editUser(user)" 
                                        class="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg transition-colors"
                                        title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="toggleStatus(user)" 
                                        class="p-2 hover:bg-slate-100 rounded-lg transition-colors"
                                        :class="user.trangthai === 'Active' ? 'text-orange-600' : 'text-green-600'"
                                        :title="user.trangthai === 'Active' ? 'Vô hiệu hóa' : 'Kích hoạt'">
                                        <i class="fa-solid" :class="user.trangthai === 'Active' ? 'fa-ban' : 'fa-check-circle'"></i>
                                    </button>
                                    {{-- <button @click="deleteUser(user)" 
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                        title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="!loading && users.data && users.data.length > 0" class="px-6 py-4 bg-slate-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-slate-600">
                    Hiển thị <span class="font-bold text-blue-600" x-text="users.from || 0"></span> 
                    đến <span class="font-bold text-blue-600" x-text="users.to || 0"></span> 
                    trong tổng số <span class="font-bold text-blue-600" x-text="users.total || 0"></span> người dùng
                </div>
                <div class="flex gap-2">
                    <button @click="changePage(users.current_page - 1)" 
                        :disabled="!users.prev_page_url"
                        :class="users.prev_page_url ? 'hover:bg-blue-100 hover:text-blue-600' : 'opacity-50 cursor-not-allowed'"
                        class="px-4 py-2 border border-gray-300 rounded-lg transition font-semibold">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    
                    <template x-for="page in paginationPages" :key="page">
                        <button @click="changePage(page)" 
                            :class="page === users.current_page ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg' : 'hover:bg-slate-100'"
                            class="px-4 py-2 border border-gray-300 rounded-lg transition font-semibold"
                            x-text="page">
                        </button>
                    </template>

                    <button @click="changePage(users.current_page + 1)" 
                        :disabled="!users.next_page_url"
                        :class="users.next_page_url ? 'hover:bg-blue-100 hover:text-blue-600' : 'opacity-50 cursor-not-allowed'"
                        class="px-4 py-2 border border-gray-300 rounded-lg transition font-semibold">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit User Modal --}}
    <div x-show="showModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal()"></div>

            <div x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:align-middle">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-purple-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fa-solid" :class="editMode ? 'fa-pen-to-square' : 'fa-user-plus'"></i>
                            <span x-text="editMode ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới'"></span>
                        </h3>
                        <button @click="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                    <form @submit.prevent="submitForm()">
                        
                        {{-- Thông tin cơ bản --}}
                        <div class="mb-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-user text-blue-600"></i>
                                Thông tin cơ bản
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Mã người dùng <span class="text-red-500">*</span>
                                    </label>
                                    
                                    <div class="flex gap-2">
                                        <input type="text" 
                                            x-model="formData.manguoidung"
                                            :readonly="true"
                                            :class="editMode ? 'bg-gray-100 cursor-not-allowed' : 'bg-blue-50'"
                                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-mono font-semibold"
                                            placeholder="Nhấn 'Sinh mã' để tạo tự động"
                                            required>
                                        
                                        {{-- Nút sinh mã - CHỈ hiện khi tạo mới --}}
                                        <button 
                                            x-show="!editMode"
                                            type="button" 
                                            @click="generateUserCode()"
                                            class="px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all flex items-center gap-2 whitespace-nowrap">
                                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                                            <span>Sinh mã</span>
                                        </button>
                                    </div>
                                    
                                    {{-- Thông báo mã đã sinh --}}
                                    <div x-show="autoGeneratedCode && !editMode" 
                                        x-transition
                                        class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-xs text-green-700 flex items-center gap-2">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>Mã đã được sinh tự động: <strong class="font-mono" x-text="autoGeneratedCode"></strong></span>
                                        </p>
                                    </div>
                                    
                                    {{-- Gợi ý format --}}
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-info-circle"></i>
                                        <span>Format: <strong>GV</strong>0001 (Giảng viên), <strong>SV</strong>0001 (Sinh viên), <strong>AD</strong>0001 (Admin)</span>
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Tên đăng nhập <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        x-model="formData.tendangnhap"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập tên đăng nhập"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Họ và tên <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        x-model="formData.hoten"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập họ và tên"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                        x-model="formData.email"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập email"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Số điện thoại
                                    </label>
                                    <input type="text" 
                                        x-model="formData.sodienthoai"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập số điện thoại">
                                </div>

                                <div x-show="!editMode">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Mật khẩu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" 
                                        x-model="formData.matkhau"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập mật khẩu"
                                        :required="!editMode">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Vai trò <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.vaitro" 
                                        @change="onRoleChange()"
                                        :disabled="editMode"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        required>
                                        <option value="">Chọn vai trò</option>
                                        <option value="GiangVien">Giảng viên</option>
                                        <option value="SinhVien">Sinh viên</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Trạng thái <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.trangthai" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        required>
                                        <option value="Active">Hoạt động</option>
                                        <option value="Inactive">Không hoạt động</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Thông tin giảng viên --}}
                        <div x-show="formData.vaitro === 'GiangVien'" class="mb-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-chalkboard-user text-green-600"></i>
                                Thông tin giảng viên
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Mã giảng viên <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        x-model="formData.magiangvien"
                                        :readonly="true"
                                        :disabled="editMode"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-blue-50 font-mono font-semibold"
                                        placeholder="Tự động điền cùng mã người dùng">
                                </div>


                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Bộ môn
                                    </label>
                                    <select x-model="formData.mabomon" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                        <option value="">Chọn bộ môn</option>
                                        <template x-for="bomon in bomons" :key="bomon.mabomon">
                                            <option :value="bomon.mabomon" x-text="bomon.tenbomon"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Chức vụ
                                    </label>
                                    <input type="text" 
                                        x-model="formData.chucvu"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập chức vụ">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Học vị
                                    </label>
                                    <input type="text" 
                                        x-model="formData.hocvi"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập học vị">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Chuyên môn
                                    </label>
                                    <input type="text" 
                                        x-model="formData.chuyenmon"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập chuyên môn">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" 
                                            x-model="formData.is_admin"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm font-semibold text-slate-700">Là trưởng bộ môn</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Thông tin sinh viên --}}
                        <div x-show="formData.vaitro === 'SinhVien'" class="mb-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-user-graduate text-purple-600"></i>
                                Thông tin sinh viên
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Mã sinh viên <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        x-model="formData.masinhvien"
                                        :readonly="true"
                                        :disabled="editMode"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-blue-50 font-mono font-semibold"
                                        placeholder="Tự động điền cùng mã người dùng">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Lớp
                                    </label>
                                    <select x-model="formData.malop" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                        <option value="">Chọn lớp</option>
                                        <template x-for="lop in lops" :key="lop.malop">
                                            <option :value="lop.malop" x-text="lop.tenlop"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        Năm nhập học
                                    </label>
                                    <input type="number" 
                                        x-model="formData.namnhaphoc"
                                        min="2000"
                                        max="2100"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        placeholder="Nhập năm nhập học">
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                            <button type="button" @click="closeModal()" 
                                class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-all">
                                <i class="fa-solid fa-xmark mr-2"></i>
                                Hủy bỏ
                            </button>
                            <button type="submit" 
                                :disabled="submitting"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid mr-2" :class="submitting ? 'fa-spinner fa-spin' : (editMode ? 'fa-save' : 'fa-plus')"></i>
                                <span x-text="submitting ? 'Đang xử lý...' : (editMode ? 'Cập nhật' : 'Thêm mới')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- View User Detail Modal - FIXED VERSION --}}
    <div x-show="showDetailModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeDetailModal()"></div>

            <div class="inline-block w-full max-w-3xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:align-middle">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-purple-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fa-solid fa-user-circle"></i>
                            <span>Chi tiết người dùng</span>
                        </h3>
                        <button @click="closeDetailModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                    <template x-if="selectedUser">
                        <div>
                            {{-- Avatar & Basic Info --}}
                            <div class="flex items-center gap-4 mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">

                                <div class="w-20 h-20 rounded-full overflow-hidden shadow-lg"> <img :src="selectedUser.avatar_url" class="w-full h-full object-cover" alt="Avatar"> </div>


                                <div class="flex-1">
                                    <h4 class="text-2xl font-bold text-slate-800"
                                        x-text="selectedUser.hoten || selectedUser.HoTen || 'N/A'"></h4>

                                    <p class="text-slate-600"
                                        x-text="selectedUser.email || selectedUser.Email || 'N/A'"></p>

                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-purple-100 text-purple-700': (selectedUser.vaitro || selectedUser.VaiTro) === 'Admin',
                                                'bg-blue-100 text-blue-700': (selectedUser.vaitro || selectedUser.VaiTro) === 'GiangVien',
                                                'bg-green-100 text-green-700': (selectedUser.vaitro || selectedUser.VaiTro) === 'SinhVien'
                                            }"
                                            x-text="(selectedUser.vaitro || selectedUser.VaiTro)"></span>

                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="(selectedUser.trangthai || selectedUser.TrangThai) === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                            x-text="(selectedUser.trangthai || selectedUser.TrangThai) === 'Active' ? 'Hoạt động' : 'Không hoạt động'"></span>
                                    </div>
                                </div>

                            </div>

                            {{-- User Details --}}
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-semibold text-slate-600">Mã người dùng</label>
                                        <p class="text-slate-800 font-mono font-semibold" x-text="selectedUser.manguoidung || selectedUser.MaNguoiDung || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-slate-600">Tên đăng nhập</label>
                                        <p class="text-slate-800 font-semibold" x-text="selectedUser.tendangnhap || selectedUser.TenDangNhap || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-slate-600">Số điện thoại</label>
                                        <p class="text-slate-800" x-text="selectedUser.sodienthoai || selectedUser.SoDienThoai || 'Chưa có'"></p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-slate-600">Ngày tạo</label>
                                        <p class="text-slate-800" x-text="formatDate(selectedUser.ngaytao || selectedUser.NgayTao || selectedUser.created_at)"></p>
                                    </div>
                                </div>

                                {{-- Giảng viên info - Hỗ trợ tất cả naming conventions --}}
                                <template x-if="selectedUser.giang_vien || selectedUser.giangVien || selectedUser.GiangVien">
                                    <div class="mt-6 p-4 bg-green-50 rounded-xl">
                                        <h5 class="text-lg font-bold text-green-800 mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-chalkboard-user"></i>
                                            Thông tin giảng viên
                                        </h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm font-semibold text-green-700">Mã giảng viên</label>
                                                <p class="text-slate-800 font-mono" 
                                                x-text="selectedUser.giang_vien?.magiangvien || selectedUser.giangVien?.magiangvien || selectedUser.GiangVien?.MaGiangVien || 'N/A'"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-semibold text-green-700">Bộ môn</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.giang_vien?.bo_mon?.tenbomon || selectedUser.giangVien?.boMon?.tenbomon || selectedUser.GiangVien?.BoMon?.TenBoMon || 'Chưa có'"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-semibold text-green-700">Chức vụ</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.giang_vien?.chucvu || selectedUser.giangVien?.chucvu || selectedUser.GiangVien?.ChucVu || 'Chưa có'"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-semibold text-green-700">Học vị</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.giang_vien?.hocvi || selectedUser.giangVien?.hocvi || selectedUser.GiangVien?.HocVi || 'Chưa có'"></p>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="text-sm font-semibold text-green-700">Chuyên môn</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.giang_vien?.chuyenmon || selectedUser.giangVien?.chuyenmon || selectedUser.GiangVien?.ChuyenMon || 'Chưa có'"></p>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="text-sm font-semibold text-green-700">Trưởng bộ môn</label>
                                                <p class="text-slate-800" 
                                                x-text="(selectedUser.giang_vien?.is_admin || selectedUser.giangVien?.is_admin || selectedUser.GiangVien?.IsAdmin) ? 'Có' : 'Không'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Sinh viên info - Hỗ trợ tất cả naming conventions --}}
                                <template x-if="selectedUser.sinh_vien || selectedUser.sinhVien || selectedUser.SinhVien">
                                    <div class="mt-6 p-4 bg-purple-50 rounded-xl">
                                        <h5 class="text-lg font-bold text-purple-800 mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-user-graduate"></i>
                                            Thông tin sinh viên
                                        </h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm font-semibold text-purple-700">Mã sinh viên</label>
                                                <p class="text-slate-800 font-mono" 
                                                x-text="selectedUser.sinh_vien?.masinhvien || selectedUser.sinhVien?.masinhvien || selectedUser.SinhVien?.MaSinhVien || 'N/A'"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-semibold text-purple-700">Lớp</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.sinh_vien?.lop?.tenlop || selectedUser.sinhVien?.lop?.tenlop || selectedUser.SinhVien?.Lop?.TenLop || 'Chưa có'"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-semibold text-purple-700">Năm nhập học</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.sinh_vien?.namnhaphoc || selectedUser.sinhVien?.namnhaphoc || selectedUser.SinhVien?.NamNhapHoc || 'Chưa có'"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-semibold text-purple-700">Điểm rèn luyện</label>
                                                <p class="text-slate-800" 
                                                x-text="selectedUser.sinh_vien?.diemrenluyen || selectedUser.sinhVien?.diemrenluyen || selectedUser.SinhVien?.DiemRenLuyen || 'Chưa có'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Loading State --}}
                    <template x-if="!selectedUser">
                        <div class="text-center py-8">
                            <div class="inline-block w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-slate-600 mt-3 font-medium">Đang tải thông tin...</p>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-gray-200 flex justify-end gap-3">
                    <button @click="editUser(selectedUser)" 
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all">
                        <i class="fa-solid fa-pen-to-square mr-2"></i>
                        Chỉnh sửa
                    </button>
                    <button @click="closeDetailModal()" 
                        class="px-6 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-xl font-semibold transition-all">
                        <i class="fa-solid fa-xmark mr-2"></i>
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div x-show="showImportModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeImportModal()"></div>

            <div class="inline-block w-full max-w-2xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:align-middle">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 bg-gradient-to-r from-green-600 to-emerald-600">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fa-solid fa-file-import"></i>
                            <span>Import người dùng từ Excel</span>
                        </h3>
                        <button @click="closeImportModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-6">
                    <form @submit.prevent="submitImport()">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Chọn file Excel <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                @change="handleFileSelect($event)"
                                accept=".xlsx,.xls"
                                class="w-full px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                required>
                            <p class="text-sm text-slate-500 mt-2">Chỉ chấp nhận file .xlsx hoặc .xls (tối đa 5MB)</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                            <h5 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info"></i>
                                Lưu ý:
                            </h5>
                            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                <li>File Excel phải có đúng định dạng mẫu</li>
                                <li>Các cột bắt buộc: Mã người dùng, Tên đăng nhập, Họ tên, Email, Vai trò</li>
                                <li>Dữ liệu trùng lặp sẽ bị bỏ qua</li>
                            </ul>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-download"></i>
                                Tải file mẫu
                            </a>
                            <div class="flex gap-3">
                                <button type="button" @click="closeImportModal()" 
                                    class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-all">
                                    <i class="fa-solid fa-xmark mr-2"></i>
                                    Hủy bỏ
                                </button>
                                <button type="submit" 
                                    :disabled="importing"
                                    class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid mr-2" :class="importing ? 'fa-spinner fa-spin' : 'fa-file-import'"></i>
                                    <span x-text="importing ? 'Đang import...' : 'Import'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0"
        class="fixed top-4 right-4 z-50 max-w-md"
        style="display: none;">
        <div class="rounded-xl shadow-2xl overflow-hidden"
            :class="{
                'bg-green-500': toast.type === 'success',
                'bg-red-500': toast.type === 'error',
                'bg-yellow-500': toast.type === 'warning',
                'bg-blue-500': toast.type === 'info'
            }">
            <div class="p-4 flex items-start gap-3">
                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                    <i class="fa-solid text-white text-lg"
                        :class="{
                            'fa-circle-check': toast.type === 'success',
                            'fa-circle-xmark': toast.type === 'error',
                            'fa-triangle-exclamation': toast.type === 'warning',
                            'fa-circle-info': toast.type === 'info'
                        }"></i>
                </div>
                <div class="flex-1">
                    <p class="text-white font-semibold" x-text="toast.message"></p>
                </div>
                <button @click="toast.show = false" class="flex-shrink-0 text-white hover:bg-white hover:bg-opacity-20 rounded p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userManagement() {
    return {
        loading: false,
        submitting: false,
        importing: false,
        users: {},
        stats: {},
        lops: [],
        bomons: [],
        selectedUser: null,
        showModal: false,
        showDetailModal: false,
        showImportModal: false,
        editMode: false,
        importFile: null,
        autoGeneratedCode: '', // THÊM biến lưu mã tự động
        filters: {
            search: '',
            vaitro: '',
            trangthai: '',
            per_page: 15
        },
        formData: {
            manguoidung: '',
            tendangnhap: '',
            matkhau: '',
            hoten: '',
            email: '',
            sodienthoai: '',
            anhdaidien: null,
            vaitro: '',
            trangthai: 'Active',
            // Giảng viên
            magiangvien: '',
            mabomon: '',
            chucvu: '',
            hocvi: '',
            chuyenmon: '',
            is_admin: false,
            // Sinh viên
            masinhvien: '',
            malop: '',
            namnhaphoc: null
        },
        toast: {
            show: false,
            message: '',
            type: 'info'
        },

        async init() {
            await this.loadStats();
            await this.loadUsers();
            await this.loadLops();
            await this.loadBoMons();
        },

        // ✅ HÀM SINH MÃ TỰ ĐỘNG
        async generateUserCode(vaitro = '') {
            try {
                const roleToUse = vaitro || this.formData.vaitro || 'Admin';
                
                this.showToast('Đang sinh mã...', 'info');
                
                const response = await fetch('/api/admin/users/generate-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ vaitro: roleToUse })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.formData.manguoidung = data.code;
                    this.autoGeneratedCode = data.code;
                    
                    // Tự động điền mã giảng viên/sinh viên
                    if (roleToUse === 'GiangVien') {
                        this.formData.magiangvien = data.code;
                    } else if (roleToUse === 'SinhVien') {
                        this.formData.masinhvien = data.code;
                    }
                    
                    this.showToast(`✅ Mã đã được sinh: ${data.code}`, 'success');
                    console.log('✅ Sinh mã thành công:', data);
                } else {
                    this.showToast('Lỗi: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('❌ Lỗi sinh mã:', error);
                this.showToast('Lỗi khi sinh mã', 'error');
            }
        },

        // ✅ MỞ MODAL TẠO MỚI
        async openCreateModal() {
            this.editMode = false;
            this.resetFormData();
            this.showModal = true;
            
            // Đợi modal render xong
            await this.$nextTick();
            
            // Tự động sinh mã Admin mặc định
            await this.generateUserCode('Admin');
        },

        // ✅ KHI THAY ĐỔI VAI TRÒ
        async onRoleChange() {
            // Reset role-specific fields
            if (this.formData.vaitro !== 'GiangVien') {
                this.formData.magiangvien = '';
                this.formData.mabomon = '';
                this.formData.chucvu = '';
                this.formData.hocvi = '';
                this.formData.chuyenmon = '';
                this.formData.is_admin = false;
            }
            if (this.formData.vaitro !== 'SinhVien') {
                this.formData.masinhvien = '';
                this.formData.malop = '';
                this.formData.namnhaphoc = null;
            }
            
            // ✅ Sinh lại mã khi đổi vai trò (CHỈ khi tạo mới)
            if (!this.editMode && this.formData.vaitro) {
                await this.generateUserCode(this.formData.vaitro);
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/api/admin/users/statistics');
                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        async loadUsers(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: page,
                    per_page: this.filters.per_page,
                    ...(this.filters.search && { search: this.filters.search }),
                    ...(this.filters.vaitro && { vaitro: this.filters.vaitro }),
                    ...(this.filters.trangthai && { trangthai: this.filters.trangthai })
                });

                const response = await fetch(`/api/admin/users?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    data.data.data = data.data.data.map(u => ({
                    ...u,
                    avatar_url: u.avatar_url 
                        ? u.avatar_url 
                        : '/images/users/avt.jpg'
                    }));
                    this.users = data.data;
                }
            } catch (error) {
                console.error('Error loading users:', error);
                this.showToast('Lỗi khi tải danh sách người dùng', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadLops() {
            try {
                const response = await fetch('/api/admin/users/lops');
                const data = await response.json();
                if (data.success) {
                    this.lops = data.data;
                }
            } catch (error) {
                console.error('Error loading lops:', error);
            }
        },

        async loadBoMons() {
            try {
                const response = await fetch('/api/admin/users/bomons');
                const data = await response.json();
                if (data.success) {
                    this.bomons = data.data;
                }
            } catch (error) {
                console.error('Error loading bomons:', error);
            }
        },

        get paginationPages() {
            if (!this.users.last_page) return [];
            
            const current = this.users.current_page;
            const last = this.users.last_page;
            const delta = 2;
            const pages = [];
            
            for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
                pages.push(i);
            }
            
            return pages;
        },

        changePage(page) {
            if (page >= 1 && page <= this.users.last_page) {
                this.loadUsers(page);
            }
        },

        resetFilters() {
            this.filters = {
                search: '',
                vaitro: '',
                trangthai: '',
                per_page: 15
            };
            this.loadUsers();
        },

        openEditModal(user) {
            this.editMode = true;
            this.selectedUser = user;
            this.formData = {
                manguoidung: user.manguoidung,
                tendangnhap: user.tendangnhap,
                hoten: user.hoten,
                email: user.email,
                sodienthoai: user.sodienthoai,
                vaitro: user.vaitro,
                trangthai: user.trangthai,
                magiangvien: user.giang_vien?.magiangvien || '',
                mabomon: user.giang_vien?.mabomon || '',
                chucvu: user.giang_vien?.chucvu || '',
                hocvi: user.giang_vien?.hocvi || '',
                chuyenmon: user.giang_vien?.chuyenmon || '',
                is_admin: user.giang_vien?.is_admin || false,
                masinhvien: user.sinh_vien?.masinhvien || '',
                malop: user.sinh_vien?.malop || '',
                namnhaphoc: user.sinh_vien?.namnhaphoc || null
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetFormData();
        },

        resetFormData() {
            this.formData = {
                manguoidung: '',
                tendangnhap: '',
                matkhau: '',
                hoten: '',
                email: '',
                sodienthoai: '',
                vaitro: '',
                trangthai: 'Active',
                magiangvien: '',
                mabomon: '',
                chucvu: '',
                hocvi: '',
                chuyenmon: '',
                is_admin: false,
                masinhvien: '',
                malop: '',
                namnhaphoc: null
            };
            this.autoGeneratedCode = ''; // ✅ Reset mã đã sinh
        },

        async submitForm() {
            this.submitting = true;
            try {
                const url = this.editMode 
                    ? `/api/admin/users/${this.formData.manguoidung}`
                    : '/api/admin/users';
                
                const method = this.editMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showToast(
                        this.editMode ? 'Cập nhật người dùng thành công' : 'Thêm người dùng thành công', 
                        'success'
                    );
                    this.closeModal();
                    await this.loadUsers();
                    await this.loadStats();
                } else {
                    this.showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                this.showToast('Lỗi khi xử lý dữ liệu', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async viewUser(user) {
            try {
                this.selectedUser = null;
                this.showDetailModal = true;
                
                const response = await fetch(`/api/admin/users/${user.manguoidung}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    this.selectedUser = {
                        ...data.data,
                        avatar_url: data.data.avatar_url 
                            ? data.data.avatar_url 
                            : '/images/users/avt.jpg'
                    };
                } else {
                    throw new Error(data.message || 'Không thể tải thông tin');
                }
            } catch (error) {
                console.error('Error loading user detail:', error);
                this.closeDetailModal();
                this.showToast('Lỗi: ' + error.message, 'error');
            }
        },

        closeDetailModal() {
            this.showDetailModal = false;
            setTimeout(() => {
                this.selectedUser = null;
            }, 300);
        },

        editUser(user) {
            this.openEditModal(user);
        },

        async toggleStatus(user) {
            const newStatus = user.trangthai === 'Active' ? 'Inactive' : 'Active';
            const action = newStatus === 'Active' ? 'kích hoạt' : 'vô hiệu hóa';
            
            if (!confirm(`Bạn có chắc chắn muốn ${action} người dùng "${user.hoten}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/admin/users/${user.manguoidung}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showToast(`${action.charAt(0).toUpperCase() + action.slice(1)} người dùng thành công`, 'success');
                    await this.loadUsers();
                    await this.loadStats();
                } else {
                    this.showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error toggling status:', error);
                this.showToast('Lỗi khi cập nhật trạng thái', 'error');
            }
        },

        async deleteUser(user) {
            if (!confirm(`Bạn có chắc chắn muốn xóa người dùng "${user.hoten}"?\n\nThao tác này không thể hoàn tác!`)) {
                return;
            }

            try {
                const response = await fetch(`/api/admin/users/${user.manguoidung}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showToast('Xóa người dùng thành công', 'success');
                    await this.loadUsers();
                    await this.loadStats();
                } else {
                    this.showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                this.showToast('Lỗi khi xóa người dùng', 'error');
            }
        },

        openImportModal() {
            this.showImportModal = true;
        },

        closeImportModal() {
            this.showImportModal = false;
            this.importFile = null;
        },

        handleFileSelect(event) {
            this.importFile = event.target.files[0];
        },

        async submitImport() {
            if (!this.importFile) {
                this.showToast('Vui lòng chọn file', 'warning');
                return;
            }

            this.importing = true;
            try {
                const formData = new FormData();
                formData.append('file', this.importFile);

                const response = await fetch('/api/admin/users/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showToast('Import dữ liệu thành công', 'success');
                    this.closeImportModal();
                    await this.loadUsers();
                    await this.loadStats();
                } else {
                    this.showToast(data.message || 'Có lỗi xảy ra khi import', 'error');
                }
            } catch (error) {
                console.error('Error importing:', error);
                this.showToast('Lỗi khi import dữ liệu', 'error');
            } finally {
                this.importing = false;
            }
        },

        formatDate(dateString) {
            if (!dateString) return 'Chưa có';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        showToast(message, type = 'info') {
            this.toast = {
                show: true,
                message: message,
                type: type
            };

            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        }
    }
}
</script>
@endpush
@endsection