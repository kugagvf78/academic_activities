<div class="relative {{ $class }}">
    @if($label)
        <label for="{{ $name }}" class="block mb-1 text-sm font-semibold text-gray-700">{{ $label }}</label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        class="appearance-none w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white pr-10 text-sm text-gray-700">
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $key => $text)
            <option value="{{ $key }}" {{ (string)$selected === (string)$key ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>

    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
</div>
