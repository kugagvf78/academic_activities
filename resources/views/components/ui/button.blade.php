@props([
    'color' => 'blue',
    'outline' => false,
])

@php
    $variant = $outline ? 'outline' : 'solid';

    $colorMap = [
        'blue' => [
            'solid' => 'bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white',
            'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50',
        ],
        'gray' => [
            'solid' => 'bg-gray-100 hover:bg-gray-200 text-gray-700',
            'outline' => 'border-2 border-gray-400 text-gray-700 hover:bg-gray-50',
        ],
    ];

    $classes = $colorMap[$color][$variant] ?? '';
@endphp

<button {{ $attributes->merge(['class' => "px-4 py-2 rounded-lg font-medium transition {$classes}"]) }}>
    {{ $slot }}
</button>
