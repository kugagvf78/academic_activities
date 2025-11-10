@php
    $colorMap = [
        'blue' => [
            'solid' => 'bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white',
            'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50',
        ],
        'gray' => [
            'solid' => 'bg-gray-100 hover:bg-gray-200 text-gray-700',
            'outline' => 'border-2 border-gray-400 text-gray-700 hover:bg-gray-50',
        ],
        'red' => [
            'solid' => 'bg-red-600 hover:bg-red-700 text-white',
            'outline' => 'border-2 border-red-600 text-red-600 hover:bg-red-50',
        ],
    ];

    $style = $outline ? $colorMap[$color]['outline'] : $colorMap[$color]['solid'];
    $classes = "inline-flex items-center justify-center {$style} px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 {$class}";
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}">
        @if($icon)
            <i class="fas {{ $icon }} mr-2"></i>
        @endif
        {{ $label }}
    </a>
@else
    <button type="{{ $type }}" class="{{ $classes }}">
        @if($icon)
            <i class="fas {{ $icon }} mr-2"></i>
        @endif
        {{ $label }}
    </button>
@endif
