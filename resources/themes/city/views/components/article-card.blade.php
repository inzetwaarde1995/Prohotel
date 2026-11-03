@props(['article', 'forSlider' => false])

<article @class([
    'city-tile flex flex-col items-center gap-3 px-[15px] py-4 transition duration-200 ease-in-out hover:-translate-y-0.5',
    'swiper-slide' => $forSlider,
])>
    <a href="{{ route('article.show', $article->slug) }}" class="block w-full">
        <img class="h-32 w-full select-none rounded-lg object-cover object-center"
             src="/storage/{{ $article->image }}" alt="{{ $article->title }}" loading="lazy">
    </a>

    <div class="flex w-full flex-1 flex-col items-center">
        <a href="{{ route('article.show', $article->slug) }}" class="mb-4 block text-center font-bold text-city-ink hover:text-city-blue dark:text-gray-100">
            {{ $article->title }}
        </a>

        <div class="flex select-none flex-wrap justify-center gap-1">
            <span class="city-chip">{{ $article->user->username ?? setting('hotel_name') }}</span>

            @if ($article->created_at)
                <span class="city-chip">{{ $article->created_at->format('d M Y') }}</span>
            @endif
        </div>
    </div>
</article>
