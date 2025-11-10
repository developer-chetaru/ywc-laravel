@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-sm text-blue-600']) }}>{{ $message }}</p>
@enderror
