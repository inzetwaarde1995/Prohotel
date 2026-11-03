@props(['icon', 'classes' => ''])

<div class="flex w-full flex-col gap-y-4 {{ $classes }}">
    <div class="flex items-center gap-x-3">
        <div class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-full ring-2 ring-white shadow-[0_2px_0_0_rgba(0,0,0,0.15)] dark:ring-city-ink {{ $icon }}"></div>

        <div class="flex flex-col">
            <h2 class="text-xl font-bold text-city-ink dark:text-white">{{ $title }}</h2>

            @if (isset($underTitle))
                <p class="text-sm text-city-muted">{{ $underTitle }}</p>
            @endif
        </div>
    </div>

    {{ $slot }}
</div>
