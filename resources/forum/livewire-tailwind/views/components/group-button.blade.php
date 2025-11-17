@php
$colorClasses = match ($intent) {
    'primary', '', null => 'text-white bg-blue-600 hover:text-white hover:bg-blue-700',
    'secondary' => 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50',
    'danger' => 'text-white bg-red-600 hover:bg-red-700'
};
@endphp

<a href="{{ $href }}" class="group-button py-2 px-5 text-base font-medium inline-flex items-center gap-x-2 -ms-px first:rounded-s-xl first:ms-0 last:rounded-e-xl focus:z-10 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors {{ $colorClasses }}" {{ $attributes }}>
    @if (isset($icon) && !empty($icon))
        @include ("forum::components.icons.{$icon}", ['size' => '5'])
    @endif
    {{ $label }}
</a>
