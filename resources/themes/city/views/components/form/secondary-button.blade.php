@props(['classes' => '', 'type' => 'submit'])

<button type="{{ $type }}"
    class="w-full rounded-lg border-2 border-city-blue-dark bg-city-blue px-6 py-2 font-bold text-white shadow-[inset_0_-3px_0_0_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:brightness-110 {{ $classes }}">
    {{ $slot }}
</button>
