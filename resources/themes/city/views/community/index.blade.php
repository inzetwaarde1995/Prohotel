<x-app-layout>
    @push('title', __('Community'))

    <div class="col-span-12 flex flex-col gap-8 lg:flex-row">
        <div class="flex w-full min-w-0 flex-col gap-14">
            {{-- Popular rooms --}}
            <section class="city-tile overflow-hidden rounded-xl p-1">
                <h2 class="mb-1 flex w-full rounded-lg bg-city-blue px-4 py-2 font-bold text-white shadow-[0_2px_0_0_rgba(0,0,0,0.05)]">
                    {{ __('Popular rooms') }}
                </h2>

                <ul>
                    @forelse ($popularRooms as $room)
                        <li wire:key="community-room-{{ $room->id }}"
                            class="flex items-center gap-3 border-[#e4e4e7] p-4 even:bg-[#f4f4f6] not-last:border-b dark:border-white/10 dark:even:bg-white/5">
                            <img alt="" width="110" height="64"
                                 class="h-16 w-[110px] shrink-0 rounded-lg bg-[#ececef] object-none object-center [image-rendering:pixelated] max-[280px]:hidden dark:bg-city-ink"
                                 src="{{ setting('room_thumbnail_path') }}/{{ $room->id }}.png"
                                 onerror="this.onerror=null;this.src='{{ asset('assets/images/profile/room_placeholder.png') }}';">

                            <div class="flex min-w-0 flex-1 flex-col">
                                <p class="break-words font-bold">{{ $room->name }}</p>
                                <span class="city-chip mt-1 w-fit">{{ __(':count visitors', ['count' => $room->users]) }}</span>
                            </div>

                            <a data-turbolinks="false" href="{{ route('nitro-client', ['room' => $room->id]) }}"
                               class="shrink-0 rounded-xl bg-city-blue px-3 py-2 text-sm font-bold leading-relaxed text-white transition hover:bg-city-blue-dark">
                                {{ __('Go to this room') }}
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-[#71717a]">{{ __('There are no rooms with visitors right now.') }}</li>
                    @endforelse
                </ul>
            </section>

            {{-- Top 5 --}}
            <section x-data="{ tab: @js(array_key_first($leaderboards)) }">
                <x-section-title icon="star">{{ __('Top 5 :hotel', ['hotel' => setting('hotel_name')]) }}</x-section-title>

                <div role="tablist" class="-mb-px flex max-w-[calc(100%-8px)] items-start gap-1 overflow-x-auto">
                    @foreach ($leaderboards as $key => $board)
                        <button type="button" role="tab" x-on:click="tab = @js($key)" :aria-selected="tab === @js($key)"
                                :class="tab === @js($key) ? 'bg-white pb-[13px]! text-black cursor-default dark:bg-city-ink-soft dark:text-white' : 'bg-[#f4f4f6] text-[#52525b] hover:text-black dark:bg-white/5 dark:text-gray-400'"
                                class="shrink-0 select-none rounded-t-lg border border-b-0 border-[#d4d4d8] px-4 py-3 text-lg leading-tight font-bold transition-all duration-150 dark:border-white/10">
                            {{ $board['label'] }}
                        </button>
                    @endforeach
                </div>

                @foreach ($leaderboards as $key => $board)
                    <div role="tabpanel" x-show="tab === @js($key)" @unless ($loop->first) style="display: none;" @endunless
                         class="city-tile overflow-hidden rounded-tl-none">
                        <ul>
                            @forelse ($board['entries'] as $entry)
                                <li class="flex items-center gap-4 border-[#e4e4e7] px-4 py-2 even:bg-[#f4f4f6] not-last:border-b dark:border-white/10 dark:even:bg-white/5">
                                    <span @class([
                                        'select-none rounded-full px-2.5 py-1 font-mono text-xl leading-none ring-1 ring-inset ring-black/10',
                                        'bg-yellow-400 text-black' => $loop->iteration === 1,
                                        'bg-white text-black' => $loop->iteration === 2,
                                        'bg-red-300 text-white' => $loop->iteration === 3,
                                        'bg-[#71717a] text-white' => $loop->iteration > 3,
                                    ])>{{ $loop->iteration }}</span>

                                    <a href="{{ route('home.show', $entry['user']->username) }}" class="flex items-center gap-2 hover:text-city-blue">
                                        <span class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#ececef] ring-1 ring-inset ring-black/5 max-[280px]:hidden dark:bg-city-ink">
                                            <img alt="" class="max-w-none select-none [image-rendering:pixelated]"
                                                 src="{{ setting('avatar_imager') }}{{ $entry['user']->look }}&direction=2&head_direction=3&gesture=sml&headonly=1">
                                        </span>
                                        <span class="flex flex-col gap-1">
                                            <strong class="leading-tight">{{ $entry['user']->username }}</strong>
                                            <span class="leading-tight text-[#52525b] dark:text-gray-400">
                                                {{ number_format($entry['amount'], 0, ',', '.') }} {{ $board['label'] }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @empty
                                <li class="px-4 py-3 text-[#71717a]">{{ __('No users found.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </section>
        </div>

        <aside class="flex w-full shrink-0 flex-col gap-12 lg:max-w-[352px]">
            @if ($featuredArticle)
                <section>
                    <x-section-title icon="star">{{ __('Recommended news') }}</x-section-title>

                    <a href="{{ route('article.show', $featuredArticle->slug) }}"
                       class="city-tile group flex flex-row gap-4 p-4 max-sm:flex-col lg:flex-col">
                        <img alt="" width="320" height="150"
                             class="h-[150px] select-none rounded-lg object-cover object-center max-sm:w-full lg:w-full"
                             src="/storage/{{ $featuredArticle->image }}">

                        <div>
                            <h3 class="font-bold group-hover:text-city-blue">{{ $featuredArticle->title }}</h3>

                            @if ($featuredArticle->short_story)
                                <p class="mb-6 text-sm font-medium text-[#52525b] dark:text-gray-400">{{ $featuredArticle->short_story }}</p>
                            @endif

                            <div class="flex flex-wrap gap-1">
                                <span class="city-chip">{{ $featuredArticle->user->username ?? setting('hotel_name') }}</span>
                                <span class="city-chip">{{ $featuredArticle->created_at?->format('d M Y') }}</span>
                            </div>
                        </div>
                    </a>
                </section>
            @endif

            <section>
                <x-section-title icon="compass">{{ __('Discover more') }}</x-section-title>

                <div class="city-tile flex flex-col overflow-hidden">
                    @php
                        $discoverLinks = [
                        ['route' => route('leaderboard.index'), 'label' => __('Leaderboards')],
                        ['route' => route('staff.index'), 'label' => __('Staff')],
                        ['route' => route('teams.index'), 'label' => __('Teams')],
                        ['route' => route('photos.index'), 'label' => __('Photos')],
                    ];
                    @endphp

                    @foreach ($discoverLinks as $link)
                        <a href="{{ $link['route'] }}"
                           class="flex items-center justify-between border-[#e4e4e7] px-4 py-3 transition not-last:border-b hover:bg-[#f4f4f6] hover:text-city-blue dark:border-white/10 dark:hover:bg-white/5">
                            {{ $link['label'] }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#a1a1aa]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
