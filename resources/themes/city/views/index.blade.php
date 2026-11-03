<x-app-layout>
    @push('title', __('Welcome to the best hotel on the web!'))

    <div class="col-span-12 flex flex-col gap-y-12">
        @if ($popularTags->isNotEmpty())
            <section>
                <x-section-title icon="heart">{{ __(':hotel also loves...', ['hotel' => setting('hotel_name')]) }}</x-section-title>

                <div class="flex flex-row flex-wrap gap-x-4 gap-y-3">
                    @foreach ($popularTags as $tag)
                        <x-tag-chip :name="$tag['name']" :weight="$tag['weight']" />
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <x-section-title icon="news">{{ __('Latest news') }}</x-section-title>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @forelse ($articles->take(3) as $article)
                    <x-article-card :article="$article" />
                @empty
                    <x-filler-article-card />
                @endforelse
            </div>
        </section>

        @if ($popularRooms->isNotEmpty())
            <section>
                <x-section-title icon="rooms">{{ __('Popular rooms') }}</x-section-title>

                <div class="grid grid-cols-1 gap-6 min-[420px]:grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 xl:gap-3">
                    @foreach ($popularRooms as $room)
                        <div class="city-tile flex flex-col gap-4 p-6" wire:key="room-{{ $room->id }}">
                            <span class="box-content flex items-center justify-center rounded-lg bg-[#ececef] p-3 dark:bg-city-ink">
                                <img loading="lazy" width="110" height="76" alt=""
                                     class="h-[76px] w-[110px] select-none rounded-lg object-none object-center [image-rendering:pixelated]"
                                     src="{{ setting('room_thumbnail_path') }}/{{ $room->id }}.png"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/images/profile/room_placeholder.png') }}';">
                            </span>

                            <div class="flex flex-col gap-0.5">
                                <span class="break-words font-bold text-city-ink dark:text-gray-100">{{ $room->name }}</span>
                                <span class="city-chip mt-1 w-fit">{{ __(':count visitors', ['count' => $room->users]) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($photos->isNotEmpty())
            <section>
                <x-section-title icon="photos">{{ __('Latest Photos') }}</x-section-title>

                <x-photos :photos="$photos" />
            </section>
        @endif

        <x-newest-furniture />
    </div>

    @push('javascript')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    @endpush

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
</x-app-layout>
