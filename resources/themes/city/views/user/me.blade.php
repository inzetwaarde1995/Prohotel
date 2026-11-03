<x-app-layout>
    @push('title', auth()->user()->username)

    <div class="col-span-12 flex flex-col gap-8 lg:flex-row">
        <section class="w-full min-w-0"
                 x-data="{ page: 0, perPage: 3, total: {{ $articles->count() }}, get pages() { return Math.max(1, Math.ceil(this.total / this.perPage)) } }">
            <header class="mb-6 flex flex-wrap items-center justify-between gap-2">
                <x-section-title icon="news" classes="mb-0!">{{ __('Latest news') }}</x-section-title>

                <div class="flex items-center gap-2">
                    <button type="button" class="city-btn-ghost" title="{{ __('Previous page') }}"
                            x-on:click="page = Math.max(0, page - 1)" :disabled="page === 0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </button>
                    <button type="button" class="city-btn-ghost" title="{{ __('Next page') }}"
                            x-on:click="page = Math.min(pages - 1, page + 1)" :disabled="page >= pages - 1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                    <a href="{{ route('article.index') }}" class="city-btn-ghost px-4! font-medium text-city-blue">
                        {{ __('More news') }}
                    </a>
                </div>
            </header>

            <div class="flex flex-col gap-3">
                @forelse ($articles as $article)
                    <a href="{{ route('article.show', $article->slug) }}" wire:key="me-article-{{ $article->id }}"
                       x-show="Math.floor({{ $loop->index }} / perPage) === page"
                       @if ($loop->index >= 3) style="display: none;" @endif
                       class="city-tile group relative top-0 flex flex-row items-start gap-6 p-5 transition-all duration-75 hover:-top-0.5 hover:shadow-sm max-sm:flex-col max-sm:items-center">
                        <img alt="" width="320" height="150"
                             class="h-[150px] w-full shrink-0 select-none rounded-lg object-cover object-center sm:w-[320px]"
                             src="/storage/{{ $article->image }}" loading="lazy">

                        <div class="min-w-0 max-sm:flex max-sm:flex-col max-sm:items-center">
                            <p class="mb-2 font-bold text-city-ink transition-colors group-hover:text-city-blue dark:text-gray-100">
                                {{ $article->title }}
                            </p>

                            @if ($article->short_story)
                                <p class="mb-6 text-sm font-medium text-[#52525b] max-sm:text-center dark:text-gray-400">
                                    {{ $article->short_story }}
                                </p>
                            @endif

                            <div class="flex flex-wrap gap-2">
                                <span class="city-chip">{{ $article->user->username ?? setting('hotel_name') }}</span>
                                @if ($article->created_at)
                                    <span class="city-chip">{{ $article->created_at->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <x-filler-article-card />
                @endforelse
            </div>
        </section>

        <aside class="flex w-full shrink-0 flex-col gap-8 lg:max-w-[352px]">
            <x-my-tags :tags="$myTags" />

            <x-panel :title="__('Online Friends')" id="online-friends">
                @if ($onlineFriends->isEmpty())
                    <p>{{ __('None of your friends are online right now.') }}</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($onlineFriends as $friend)
                            <a href="{{ route('home.show', $friend->username) }}" title="{{ $friend->username }}"
                               wire:key="friend-{{ $friend->id }}"
                               class="h-12 w-12 overflow-hidden rounded-full border-2 border-[#e4e4e7] bg-[#ececef] transition hover:border-city-blue dark:border-white/10 dark:bg-city-ink">
                                <img class="h-full w-full object-none object-center [image-rendering:pixelated]"
                                     src="{{ setting('avatar_imager') }}{{ $friend->look }}&direction=2&head_direction=3&gesture=sml&headonly=1"
                                     alt="{{ $friend->username }}">
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-panel>

            <x-panel :title="sprintf(__('User Referrals (%s/%s)'), $user->referrals->referrals_total ?? 0, setting('referrals_needed'))">
                <p class="mb-3 text-sm">
                    {{ __('Here at :hotel we have added a referral system, allowing you to obtain a bonus for every :needed users that registers through your referral link will allow you to claim a reward of :amount diamonds!', ['hotel' => setting('hotel_name'), 'needed' => setting('referrals_needed'), 'amount' => setting('referral_reward_amount')]) }}
                </p>

                <div class="flex gap-2" x-data="{ copied: false }">
                    <input id="referral" type="text" readonly class="city-input pl-3!"
                           value="{{ sprintf('%s/register/%s', config('habbo.site.site_url'), $user->referral_code) }}">
                    <button type="button" class="city-btn-ghost shrink-0 px-3! text-city-blue"
                            x-on:click="navigator.clipboard.writeText(document.getElementById('referral').value); copied = true; setTimeout(() => copied = false, 2000)">
                        <span x-show="! copied">{{ __('Copy code') }}</span>
                        <span x-show="copied" style="display: none;">✓</span>
                    </button>
                </div>

                <small class="mt-2 block text-[#71717a]">
                    {{ __('Boosting referrals by making own accounts will lead to removal of all progress, currency, inventory and a potential ban') }}
                </small>

                @if (($user->referrals?->referrals_total ?? 0) >= (int) setting('referrals_needed'))
                    <a href="{{ route('claim.referral-reward') }}" class="city-btn-success mt-3 w-full text-base!">
                        {{ __('Claim your referrals reward!') }}
                    </a>
                @else
                    <p class="mt-3 rounded-lg bg-[#ececef] px-3 py-2 text-center text-sm font-medium dark:bg-white/10">
                        {{ __('You need to refer :needed more users, before being able to claim your reward', ['needed' => $user->referralsNeeded() ?? 0]) }}
                    </p>
                @endif
            </x-panel>

            @if (setting('discord_widget_id'))
                <x-user.discord-widget />
            @endif
        </aside>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
