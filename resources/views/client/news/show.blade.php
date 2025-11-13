@extends('layouts.client')
@section('title', $news->tieude)

@section('content')

{{-- 🌟 BREADCRUMB --}}
<section class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('client.home') }}" class="hover:text-blue-600 transition">
                <i class="fas fa-home"></i>
            </a>
            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
            <a href="{{ route('client.news.index') }}" class="hover:text-blue-600 transition">
                Tin tức
            </a>
            <i class="fas fa-chevron-right text-xs text-gray-400"></i>
            <span class="text-gray-800 font-medium line-clamp-1">{{ $news->tieude }}</span>
        </div>
    </div>
</section>

{{-- 📰 MAIN CONTENT --}}
<section class="container mx-auto px-6 py-12">
    <div class="grid lg:grid-cols-3 gap-8">
        
        {{-- MAIN ARTICLE --}}
        <article class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                
                {{-- Header --}}
                <div class="p-8 pb-6 border-b border-gray-100">
                    {{-- Category Badge --}}
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-{{ $news->category_color }}-600 text-white text-xs font-semibold px-4 py-1.5 rounded-full">
                            {{ $news->category }}
                        </span>
                        
                        {{-- Views --}}
                        <div class="flex items-center gap-1.5 text-gray-500 text-sm">
                            <i class="fas fa-eye"></i>
                            <span>{{ $news->luotxem }} lượt xem</span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        {{ $news->tieude }}
                    </h1>

                    {{-- Meta Info --}}
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar text-blue-500"></i>
                            <span>{{ $news->date_full }}</span>
                        </div>
                        
                        @if($news->tacgia)
                        <div class="flex items-center gap-2">
                            <i class="far fa-user text-blue-500"></i>
                            <span>{{ $news->tacgia }}</span>
                        </div>
                        @endif

                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="far fa-clock"></i>
                            <span>{{ $news->time_ago }}</span>
                        </div>
                    </div>

                    {{-- Contest Link (if exists) --}}
                    @if($news->tencuocthi)
                    <div class="mt-6 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-trophy text-blue-600 text-xl mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 mb-1">Tin tức liên quan đến cuộc thi:</p>
                                <p class="font-semibold text-blue-700 mb-2">{{ $news->tencuocthi }}</p>
                                
                                @if($news->thoigianbatdau && $news->thoigianketthuc)
                                <div class="flex items-center gap-4 text-xs text-gray-600">
                                    <span>
                                        <i class="far fa-calendar-check mr-1"></i>
                                        Bắt đầu: {{ \Carbon\Carbon::parse($news->thoigianbatdau)->format('d/m/Y') }}
                                    </span>
                                    <span>
                                        <i class="far fa-calendar-times mr-1"></i>
                                        Kết thúc: {{ \Carbon\Carbon::parse($news->thoigianketthuc)->format('d/m/Y') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Featured Image --}}
                <div class="relative">
                    <img src="{{ asset('images/home/banner1.png') }}" 
                         alt="{{ $news->tieude }}"
                         class="w-full h-[400px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>

                {{-- Content --}}
                <div class="p-8">
                    <div class="prose prose-lg max-w-none">
                        {!! $news->noidung !!}
                    </div>

                    {{-- Share Section --}}
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-share-alt text-blue-600 mr-2"></i>
                                Chia sẻ bài viết
                            </h3>
                            <div class="flex items-center gap-3">
                                <button onclick="shareOnFacebook()" 
                                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                    <i class="fab fa-facebook-f"></i>
                                    <span class="text-sm font-medium">Facebook</span>
                                </button>
                                <button onclick="shareOnTwitter()" 
                                        class="flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg transition">
                                    <i class="fab fa-twitter"></i>
                                    <span class="text-sm font-medium">Twitter</span>
                                </button>
                                <button onclick="copyLink()" 
                                        class="flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                                    <i class="fas fa-link"></i>
                                    <span class="text-sm font-medium">Sao chép</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="mt-8 flex gap-4">
                <a href="{{ route('client.news.index') }}" 
                   class="flex-1 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 px-6 py-4 rounded-xl font-semibold transition text-center shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại danh sách
                </a>
            </div>
        </article>

        {{-- SIDEBAR --}}
        <aside class="lg:col-span-1 space-y-8">
            
            {{-- Related News --}}
            @if($related->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-newspaper text-blue-600"></i>
                    Tin tức liên quan
                </h3>

                <div class="space-y-4">
                    @foreach($related as $item)
                    <a href="{{ route('client.news.show', $item->slug) }}" 
                       class="block group">
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-gray-50 transition">
                            <img src="{{ asset('images/home/banner1.png') }}" 
                                 alt="{{ $item->tieude }}"
                                 class="w-20 h-20 object-cover rounded-lg flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-800 group-hover:text-blue-600 transition line-clamp-2 mb-2 text-sm">
                                    {{ $item->tieude }}
                                </h4>
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="far fa-calendar text-blue-500"></i>
                                    {{ $item->date }}
                                </p>
                            </div>
                        </div>
                    </a>
                    @if(!$loop->last)
                    <div class="border-b border-gray-100"></div>
                    @endif
                    @endforeach
                </div>

                <a href="{{ route('client.news.index') }}" 
                   class="block mt-6 text-center text-blue-600 hover:text-blue-700 font-semibold text-sm">
                    Xem tất cả tin tức <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-gradient-to-br from-blue-600 to-cyan-500 rounded-2xl shadow-lg p-6 text-white">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt"></i>
                    Liên kết nhanh
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('client.events.index') }}" 
                       class="flex items-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-3 rounded-xl transition group">
                        <i class="fas fa-trophy text-white/90 group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Hội thảo & Cuộc thi</span>
                    </a>
                    <a href="{{ route('client.results.index') }}" 
                       class="flex items-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-3 rounded-xl transition group">
                        <i class="fas fa-medal text-white/90 group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Kết quả</span>
                    </a>
                    <a href="{{ route('client.contact') }}" 
                       class="flex items-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-3 rounded-xl transition group">
                        <i class="fas fa-envelope text-white/90 group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Liên hệ</span>
                    </a>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin liên hệ
                </h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-blue-600 mt-1"></i>
                        <span>Khoa Công nghệ Thông tin<br>Đại học XYZ</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fas fa-phone text-blue-600"></i>
                        <span>(028) 1234 5678</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fas fa-envelope text-blue-600"></i>
                        <span>cntt@university.edu.vn</span>
                    </p>
                </div>
            </div>

        </aside>
    </div>
