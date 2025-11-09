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

        {{-- Hiển thị “1 đến 10 trong 58” --}}
        @php
            $from = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
            $to = min($paginator->currentPage() * $paginator->perPage(), $paginator->total());
        @endphp

        <span class="text-gray-500">
            Hiển thị từ <strong>{{ $from }}</strong> đến <strong>{{ $to }}</strong> / {{ $paginator->total() }} dòng
        </span>
    </div>
</div>
@endif
