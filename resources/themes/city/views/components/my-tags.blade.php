@props(['tags'])

<x-panel :title="__('My tags')">
    <div class="-mx-1 mb-3 flex flex-wrap gap-x-3 gap-y-2 border-b border-[#e4e4e7] pb-5 dark:border-white/10">
        @forelse ($tags as $tag)
            <span class="inline-flex items-center gap-1 rounded-full bg-sky-300/60 py-1 pl-3 pr-1 leading-tight ring-1 ring-inset ring-black/5" wire:key="my-tag-{{ $tag->id }}">
                <a href="{{ route('tags.index', ['tag' => $tag->name]) }}" class="hover:underline">{{ $tag->name }}</a>

                <form method="POST" action="{{ route('tags.destroy', $tag) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="{{ __('Remove') }}"
                            class="flex h-5 w-5 items-center justify-center rounded-full text-black/50 transition hover:bg-black/10 hover:text-black dark:text-white/60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </form>
            </span>
        @empty
            <p class="px-1">{{ __('You have no tags yet. Add a tag that describes you!') }}</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('tags.store') }}">
        @csrf
        <label class="mb-3 block">
            <span class="sr-only">{{ __('Tag') }}</span>
            <input type="text" name="tag" maxlength="20" required value="{{ old('tag') }}" placeholder="{{ __('Tag') }}..."
                   @class(['city-input pl-4!', 'city-input-error' => $errors->tags->has('tag')])>
        </label>

        @error('tag', 'tags')
            <p class="mb-3 text-sm text-red-500">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full rounded-lg bg-city-blue px-4 py-2.5 font-bold text-white transition hover:bg-city-blue-dark">
            {{ __('Add') }}
        </button>
    </form>

    @unless (request()->routeIs('tags.index'))
        <a href="{{ route('tags.index') }}" class="mt-3 block text-center font-medium text-city-blue hover:underline">
            {{ __('View more tags') }}
        </a>
    @endunless
</x-panel>