</section>

{{-- 📱 SCROLL TO TOP BUTTON --}}
<button id="scrollToTop" 
        class="fixed bottom-8 right-8 bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition opacity-0 pointer-events-none z-50">
    <i class="fas fa-arrow-up"></i>
</button>

@push('scripts')
<script>
// Scroll to Top functionality
const scrollBtn = document.getElementById('scrollToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollBtn.classList.remove('opacity-0', 'pointer-events-none');
    } else {
        scrollBtn.classList.add('opacity-0', 'pointer-events-none');
    }
});

scrollBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Share functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(document.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        // Show notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
        notification.innerHTML = '<i class="fas fa-check mr-2"></i>Đã sao chép liên kết!';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    });
}
</script>

<style>
/* Prose styling for content */
.prose {
    color: #374151;
    line-height: 1.8;
}

.prose h2 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #1f2937;
}

.prose h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #1f2937;
}

.prose p {
    margin-bottom: 1.25rem;
}

.prose ul, .prose ol {
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose img {
    border-radius: 0.75rem;
    margin: 1.5rem 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose blockquote {
    border-left: 4px solid #2563eb;
    padding-left: 1rem;
    font-style: italic;
    color: #6b7280;
    margin: 1.5rem 0;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
    color: #db2777;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.prose pre code {
    background: none;
    padding: 0;
    color: inherit;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endpush

@endsection