@props(['name', 'weight' => 4, 'href' => null])

@php
    $shade = match ((int) $weight) {
        1 => 'bg-sky-300/15',
        2 => 'bg-sky-300/35',
        3 => 'bg-sky-300/60',
        default => 'bg-sky-300',
    };
@endphp

<a href="{{ $href ?? route('tags.index', ['tag' => $name]) }}"
   {{ $attributes->merge(['class' => "select-none rounded-full px-3 py-1 leading-tight text-black ring-1 ring-inset ring-black/5 transition hover:ring-city-blue dark:text-gray-100 {$shade}"]) }}>
    {{ $name }}
</a>
