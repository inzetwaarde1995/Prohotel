@props(['show', 'close', 'title', 'size' => 'max-w-lg'])

<div x-show="{{ $show }}" style="display: none;" x-transition.opacity role="dialog" aria-modal="true"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-on:click.self="{{ $close }}">
    <div class="max-h-[90vh] w-full overflow-y-auto rounded-xl bg-white p-1 shadow-xl dark:bg-city-ink-soft {{ $size }}" x-trap.noscroll="{{ $show }}">
        <h2 class="mb-1 flex items-center justify-between gap-2 rounded-lg bg-city-blue px-4 py-2 font-bold text-white">
            {{ $title }}
            <button type="button" class="rounded p-1 hover:bg-white/20" x-on:click="{{ $close }}" aria-label="{{ __('Close') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </h2>

        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
</div>
