<div {!! isset($xShow) && !empty($xShow) ? "x-show=\"{$xShow}\"" : "" !!} class="mb-4">
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-700">{{ $label }}</label>
    <input
        type="{{ $type ?? 'text' }}"
        id="{{ $id }}"
        value="{{ $value }}"
        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 placeholder-gray-400 outline-none transition-colors"
        {{ $attributes }} />

    @include ('forum::components.form.error')
</div>
