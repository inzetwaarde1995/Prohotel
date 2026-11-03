<section class="city-dark-band -mb-10 mt-8 py-20">
    <x-section-title icon="furni" classes="text-white!">{{ __('Newest furni') }}</x-section-title>

    <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 sm:grid-cols-3 xl:grid-cols-6">
        @foreach ($items as $item)
            <div class="rounded-lg bg-white/10 p-6 ring-1 ring-inset ring-white/10" wire:key="furni-{{ $item->id }}">
                <span class="mb-4 flex h-16 w-full items-center justify-center rounded-lg bg-white/10 px-4">
                    <img alt="{{ $item->display_name }}" class="select-none [image-rendering:pixelated]"
                         src="{{ $item->icon() }}" loading="lazy"
                         onerror="this.onerror=null;this.style.visibility='hidden';">
                </span>
                <span class="break-words text-[#a1a1aa]">{{ $item->display_name }}</span>
            </div>
        @endforeach
    </div>
</section>
