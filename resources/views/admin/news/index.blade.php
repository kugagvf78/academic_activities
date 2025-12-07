@extends('layouts.admin')

@section('page-title', 'Quản lý Tin tức')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush


@section('breadcrumb')
    <nav class="text-sm">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
            <li>/</li>
            <li class="text-slate-600">Tin tức</li>
        </ol>
    </nav>
@endsection

@section('content')
<div x-data="newsManager()" x-init="init()" class="space-y-6">
    
    {{-- Thống kê --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Tổng tin tức</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1" x-text="stats.total"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-newspaper text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Đã xuất bản</p>
                    <p class="text-3xl font-bold text-green-600 mt-1" x-text="stats.published"></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Nháp</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1" x-text="stats.draft"></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Tổng lượt xem</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1" x-text="stats.total_views"></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-eye text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            
            {{-- Filters --}}
            <div class="flex flex-wrap gap-3 flex-1">
                {{-- Search --}}
                <div class="flex-1 min-w-[250px]">
                    <input type="text" 
                        x-model="filters.search" 
                        @input.debounce.500ms="loadNews()"
                        placeholder="Tìm kiếm tin tức..." 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                {{-- Loại tin --}}
                <select x-model="filters.loaitin" 
                    @change="loadNews()"
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tất cả loại tin</option>
                    <option value="TinTuc">Tin tức</option>
                    <option value="ThongBao">Thông báo</option>
                    <option value="SuKien">Sự kiện</option>
                </select>
                
                {{-- Trạng thái --}}
                <select x-model="filters.trangthai" 
                    @change="loadNews()"
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="Published">Đã xuất bản</option>
                    <option value="Draft">Nháp</option>
                    <option value="Archived">Lưu trữ</option>
                </select>
                
                {{-- Cuộc thi --}}
                <select x-model="filters.macuocthi" 
                    @change="loadNews()"
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tất cả cuộc thi</option>
                    <template x-for="ct in cuocthis" :key="ct.macuocthi">
                        <option :value="ct.macuocthi" x-text="ct.tencuocthi"></option>
                    </template>
                </select>
            </div>
            
            {{-- Actions --}}
            <div class="flex gap-3">
                <button @click="showModal = true; editMode = false; resetForm()"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tạo tin mới</span>
                </button>
                
                <button @click="deleteSelected()" 
                    x-show="selectedIds.length > 0"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    <span x-text="'Xóa (' + selectedIds.length + ')'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left w-px">
                            <input type="checkbox" 
                                @change="toggleSelectAll()"
                                :checked="selectedIds.length === news.length && news.length > 0"
                                class="rounded border-slate-300">
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Tiêu đề</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Loại tin</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Cuộc thi</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Trạng thái</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Lượt xem</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Ngày đăng</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                                <p class="text-slate-500 mt-2">Đang tải...</p>
                            </td>
                        </tr>
                    </template>
                    
                    <template x-if="!loading && news.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fa-solid fa-inbox text-4xl text-slate-300"></i>
                                <p class="text-slate-500 mt-2">Không có tin tức nào</p>
                            </td>
                        </tr>
                    </template>
                    
                    <template x-for="item in news" :key="item.matintuc">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 w-px">
                                <input type="checkbox" 
                                    :value="item.matintuc"
                                    @change="toggleSelect(item.matintuc)"
                                    :checked="selectedIds.includes(item.matintuc)"
                                    class="rounded border-slate-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    <p class="font-semibold text-slate-800" x-text="item.tieude"></p>
                                    <p class="text-sm text-slate-500 mt-1 line-clamp-2" x-text="item.excerpt"></p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-blue-100 text-blue-700': item.loaitin === 'TinTuc',
                                        'bg-red-100 text-red-700': item.loaitin === 'ThongBao',
                                        'bg-green-100 text-green-700': item.loaitin === 'SuKien'
                                    }"
                                    x-text="getLoaiTinLabel(item.loaitin)">
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600 whitespace-normal" x-text="item.tencuocthi || '-'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700': item.trangthai === 'Published',
                                        'bg-yellow-100 text-yellow-700': item.trangthai === 'Draft',
                                        'bg-gray-100 text-gray-700': item.trangthai === 'Archived'
                                    }"
                                    x-text="getTrangThaiLabel(item.trangthai)">
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1 text-slate-600">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                    <span class="text-sm" x-text="item.luotxem"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600" x-text="item.ngaydang_formatted"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="editNews(item.matintuc)"
                                        class="text-blue-600 hover:text-blue-700 p-2 hover:bg-blue-50 rounded transition"
                                        title="Chỉnh sửa">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button @click="deleteNews(item.matintuc)"
                                        class="text-red-600 hover:text-red-700 p-2 hover:bg-red-50 rounded transition"
                                        title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <div class="text-sm text-slate-600">
                Hiển thị <span x-text="pagination.from || 0"></span> - <span x-text="pagination.to || 0"></span> 
                trong tổng số <span x-text="pagination.total"></span> tin tức
            </div>
            <div class="flex gap-2">
                <button @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    :class="pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100'"
                    class="px-4 py-2 border border-slate-300 rounded-lg transition">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                
                <template x-for="page in paginationPages" :key="page">
                    <button @click="page !== '...' && changePage(page)"
                        :disabled="page === '...'"
                        :class="{
                            'bg-blue-600 text-white': page === pagination.current_page,
                            'hover:bg-slate-100': page !== '...' && page !== pagination.current_page,
                            'cursor-default': page === '...'
                        }"
                        class="px-4 py-2 border border-slate-300 rounded-lg transition"
                        x-text="page">
                    </button>
                </template>
                
                <button @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    :class="pagination.current_page >= pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100'"
                    class="px-4 py-2 border border-slate-300 rounded-lg transition">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    <div x-show="showModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between z-10">
                    <h3 class="text-xl font-bold text-slate-800" x-text="editMode ? 'Chỉnh sửa tin tức' : 'Tạo tin tức mới'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
                
                <form @submit.prevent="submitForm()" class="p-6 space-y-6">
                    {{-- Tiêu đề --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tiêu đề <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            x-model="form.tieude" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nhập tiêu đề tin tức">
                        <p x-show="errors.tieude" class="text-red-500 text-sm mt-1" x-text="errors.tieude"></p>
                    </div>
                    
                    {{-- Row: Loại tin, Cuộc thi, Trạng thái --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Loại tin <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.loaitin" 
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn loại tin</option>
                                <option value="TinTuc">Tin tức</option>
                                <option value="ThongBao">Thông báo</option>
                                <option value="SuKien">Sự kiện</option>
                            </select>
                            <p x-show="errors.loaitin" class="text-red-500 text-sm mt-1" x-text="errors.loaitin"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Cuộc thi
                            </label>
                            <select x-model="form.macuocthi" 
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Không liên kết</option>
                                <template x-for="ct in cuocthis" :key="ct.macuocthi">
                                    <option :value="ct.macuocthi" x-text="ct.tencuocthi"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.trangthai" 
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn trạng thái</option>
                                <option value="Draft">Nháp</option>
                                <option value="Published">Xuất bản</option>
                                <option value="Archived">Lưu trữ</option>
                            </select>
                            <p x-show="errors.trangthai" class="text-red-500 text-sm mt-1" x-text="errors.trangthai"></p>
                        </div>
                    </div>
                    
                    {{-- Nội dung --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nội dung <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="form.noidung" 
                            rows="10"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nhập nội dung tin tức..."></textarea>
                        <p x-show="errors.noidung" class="text-red-500 text-sm mt-1" x-text="errors.noidung"></p>
                    </div>
                    
                    {{-- Hình ảnh --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Hình ảnh
                        </label>
                        <input type="file" 
                            @change="handleFileUpload($event)"
                            accept="image/*"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-slate-500 mt-1">Định dạng: JPG, PNG, GIF. Tối đa 2MB</p>
                        <p x-show="errors.hinhanh" class="text-red-500 text-sm mt-1" x-text="errors.hinhanh"></p>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" 
                            @click="showModal = false"
                            class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">
                            Hủy
                        </button>
                        <button type="submit" 
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin" x-show="submitting"></i>
                            <span x-text="submitting ? 'Đang xử lý...' : (editMode ? 'Cập nhật' : 'Tạo mới')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function newsManager() {
    return {
        loading: false,
        submitting: false,
        showModal: false,
        editMode: false,
        news: [],
        cuocthis: [],
        selectedIds: [],
        stats: {
            total: 0,
            published: 0,
            draft: 0,
            archived: 0,
            total_views: 0
        },
        filters: {
            search: '',
            loaitin: 'all',
            trangthai: 'all',
            macuocthi: 'all',
            page: 1,
            per_page: 10
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
            from: 0,
            to: 0
        },
        form: {
            matintuc: '',
            tieude: '',
            noidung: '',
            loaitin: '',
            trangthai: 'Draft',
            macuocthi: '',
            hinhanh: null
        },
        errors: {},
        
        async init() {
            await this.loadStats();
            await this.loadCuocThi();
            await this.loadNews();
        },
        
        async loadStats() {
            try {
                const response = await fetch('/api/admin/news/statistics');
                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },
        
        async loadCuocThi() {
            try {
                const response = await fetch('/api/admin/news/cuocthi');
                const data = await response.json();
                if (data.success) {
                    this.cuocthis = data.data;
                }
            } catch (error) {
                console.error('Error loading cuocthi:', error);
            }
        },
        
        async loadNews() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    loaitin: this.filters.loaitin,
                    trangthai: this.filters.trangthai,
                    macuocthi: this.filters.macuocthi,
                    page: this.filters.page,
                    per_page: this.filters.per_page
                });
                
                const response = await fetch(`/api/admin/news?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.news = data.data.data;
                    this.pagination = {
                        current_page: data.data.current_page,
                        last_page: data.data.last_page,
                        per_page: data.data.per_page,
                        total: data.data.total,
                        from: data.data.from,
                        to: data.data.to
                    };
                }
            } catch (error) {
                console.error('Error loading news:', error);
                alert('Có lỗi xảy ra khi tải tin tức');
            } finally {
                this.loading = false;
            }
        },
        
        async editNews(id) {
            try {
                const response = await fetch(`/api/admin/news/${id}`);
                const data = await response.json();
                
                if (data.success) {
                    this.form = {
                        matintuc: data.data.matintuc,
                        tieude: data.data.tieude,
                        noidung: data.data.noidung,
                        loaitin: data.data.loaitin,
                        trangthai: data.data.trangthai,
                        macuocthi: data.data.macuocthi || '',
                        hinhanh: null
                    };
                    this.editMode = true;
                    this.showModal = true;
                }
            } catch (error) {
                console.error('Error loading news:', error);
                alert('Có lỗi xảy ra khi tải tin tức');
            }
        },
        
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            
            try {
                const formData = new FormData();
                formData.append('tieude', this.form.tieude);
                formData.append('noidung', this.form.noidung);
                formData.append('loaitin', this.form.loaitin);
                formData.append('trangthai', this.form.trangthai);
                if (this.form.macuocthi) formData.append('macuocthi', this.form.macuocthi);
                if (this.form.hinhanh) formData.append('hinhanh', this.form.hinhanh);
                
                let url = '/api/admin/news';
                let method = 'POST';
                
                if (this.editMode) {
                    url = `/api/admin/news/${this.form.matintuc}`;
                    formData.append('_method', 'PUT');
                }
                
                console.log('=== SUBMITTING FORM ===');
                console.log('URL:', url);
                console.log('Method:', method);
                console.log('Edit Mode:', this.editMode);
                console.log('Form Data:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}:`, value);
                }
                
                // Lấy CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                console.log('CSRF Token:', csrfToken);
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken  // Thêm CSRF token vào header
                    }
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                
                // Đọc response text trước
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                // Parse JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was:', responseText);
                    throw new Error('Server returned invalid JSON: ' + responseText.substring(0, 100));
                }
                
                console.log('Parsed response data:', data);
                
                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                        alert('Vui lòng kiểm tra lại các trường nhập liệu');
                    } else {
                        alert(data.message || `HTTP error! status: ${response.status}`);
                    }
                    return;
                }
                
                if (data.success) {
                    alert(data.message);
                    this.showModal = false;
                    this.resetForm();
                    await this.loadNews();
                    await this.loadStats();
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    alert(data.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('=== ERROR ===');
                console.error('Error object:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
                alert('Có lỗi xảy ra khi lưu tin tức: ' + error.message);
            } finally {
                this.submitting = false;
            }
        },
        
        async deleteNews(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa tin tức này?')) return;
            
            try {
                const response = await fetch(`/api/admin/news/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    await this.loadNews();
                    await this.loadStats();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa tin tức');
            }
        },
        
        async deleteSelected() {
            if (this.selectedIds.length === 0) return;
            if (!confirm(`Bạn có chắc chắn muốn xóa ${this.selectedIds.length} tin tức?`)) return;
            
            try {
                const response = await fetch('/api/admin/news/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ids: this.selectedIds
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.selectedIds = [];
                    await this.loadNews();
                    await this.loadStats();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa tin tức');
            }
        },
        
        toggleSelect(id) {
            const index = this.selectedIds.indexOf(id);
            if (index > -1) {
                this.selectedIds.splice(index, 1);
            } else {
                this.selectedIds.push(id);
            }
        },
        
        toggleSelectAll() {
            if (this.selectedIds.length === this.news.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = this.news.map(n => n.matintuc);
            }
        },
        
        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.filters.page = page;
            this.loadNews();
        },
        
        get paginationPages() {
            const pages = [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            if (last <= 7) {
                for (let i = 1; i <= last; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) pages.push(i);
                    pages.push('...');
                    pages.push(last);
                } else if (current >= last - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = last - 4; i <= last; i++) pages.push(i);
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(last);
                }
            }
            
            return pages;
        },
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2048000) {
                    alert('Kích thước file không được vượt quá 2MB');
                    event.target.value = '';
                    return;
                }
                this.form.hinhanh = file;
            }
        },
        
        resetForm() {
            this.form = {
                matintuc: '',
                tieude: '',
                noidung: '',
                loaitin: '',
                trangthai: 'Draft',
                macuocthi: '',
                hinhanh: null
            };
            this.errors = {};
            this.editMode = false;
        },
        
        getLoaiTinLabel(loaitin) {
            const labels = {
                'TinTuc': 'Tin tức',
                'ThongBao': 'Thông báo',
                'SuKien': 'Sự kiện'
            };
            return labels[loaitin] || loaitin;
        },
        
        getTrangThaiLabel(trangthai) {
            const labels = {
                'Published': 'Đã xuất bản',
                'Draft': 'Nháp',
                'Archived': 'Lưu trữ'
            };
            return labels[trangthai] || trangthai;
        }
    }
}
</script>
@endpush
@endsection