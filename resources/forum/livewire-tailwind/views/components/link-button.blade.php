@php
$colorClasses = match ($intent) {
    'primary', '', null => 'text-white bg-blue-600 hover:text-white hover:bg-blue-700',
    'secondary' => 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50',
    'danger' => 'text-white bg-red-600 hover:bg-red-700'
};

$sizeClasses = match ($size) {
    'regular', '', null => 'min-w-36 px-4 py-2',
    'small' => 'px-4 py-1',
};
@endphp

<a href="{{ $href }}" class="link-button inline-block rounded-md font-regular text-md text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors {{ $colorClasses }} {{ $sizeClasses }}" {{ $attributes }}>
    @if (isset($icon) && !empty($icon))
        @include ("forum::components.icons.{$icon}", ['size' => '5'])
    @endif
    {{ $label }}
</a>
