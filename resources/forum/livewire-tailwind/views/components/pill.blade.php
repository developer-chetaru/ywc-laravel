<span class="inline-block
    rounded-full
    text-base
    text-nowrap
    align-middle
    {{ $bgColor ?? 'bg-gray-200' }}
    {{ $textColor ?? 'text-gray-700' }}
    {{ $padding ?? 'px-2' }}
    {{ $margin ?? 'mx-2' }}">
    @if (isset($icon))
        @include ("forum::components.icons.{$icon}")
    @endif
    {{ $text }}
</span>
