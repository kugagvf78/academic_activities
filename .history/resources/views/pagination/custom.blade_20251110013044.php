@if ($paginator->hasPages())
<div class="flex flex-col sm:flex-row items-center justify-between w-full mt-6 gap-4">

    {{-- 🔹 Bên trái: các nút phân trang --}}
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1">

        {{-- First Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed">
                <i class="fas fa-angle-double-left"></i>
            </span>
        @else
            <a href="{{ $paginator->url(1) }}"
               class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="fas fa-angle-double-left"></i>
            </a>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed">
                <i class="fas fa-angle-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="fas fa-angle-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-400">…</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-600 text-white font-semibold shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="fas fa-angle-right"></i>
            </a>
        @else
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed">
                <i class="fas fa-angle-right"></i>
            </span>
        @endif

        {{-- Last Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->url($paginator->lastPage()) }}"
               class="flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="fas fa-angle-double-right"></i>
            </a>
        @else
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed">
                <i class="fas fa-angle-double-right"></i>
            </span>
        @endif
    </nav>

    {{-- 🔹 Bên phải: chọn số dòng + mô tả --}}
    <div class="flex items-center space-x-3 text-sm text-gray-600">
        {{-- Số dòng / trang --}}
        <form method="GET" id="perPageForm" class="flex items-center space-x-2">
            <select id="perPage" name="per_page"
                    onchange="document.getElementById('perPageForm').submit()"
                    class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach ([5, 10, 25, 50] as $size)
                    <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- 🔹 Pagination --}}
@if($events->hasPages())
    <div class="mt-12">
        <div class="flex justify-center">
            <nav aria-label="Event pagination" class="pagination-wrapper">
                {!! $events->appends(request()->query())->links('pagination.custom') !!}
            </nav>
        </div>
    </div>
@else
    {{-- 🔸 Nếu không có dữ liệu --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="mb-6">
            <i class="fas fa-calendar-xmark text-6xl text-gray-300"></i>
        </div>
        <h4 class="text-xl font-semibold text-gray-600 mb-3">Không tìm thấy cuộc thi nào</h4>
        <p class="text-gray-500 mb-6">
            @if(request('search') || request('status') || request('category'))
                Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm
            @else
                Hiện tại chưa có cuộc thi nào được đăng trong hệ thống
            @endif
        </p>
        <a href="{{ route('client.events') }}"
           class="inline-flex items-center bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition">
            <i class="fas fa-rotate-right mr-2"></i>
            Làm mới trang
        </a>
    </div>
@endif

    </div>
</div>
@endif
