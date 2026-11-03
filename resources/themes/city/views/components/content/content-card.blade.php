@props(['icon' => '', 'classes' => ''])

<div class="flex w-full flex-col gap-y-4 overflow-hidden rounded-lg bg-city-bg p-2 pb-3 shadow-[0_2px_0_0_rgba(0,0,0,0.1)] dark:bg-city-ink-soft {{ $classes }}">
    <div class="city-panel-header flex items-center gap-x-3 rounded-md px-3 py-2 text-white">
        @if (empty($icon) === false)
            <div class="relative flex h-[40px] w-[40px] shrink-0 items-center justify-center rounded-full ring-2 ring-white/40 {{ $icon }}"></div>
        @endif

        <div class="flex flex-col justify-center text-sm">
            <p class="font-bold">{{ $title }}</p>

            @if (isset($underTitle))
                <p class="text-white/80">{{ $underTitle }}</p>
            @endif
        </div>
    </div>

    <section class="flex h-full flex-col px-2">
        {{ $slot }}
    </section>
</div>
