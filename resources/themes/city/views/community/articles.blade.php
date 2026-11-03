<x-app-layout>
    @push('title', __('News'))

    <div class="col-span-12">
        <header class="mb-6 flex items-center justify-between gap-3 max-md:flex-col max-md:items-start">
            <x-section-title icon="news" classes="mb-0! max-md:w-full max-md:border-b max-md:border-[#e4e4e7] max-md:pb-3 dark:max-md:border-white/10">
                {{ __('News') }}
            </x-section-title>

            <form method="GET" action="{{ route('article.index') }}" class="flex items-center gap-2 max-md:w-full" role="search">
                <label class="relative block w-full md:w-64">
                    <span class="sr-only">{{ __('Search') }}</span>
                    <span class="city-input-icon text-[#a1a1aa]!">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="search" name="search" value="{{ $search }}" maxlength="50" placeholder="{{ __('Search') }}..." class="city-input">
                </label>
            </form>
        </header>

        @if ($search !== '')
            <p class="mb-4 text-sm text-[#52525b] dark:text-gray-400">
                {{ __(':count results for ":search"', ['count' => $articles->total(), 'search' => $search]) }}
                <a href="{{ route('article.index') }}" class="ml-2 font-medium text-city-blue hover:underline">{{ __('Clear') }}</a>
            </p>
        @endif

        <div class="mb-6 grid grid-cols-2 gap-x-6 gap-y-3 max-sm:grid-cols-1">
            @forelse ($articles as $article)
                <a href="{{ route('article.show', $article->slug) }}" wire:key="news-{{ $article->id }}"
                   class="city-tile group relative top-0 flex gap-3 px-[15px] py-4 transition-all duration-100 hover:-top-0.5 hover:shadow-sm max-[384px]:flex-col">
                    <img alt="" width="128" height="96" loading="lazy"
                         class="h-24 w-full shrink-0 select-none rounded-lg object-cover object-center min-[384px]:w-32"
                         src="/storage/{{ $article->image }}">

                    <div class="flex min-w-0 flex-col gap-2">
                        <p class="font-bold text-city-ink transition-colors group-hover:text-city-blue dark:text-gray-100">
                            {{ $article->title }}
                        </p>

                        @if ($article->short_story)
                            <p class="text-sm font-medium text-[#52525b] dark:text-gray-400">{{ $article->short_story }}</p>
                        @endif

                        <div class="mt-auto flex flex-wrap gap-1">
                            <span class="city-chip">{{ $article->user->username ?? setting('hotel_name') }}</span>
                            @if ($article->created_at)
                                <span class="city-chip">{{ $article->created_at->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="city-tile col-span-full p-6 text-center text-[#71717a]">
                    {{ $search !== '' ? __('No news found for ":search".', ['search' => $search]) : __('No published articles') }}
                </div>
            @endforelse
        </div>

        @if ($articles->hasPages())
            <div class="flex items-center gap-2">
                @unless ($articles->onFirstPage())
                    <a href="{{ $articles->previousPageUrl() }}" class="city-btn-ghost px-5! py-3! text-lg font-medium">
                        {{ __('Previous') }}
                    </a>
                @endunless

                @if ($articles->hasMorePages())
                    <a href="{{ $articles->nextPageUrl() }}" class="city-btn-ghost px-5! py-3! text-lg font-medium">
                        {{ __('More news') }}
                    </a>
                @endif

                <span class="ml-auto text-sm text-[#71717a]">
                    {{ __('Page :current of :last', ['current' => $articles->currentPage(), 'last' => $articles->lastPage()]) }}
                </span>
            </div>
        @endif
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
