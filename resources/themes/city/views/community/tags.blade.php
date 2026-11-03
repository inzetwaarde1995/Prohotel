<x-app-layout>
    @push('title', __('Tags'))

    <div class="col-span-12 flex flex-col gap-8 lg:flex-row">
        <aside class="flex w-full shrink-0 flex-col gap-12 lg:max-w-[352px]">
            @auth
                <x-my-tags :tags="$myTags" />
            @endauth

            <section>
                <x-section-title icon="fire">{{ __('Popular tags') }}</x-section-title>

                <div class="city-tile flex flex-wrap justify-center gap-x-4 gap-y-3 p-4">
                    @forelse ($popularTags as $tag)
                        <x-tag-chip :name="$tag['name']" :weight="$tag['weight']" />
                    @empty
                        <p class="text-[#71717a]">{{ __('Nobody has added a tag yet.') }}</p>
                    @endforelse
                </div>
            </section>

            <section>
                <x-section-title icon="fire">{{ __('Tag fight') }}</x-section-title>

                <div class="city-tile p-4"
                     x-data="{
                         first: '',
                         second: '',
                         state: 'idle',
                         firstWins: true,
                         count: '',
                         timers: [],
                         start() {
                             this.first = this.first.trim().toLowerCase();
                             this.second = this.second.trim().toLowerCase();

                             if (this.first === '' || this.second === '') {
                                 return;
                             }

                             this.timers.forEach((timer) => clearTimeout(timer));
                             this.timers = [];
                             this.firstWins = Math.random() < 0.5;
                             this.state = 'countdown';

                             ['3', '2', '1', @js(__('Fight!'))].forEach((step, index) => {
                                 this.timers.push(setTimeout(() => this.count = step, index * 800));
                             });

                             this.timers.push(setTimeout(() => this.state = 'fighting', 3200));
                             this.timers.push(setTimeout(() => this.state = 'done', 5700));
                         },
                         get winner() {
                             return this.firstWins ? this.first : this.second;
                         },
                     }">
                    <form x-on:submit.prevent="start()" class="mb-6 flex flex-col gap-2 border-b border-[#e4e4e7] pb-6 dark:border-white/10">
                        <label>
                            <span class="sr-only">{{ __('First tag') }}</span>
                            <input type="text" x-model="first" maxlength="20" required placeholder="{{ __('First tag') }}" class="city-input pl-4!">
                        </label>
                        <label>
                            <span class="sr-only">{{ __('Second tag') }}</span>
                            <input type="text" x-model="second" maxlength="20" required placeholder="{{ __('Second tag') }}" class="city-input mb-4 pl-4!">
                        </label>
                        <button type="submit" :disabled="state === 'countdown' || state === 'fighting'"
                                class="w-fit rounded-lg bg-city-blue px-6 py-2 font-bold text-white transition hover:bg-city-blue-dark disabled:cursor-wait disabled:opacity-60">
                            {{ __('Start') }}
                        </button>
                    </form>

                    <div class="flex flex-col items-center" aria-live="polite">
                        <div class="relative h-[120px] w-[160px]">
                            <template x-if="state === 'countdown'">
                                <div class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center">
                                    <template x-for="step in [count]" :key="step">
                                        <span x-text="step" class="city-countdown" :class="isNaN(step) && 'city-countdown-final'"></span>
                                    </template>
                                </div>
                            </template>

                            <img x-show="state === 'idle' || state === 'countdown'" alt="" width="160" height="120" class="pointer-events-none select-none [image-rendering:pixelated]"
                                 src="{{ asset('assets/images/tagfight/start.gif') }}">
                            <img x-show="state === 'fighting'" style="display: none;" alt="" width="160" height="120" class="pointer-events-none select-none [image-rendering:pixelated]"
                                 src="{{ asset('assets/images/tagfight/fight.gif') }}">
                            <img x-show="state === 'done' && firstWins" style="display: none;" alt="" width="160" height="120" class="pointer-events-none select-none [image-rendering:pixelated]"
                                 src="{{ asset('assets/images/tagfight/first-wins.gif') }}">
                            <img x-show="state === 'done' && ! firstWins" style="display: none;" alt="" width="160" height="120" class="pointer-events-none select-none [image-rendering:pixelated]"
                                 src="{{ asset('assets/images/tagfight/second-wins.gif') }}">
                        </div>

                        <div x-show="state !== 'idle'" style="display: none;" class="flex w-[160px] justify-between text-sm font-bold">
                            <span class="max-w-[78px] truncate" :class="state === 'done' && firstWins && 'text-green-600'" x-text="first"></span>
                            <span class="max-w-[78px] truncate text-right" :class="state === 'done' && ! firstWins && 'text-green-600'" x-text="second"></span>
                        </div>

                        <p class="mt-3 min-h-6 text-center font-bold">
                            <span x-show="state === 'idle'" class="text-sm font-normal text-[#71717a]">{{ __('Pick two tags and let them fight!') }}</span>
                            <span x-show="state === 'countdown'" style="display: none;" class="text-[#71717a]">{{ __('Get ready...') }}</span>
                            <span x-show="state === 'fighting'" style="display: none;" class="text-[#71717a]">{{ __('Fighting...') }}</span>
                            <span x-show="state === 'done'" style="display: none;" x-text="@js(__(':tag wins the fight!', ['tag' => '__TAG__'])).replace('__TAG__', winner)"></span>
                        </p>
                    </div>
                </div>
            </section>
        </aside>

        <section class="w-full min-w-0">
            <header class="mb-6 flex items-center justify-between gap-3 max-md:flex-col max-md:items-start">
                <x-section-title icon="rooms" classes="mb-0! max-md:w-full max-md:border-b max-md:border-[#e4e4e7] max-md:pb-3 dark:max-md:border-white/10">
                    {{ __('Find new friends') }}
                </x-section-title>

                <form method="GET" action="{{ route('tags.index') }}" class="flex gap-2 max-[380px]:w-full max-[380px]:flex-col" role="search">
                    <label>
                        <span class="sr-only">{{ __('Search tags') }}</span>
                        <input type="search" name="tag" maxlength="20" value="{{ $search }}" placeholder="{{ __('Search tags') }}..." class="city-input pl-4!">
                    </label>
                    <button type="submit" class="shrink-0 rounded-lg bg-city-blue px-4 py-2 font-bold text-white transition hover:bg-city-blue-dark">
                        {{ __('Search') }}
                    </button>
                </form>
            </header>

            <div class="city-tile divide-y divide-[#e4e4e7] overflow-hidden dark:divide-white/10">
                @if ($users === null)
                    <p class="p-4 text-[#52525b] dark:text-gray-400">{{ __('Search for a tag to find users with the same interests.') }}</p>
                @else
                    @forelse ($users as $user)
                        <div class="flex items-center gap-4 p-4 even:bg-[#f4f4f6] dark:even:bg-white/5" wire:key="tag-user-{{ $user->id }}">
                            <a href="{{ route('home.show', $user->username) }}"
                               class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#ececef] ring-1 ring-inset ring-black/5 dark:bg-city-ink">
                                <img alt="" class="max-w-none select-none [image-rendering:pixelated]"
                                     src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&headonly=1">
                            </a>

                            <div class="flex min-w-0 flex-1 flex-col gap-1">
                                <a href="{{ route('home.show', $user->username) }}" class="flex items-center gap-2 font-bold hover:text-city-blue">
                                    <span @class(['h-2 w-2 shrink-0 rounded-full', 'bg-green-500' => $user->online, 'bg-[#a1a1aa]' => ! $user->online])
                                          title="{{ $user->online ? __('Online') : __('Offline') }}"></span>
                                    {{ $user->username }}
                                </a>

                                @if ($user->motto)
                                    <p class="truncate text-sm text-[#52525b] dark:text-gray-400">{{ $user->motto }}</p>
                                @endif

                                <div class="mt-1 flex flex-wrap gap-1.5">
                                    @foreach ($user->tags as $userTag)
                                        <x-tag-chip :name="$userTag->name" :weight="$userTag->name === $search ? 4 : 1" class="px-2.5! py-0.5! text-sm" />
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <span class="block p-4 font-medium text-red-600">{{ __('No results found.') }}</span>
                    @endforelse
                @endif
            </div>

            @if ($users?->hasPages())
                <div class="mt-4 flex items-center gap-2">
                    @unless ($users->onFirstPage())
                        <a href="{{ $users->previousPageUrl() }}" class="city-btn-ghost px-5! py-3! font-medium">{{ __('Previous') }}</a>
                    @endunless
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="city-btn-ghost px-5! py-3! font-medium">{{ __('Next page') }}</a>
                    @endif
                </div>
            @endif
        </section>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
