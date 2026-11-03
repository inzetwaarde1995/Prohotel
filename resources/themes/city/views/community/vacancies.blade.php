@php
    $values = [
        [
            'title' => __('Team work'),
            'text' => __('Working together with your colleagues leads to growth and success for everyone.'),
            'bg' => 'bg-yellow-400',
            'image' => asset('assets/images/vacancies/team-work.png'),
        ],
        [
            'title' => __('Learn with us'),
            'text' => __('You do not have to know everything, as long as you are open to learning new things!'),
            'bg' => 'bg-city-blue',
            'image' => asset('assets/images/vacancies/learn.png'),
        ],
        [
            'title' => __('Have fun'),
            'text' => __('In the end it is all about joy and laughter!'),
            'bg' => 'bg-purple-500',
            'image' => asset('assets/images/vacancies/fun.png'),
        ],
    ];

    $reopen = (int) old('position_id');
@endphp

<x-app-layout>
    @push('title', __('Vacancies'))

    <div class="col-span-12" x-data="{ open: {{ $reopen ?: 'null' }} }" x-on:keydown.escape.window="open = null">
        <section class="mb-14">
            <x-section-title icon="rooms">{{ __(':hotel vacancies', ['hotel' => setting('hotel_name')]) }}</x-section-title>

            <p class="mb-8 text-lg font-medium">
                {{ __('At :hotel we work day and night to make everything great. We can only do that together, with our whole team and the friends who help us. Do you want to help make :hotel an amazing place too? Join us!', ['hotel' => setting('hotel_name')]) }}
            </p>

            <div class="flex gap-8 max-md:flex-col">
                @foreach ($values as $value)
                    <div class="flex flex-1 flex-col gap-4">
                        <div class="grid h-56 w-full place-items-center overflow-hidden rounded-3xl shadow-[0_2px_0_0_rgba(0,0,0,0.1)] {{ $value['bg'] }}">
                            <img alt="" class="pointer-events-none select-none [image-rendering:pixelated]" src="{{ $value['image'] }}">
                        </div>

                        <div class="rounded-xl bg-white p-6 dark:bg-city-ink-soft">
                            <header class="mb-2 flex items-center gap-2">
                                <h3 class="text-xl font-bold">{{ $value['title'] }}</h3>
                                <hr class="h-px flex-1 border-none bg-black/20 dark:bg-white/20">
                            </header>
                            <p class="text-[#52525b] dark:text-gray-400">{{ $value['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <x-section-title icon="briefcase">{{ __('Available vacancies') }}</x-section-title>

            @if ($vacancies->isEmpty())
                <div class="city-tile p-6 text-center">
                    <p class="font-bold">{{ __('No positions open') }}</p>
                    <p class="text-[#52525b] dark:text-gray-400">{{ __('Please come back at a later time to check if we have any positions open by then! Thank you for your interest.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-5 max-sm:grid-cols-1">
                    @foreach ($vacancies as $vacancy)
                        <article class="city-tile flex h-auto flex-col rounded-xl p-1" wire:key="vacancy-{{ $vacancy['id'] }}">
                            <h2 class="mb-1 flex w-full items-center gap-2 rounded-lg bg-city-blue px-4 py-2 font-bold text-white shadow-[0_2px_0_0_rgba(0,0,0,0.05)]">
                                @if ($vacancy['badge'])
                                    <img src="{{ $vacancy['badge'] }}" alt="" class="-my-1 max-h-8 [image-rendering:pixelated]" onerror="this.remove()">
                                @endif
                                {{ $vacancy['title'] }}
                            </h2>

                            <div class="flex flex-wrap gap-2 p-2">
                                <span class="city-chip">{{ $vacancy['kind'] === 'team' ? __('Team') : __('Staff') }}</span>
                                @if ($vacancy['deadline'])
                                    <span class="city-chip">{{ __('Until :date', ['date' => $vacancy['deadline']]) }}</span>
                                @endif
                            </div>

                            <div class="prose prose-sm mb-6 max-w-none flex-1 px-6 dark:prose-invert prose-img:inline prose-ul:ml-4 prose-ul:list-disc">
                                @if ($vacancy['subtitle'])
                                    <p class="font-medium">{{ $vacancy['subtitle'] }}</p>
                                @endif

                                {!! nl2br(e($vacancy['description'])) !!}
                            </div>

                            <div class="p-6 pt-0">
                                @if ($vacancy['applied'])
                                    <span class="inline-flex items-center gap-2 rounded-lg bg-[#ececef] px-5 py-3 font-bold text-[#52525b] dark:bg-white/10 dark:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('You have already applied') }}
                                    </span>
                                @elseauth
                                    <button type="button" class="city-btn-success" x-on:click="open = {{ $vacancy['id'] }}">
                                        {{ __('Apply') }}
                                    </button>
                                @else
                                    <a href="{{ route('welcome') }}" class="city-btn-success">
                                        {{ __('Log in to apply') }}
                                    </a>
                                @endif
                            </div>
                        </article>

                        @if (auth()->check() && ! $vacancy['applied'])
                            <div x-show="open === {{ $vacancy['id'] }}" style="display: none;" x-transition.opacity
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true"
                                 x-on:click.self="open = null">
                                <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white p-1 shadow-xl dark:bg-city-ink-soft" x-trap.noscroll="open === {{ $vacancy['id'] }}">
                                    <h2 class="mb-1 flex items-center justify-between rounded-lg bg-city-blue px-4 py-2 font-bold text-white">
                                        {{ __('Apply for :position', ['position' => $vacancy['title']]) }}
                                        <button type="button" class="rounded p-1 hover:bg-white/20" x-on:click="open = null" aria-label="{{ __('Close') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                            </svg>
                                        </button>
                                    </h2>

                                    <form method="POST" action="{{ $vacancy['action'] }}" class="flex flex-col gap-3 p-4">
                                        @csrf
                                        <input type="hidden" name="redirect" value="vacancies">
                                        <input type="hidden" name="position_id" value="{{ $vacancy['id'] }}">

                                        <label for="content-{{ $vacancy['id'] }}" class="font-bold">{{ __('Why should we choose you?') }}</label>
                                        <textarea id="content-{{ $vacancy['id'] }}" name="content" required minlength="10" rows="7"
                                                  placeholder="{{ __('Tell us something about yourself, your experience and why you want this position...') }}"
                                                  class="city-input min-h-40 pl-3!">{{ $reopen === $vacancy['id'] ? old('content') : '' }}</textarea>

                                        @if ($reopen === $vacancy['id'])
                                            @error('content')
                                                <p class="text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                        @endif

                                        <p class="text-sm text-[#71717a]">
                                            {{ __('Please fill out all the fields to apply for :position. Be honest and transparent. Providing incorrect information may lead to removal if hired.', ['position' => $vacancy['title']]) }}
                                        </p>

                                        @if (setting('google_recaptcha_enabled'))
                                            <div class="g-recaptcha" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                                        @endif

                                        @if (setting('cloudflare_turnstile_enabled'))
                                            <x-turnstile />
                                        @endif

                                        <button type="submit" class="city-btn-success w-full">{{ __('Send application') }}</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
