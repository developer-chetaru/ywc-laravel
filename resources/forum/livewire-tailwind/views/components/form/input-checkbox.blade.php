<div {!! isset($xShow) && !empty($xShow) ? "x-show=\"{$xShow}\"" : "" !!} class="flex {{ isset($reverse) && $reverse ? 'flex-row-reverse' : '' }} items-center mb-2">
    <input
        id="{{ $id }}"
        type="checkbox"
        value="{{ $value }}"
        class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer"
        {{ $attributes }} />
    @if (isset($label))
        <label for="{{ $id }}" class="ms-2 text-sm font-medium text-gray-700 select-none cursor-pointer">{{ $label }}</label>
    @endif
</div>
