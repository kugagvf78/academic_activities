@php
    $baseClass = "w-full border rounded-xl px-4 py-3 text-sm focus:outline-none transition bg-white";
    $inputClass = $disabled
        ? "$baseClass border-gray-200 text-gray-400 bg-gray-50 cursor-not-allowed"
        : "$baseClass border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700";
@endphp

<div class="{{ $class }}">
    @if($label)
        <label for="{{ $name }}" class="block mb-1 text-sm font-semibold text-gray-700">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="{{ $inputClass }}"
        @if($disabled) disabled @endif
    />
</div>
