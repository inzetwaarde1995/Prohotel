@props(['title', 'classes' => ''])

<section {{ $attributes->merge(['class' => 'city-tile rounded-xl p-1 ' . $classes]) }}>
    <h2 class="mb-1 flex w-full rounded-lg bg-city-blue px-4 py-2 font-bold text-white shadow-[0_2px_0_0_rgba(0,0,0,0.05)]">
        {{ $title }}
    </h2>

    <div class="p-3">
        {{ $slot }}
    </div>
</section>
