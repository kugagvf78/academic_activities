@extends('layouts.admin')

@section('page-title', 'Quản lý Cuộc thi')
@section('breadcrumb', 'Admin / Cuộc thi')

@section('content')
<div x-data="competitionManager()" x-init="init()">
    
    {{-- Header Actions --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-800">Danh sách cuộc thi</h3>
            <p class="text-sm text-slate-600">Quản lý tất cả cuộc thi trong hệ thống</p>
        </div>
        <button @click="openModal('create')" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>Tạo cuộc thi mới</span>
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Tổng cuộc thi</p>
                    <p class="text-2xl font-bold text-slate-800" x-text="statistics.total || 0"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Chuẩn bị</p>
                    <p class="text-2xl font-bold text-slate-800" x-text="statistics.chuanbi || 0"></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Đang diễn ra</p>
                    <p class="text-2xl font-bold text-slate-800" x-text="statistics.dangdienra || 0"></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-play text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-slate-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Kết thúc</p>
                    <p class="text-2xl font-bold text-slate-800" x-text="statistics.ketthuc || 0"></p>
                </div>
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-flag-checkered text-slate-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" 
                    x-model="filters.search"
                    @input.debounce.500ms="fetchCompetitions()"
                    placeholder="Tìm kiếm theo tên cuộc thi..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select x-model="filters.status" 
                    @change="fetchCompetitions()"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả trạng thái</option>
                    <option value="ChuanBi">Chuẩn bị</option>
                    <option value="DangDienRa">Đang diễn ra</option>
                    <option value="KetThuc">Kết thúc</option>
                    <option value="DaHuy">Đã hủy</option>
                </select>
            </div>
            <div>
                <select x-model="filters.per_page" 
                    @change="fetchCompetitions()"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="10">10 / trang</option>
                    <option value="25">25 / trang</option>
                    <option value="50">50 / trang</option>
                    <option value="100">100 / trang</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        
        {{-- Bulk Actions --}}
        <div x-show="selectedIds.length > 0" 
            class="bg-blue-50 border-b border-blue-200 px-4 py-3 flex items-center justify-between">
            <span class="text-sm text-slate-700">
                Đã chọn <strong x-text="selectedIds.length"></strong> cuộc thi
            </span>
            <button @click="bulkDelete()" 
                class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                <i class="fa-solid fa-trash mr-2"></i>Xóa đã chọn
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" 
                                @change="toggleSelectAll($event)"
                                :checked="selectedIds.length === competitions.length && competitions.length > 0"
                                class="rounded border-slate-300">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Tên cuộc thi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Loại</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Thời gian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Thống kê</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                                <p class="text-slate-600 mt-2">Đang tải dữ liệu...</p>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading && competitions.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-2"></i>
                                <p class="text-slate-600">Không có cuộc thi nào</p>
                            </td>
                        </tr>
                    </template>

                    <template x-for="competition in competitions" :key="competition.macuocthi">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <input type="checkbox" 
                                    :value="competition.macuocthi"
                                    @change="toggleSelect(competition.macuocthi)"
                                    :checked="selectedIds.includes(competition.macuocthi)"
                                    class="rounded border-slate-300">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <template x-if="competition.hinhanh">
                                        <img :src="competition.hinhanh" 
                                            class="w-12 h-12 rounded-lg object-cover"
                                            :alt="competition.tencuocthi">
                                    </template>
                                    <template x-if="!competition.hinhanh">
                                        <div class="w-12 h-12 bg-slate-200 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-trophy text-slate-400"></i>
                                        </div>
                                    </template>
                                    <div>
                                        <p class="font-medium text-slate-800" x-text="competition.tencuocthi"></p>
                                        <p class="text-xs text-slate-500" x-text="'#' + competition.macuocthi"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-700" x-text="competition.loaicuocthi || '-'"></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <p class="text-slate-700" x-text="formatDate(competition.thoigianbatdau)"></p>
                                    <p class="text-slate-500 text-xs" x-text="formatDate(competition.thoigianketthuc)"></p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="getStatusClass(competition.trangthai)" 
                                    class="px-3 py-1 rounded-full text-xs font-medium inline-block"
                                    x-text="getStatusText(competition.trangthai)"></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm space-y-1">
                                    <p class="text-slate-600">
                                        <i class="fa-solid fa-users text-xs mr-1"></i>
                                        <span x-text="competition.participants_count || 0"></span> đăng ký
                                    </p>
                                    <p class="text-slate-600">
                                        <i class="fa-solid fa-file-alt text-xs mr-1"></i>
                                        <span x-text="competition.submissions_count || 0"></span> bài làm
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewCompetition(competition)" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button @click="openModal('edit', competition)" 
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="Sửa">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button @click="deleteCompetition(competition.macuocthi)" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
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
        <div x-show="pagination.last_page > 1" 
            class="px-4 py-3 border-t border-slate-200 flex items-center justify-between">
            <div class="text-sm text-slate-600">
                Hiển thị <span x-text="pagination.from"></span> - <span x-text="pagination.to"></span> 
                trong tổng số <span x-text="pagination.total"></span> cuộc thi
            </div>
            <div class="flex gap-2">
                <button @click="changePage(pagination.current_page - 1)" 
                    :disabled="pagination.current_page === 1"
                    :class="pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100'"
                    class="px-3 py-1 border border-slate-300 rounded-lg transition">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                
                <template x-for="page in getPages()" :key="page">
                    <button @click="changePage(page)" 
                        :class="page === pagination.current_page ? 'bg-blue-600 text-white' : 'hover:bg-slate-100'"
                        class="px-3 py-1 border border-slate-300 rounded-lg transition"
                        x-text="page"></button>
                </template>

                <button @click="changePage(pagination.current_page + 1)" 
                    :disabled="pagination.current_page === pagination.last_page"
                    :class="pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100'"
                    class="px-3 py-1 border border-slate-300 rounded-lg transition">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    <div x-show="modal.open" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="closeModal()">
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeModal()"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 animate-fadeIn">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-slate-800" 
                        x-text="modal.mode === 'create' ? 'Tạo cuộc thi mới' : 'Sửa cuộc thi'"></h3>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Tên cuộc thi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                            x-model="form.tencuocthi"
                            required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nhập tên cuộc thi">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mô tả</label>
                        <textarea x-model="form.mota"
                            rows="3"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nhập mô tả cuộc thi"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Thời gian bắt đầu <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                x-model="form.thoigianbatdau"
                                required
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Thời gian kết thúc <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                x-model="form.thoigianketthuc"
                                required
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.trangthai"
                                required
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="ChuanBi">Chuẩn bị</option>
                                <option value="DangDienRa">Đang diễn ra</option>
                                <option value="KetThuc">Kết thúc</option>
                                <option value="DaHuy">Đã hủy</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Loại cuộc thi</label>
                            <input type="text" 
                                x-model="form.loaicuocthi"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ví dụ: Học thuật, Thể thao...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">URL Hình ảnh</label>
                        <input type="url" 
                            x-model="form.hinhanh"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="https://example.com/image.jpg">
                        <template x-if="form.hinhanh">
                            <img :src="form.hinhanh" class="mt-2 w-32 h-32 object-cover rounded-lg">
                        </template>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" 
                            @click="closeModal()"
                            class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">
                            Hủy
                        </button>
                        <button type="submit" 
                            :disabled="submitting"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                            <i class="fa-solid fa-spinner fa-spin mr-2" x-show="submitting"></i>
                            <span x-text="modal.mode === 'create' ? 'Tạo mới' : 'Cập nhật'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function competitionManager() {
    return {
        competitions: [],
        statistics: {},
        loading: false,
        submitting: false,
        selectedIds: [],
        filters: {
            search: '',
            status: '',
            per_page: 10,
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            from: 0,
            to: 0,
        },
        modal: {
            open: false,
            mode: 'create', // 'create' or 'edit'
        },
        form: {
            macuocthi: null,
            tencuocthi: '',
            mota: '',
            thoigianbatdau: '',
            thoigianketthuc: '',
            trangthai: 'ChuanBi',
            loaicuocthi: '',
            hinhanh: '',
        },

        init() {
            this.fetchStatistics();
            this.fetchCompetitions();
        },

        async fetchStatistics() {
            try {
                const response = await fetch('/api/admin/competitions/statistics', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.statistics = data.data.stats;
                }
            } catch (error) {
                console.error('Error fetching statistics:', error);
            }
        },

        async fetchCompetitions() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.filters.per_page,
                    search: this.filters.search,
                    status: this.filters.status,
                });

                const response = await fetch(`/api/admin/competitions?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.competitions = data.data;
                    this.pagination = data.pagination;
                }
            } catch (error) {
                console.error('Error fetching competitions:', error);
                alert('Có lỗi xảy ra khi tải dữ liệu');
            } finally {
                this.loading = false;
            }
        },

        openModal(mode, competition = null) {
            this.modal.mode = mode;
            this.modal.open = true;
            
            if (mode === 'edit' && competition) {
                this.form = {
                    macuocthi: competition.macuocthi,
                    tencuocthi: competition.tencuocthi,
                    mota: competition.mota || '',
                    thoigianbatdau: competition.thoigianbatdau?.substring(0, 16) || '',
                    thoigianketthuc: competition.thoigianketthuc?.substring(0, 16) || '',
                    trangthai: competition.trangthai,
                    loaicuocthi: competition.loaicuocthi || '',
                    hinhanh: competition.hinhanh || '',
                };
            } else {
                this.resetForm();
            }
        },

        closeModal() {
            this.modal.open = false;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                macuocthi: null,
                tencuocthi: '',
                mota: '',
                thoigianbatdau: '',
                thoigianketthuc: '',
                trangthai: 'ChuanBi',
                loaicuocthi: '',
                hinhanh: '',
            };
        },

        async submitForm() {
            this.submitting = true;
            try {
                const url = this.modal.mode === 'create' 
                    ? '/api/admin/competitions'
                    : `/api/admin/competitions/${this.form.macuocthi}`;
                
                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closeModal();
                    this.fetchCompetitions();
                    this.fetchStatistics();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Có lỗi xảy ra khi lưu dữ liệu');
            } finally {
                this.submitting = false;
            }
        },

        async deleteCompetition(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa cuộc thi này?')) return;

            try {
                const response = await fetch(`/api/admin/competitions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.fetchCompetitions();
                    this.fetchStatistics();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error deleting competition:', error);
                alert('Có lỗi xảy ra khi xóa cuộc thi');
            }
        },

        async bulkDelete() {
            if (!confirm(`Bạn có chắc chắn muốn xóa ${this.selectedIds.length} cuộc thi đã chọn?`)) return;

            try {
                const response = await fetch('/api/admin/competitions/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: this.selectedIds })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.selectedIds = [];
                    this.fetchCompetitions();
                    this.fetchStatistics();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error bulk deleting:', error);
                alert('Có lỗi xảy ra khi xóa cuộc thi');
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

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedIds = this.competitions.map(c => c.macuocthi);
            } else {
                this.selectedIds = [];
            }
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchCompetitions();
            }
        },

        getPages() {
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

        viewCompetition(competition) {
            // Có thể mở modal xem chi tiết hoặc chuyển trang
            alert('Xem chi tiết cuộc thi: ' + competition.tencuocthi);
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        getStatusClass(status) {
            const classes = {
                'ChuanBi': 'bg-yellow-100 text-yellow-800',
                'DangDienRa': 'bg-green-100 text-green-800',
                'KetThuc': 'bg-slate-100 text-slate-800',
                'DaHuy': 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-slate-100 text-slate-800';
        },

        getStatusText(status) {
            const texts = {
                'ChuanBi': 'Chuẩn bị',
                'DangDienRa': 'Đang diễn ra',
                'KetThuc': 'Kết thúc',
                'DaHuy': 'Đã hủy',
            };
            return texts[status] || status;
        }
    }
}
</script>
@endpush