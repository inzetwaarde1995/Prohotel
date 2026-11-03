@php
    $onlineCount = DB::table('users')->where('online', '1')->count();
    $isWelcomePage = request()->routeIs('welcome');
@endphp

@auth
    @php
        $authUser = auth()->user();
        $onlineFriendsCount = rescue(fn (): int => $authUser->getOnlineFriends(99)->count(), 0, report: false);
        $currencies = [
            ['label' => __('Credits'), 'amount' => $authUser->credits, 'icon' => 'credits', 'bg' => 'bg-yellow-500/25'],
            ['label' => __('Duckets'), 'amount' => $authUser->currency('duckets'), 'icon' => 'duckets', 'bg' => 'bg-purple-500/25'],
            ['label' => __('Diamonds'), 'amount' => $authUser->currency('diamonds'), 'icon' => 'diamonds', 'bg' => 'bg-sky-500/25'],
        ];
    @endphp

    <section class="city-hero border-b border-black text-white">
        @if (setting('cms_header'))
            <div class="city-hero-art max-sm:opacity-30" style="background-image: url({{ setting('cms_header') }});"></div>
        @endif

        <div class="relative mx-auto flex h-[150px] w-[calc(100%-48px)] max-w-[1128px] items-center justify-between gap-5 max-sm:h-auto max-sm:min-h-[150px] max-sm:flex-col max-sm:py-6">
            <a href="{{ route('me.show') }}">
                <img class="max-h-[110px]" src="{{ setting('cms_logo') }}" alt="{{ setting('hotel_name') }}">
            </a>

            <div class="flex flex-wrap items-center gap-3">
                @if (config('habbo.client.flash_enabled'))
                    <a data-turbolinks="false" href="{{ route('flash-client') }}" class="city-btn-dark">
                        {{ __('Flash client') }}
                    </a>
                @endif

                <a data-turbolinks="false" href="{{ route('nitro-client') }}" class="city-btn-play">
                    {{ __('Enter :hotel', ['hotel' => setting('hotel_name')]) }}
                </a>
            </div>
        </div>
    </section>

    <div class="mb-2 flex justify-between border-b border-[#e4e4e7] bg-white/80 px-6 py-[7px] dark:border-white/10 dark:bg-city-ink-soft/80">
        <div class="flex h-0 flex-1 items-center self-end max-lg:hidden">
            <a href="{{ route('home.show', $authUser->username) }}"
               class="w-fit overflow-hidden rounded-full border-2 border-city-blue bg-[#a1a1aa]/50 backdrop-blur-2xl transition hover:scale-105">
                <img class="h-16 w-16 object-none object-[center_-24px] [image-rendering:pixelated]"
                     src="{{ setting('avatar_imager') }}{{ $authUser->look }}&direction=2&head_direction=3&gesture=sml&action=wav"
                     alt="{{ $authUser->username }}">
            </a>
        </div>

        <div class="flex snap-x snap-mandatory gap-8 max-lg:mr-8 max-lg:flex-1 max-lg:overflow-x-auto">
            @foreach ($currencies as $currency)
                <div class="flex shrink-0 snap-center items-center gap-2" title="{{ $currency['label'] }}">
                    <img alt="{{ $currency['label'] }}" width="15" height="15"
                         class="box-content h-[15px] w-[15px] shrink-0 rounded-full p-2 select-none [image-rendering:pixelated] {{ $currency['bg'] }}"
                         src="{{ asset('assets/images/icons/currency/' . $currency['icon'] . '.png') }}">
                    <span class="text-sm font-medium">{{ number_format($currency['amount'], 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-2 self-center lg:flex-1">
            <a href="{{ route('me.show') }}#online-friends" title="{{ __('Online Friends') }}"
               class="flex items-center gap-2 rounded-full bg-black/5 px-3.5 py-1.5 text-lg font-medium max-[320px]:hidden dark:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-city-blue" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                </svg>
                <span>{{ $onlineFriendsCount }}</span>
            </a>
        </div>
    </div>
@endauth

@guest
    <section @class([
        'city-hero border-b border-black text-white',
        'min-h-[384px]' => $isWelcomePage,
    ])>
        @if (setting('cms_header'))
            <div class="city-hero-art max-sm:opacity-30" style="background-image: url({{ setting('cms_header') }});"></div>
        @endif

        <div @class([
            'relative mx-auto max-w-[1128px] px-6',
            'pt-12 pb-20' => $isWelcomePage,
            'py-10' => ! $isWelcomePage,
        ])>
            <div class="max-w-[554px]">
                <h1 class="mb-2 text-2xl font-bold drop-shadow-md">
                    {{ setting('hotel_name') }}
                </h1>

                <p class="mb-2 font-bold">
                    {{ __('An online virtual world where you can create your own avatar, make friends, chat, create rooms and much more!') }}
                </p>

                <div class="mb-8 mt-4 flex flex-wrap items-center gap-2">
                    <span class="city-btn-dark">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-green-400"></span>
                        </span>
                        {{ __('There is currently :online users online', ['online' => $onlineCount]) }}
                    </span>

                    @unless ($isWelcomePage)
                        <x-modals.modal-wrapper>
                            <button type="button" x-on:click="open = true" class="city-btn-dark">
                                {{ __('Login') }}
                            </button>

                            <x-modals.regular-modal x-model="show {{ session()->get('wrong-auth') }}">
                                <x-auth.login-form />
                            </x-modals.regular-modal>
                        </x-modals.modal-wrapper>
                    @endunless
                </div>

                <a data-turbolinks="false" href="{{ route('register') }}" class="city-btn-play">
                    {{ __('Create an account') }}
                </a>
            </div>
        </div>
    </section>
@endguest

@guest
    @if ($isWelcomePage)
        <section class="relative z-10 mx-6 -mt-8 mb-2 w-auto max-w-[744px] md:mx-auto md:-mt-12">
            <x-auth.hero-login-form />
        </section>
    @endif
@endguest
