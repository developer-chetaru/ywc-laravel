<div {!! isset($xShow) && !empty($xShow) ? "x-show=\"{$xShow}\"" : "" !!} class="mb-4">
    @if (isset($label))
        <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <textarea
        id="{{ $id }}"
        class="block px-4 py-2.5 w-full min-h-36 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400 outline-none transition-colors resize-y"
        {{ $attributes }}></textarea>

    @include ('forum::components.form.error')
</div>
