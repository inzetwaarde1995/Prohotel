@props(['classes' => '', 'type' => 'submit'])

<button type="{{ $type }}" class="city-btn-green w-full {{ $classes }}">
    {{ $slot }}
</button>
