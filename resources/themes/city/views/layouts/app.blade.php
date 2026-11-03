<!DOCTYPE html>
<html class="app" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="turbolinks-cache-control" content="no-cache">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-seo-meta />

    <link rel="icon" type="image/gif" sizes="18x17" href="{{ asset('assets/images/home_icon.gif') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap">

    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/tippy-bundle.umd.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/scale.min.css') }}"/>

    @livewireStyles
    @livewireScriptConfig
    @vite(['resources/themes/' . setting('theme', 'city') . '/css/app.css', 'resources/themes/' . setting('theme', 'city') . '/js/app.js'], 'build')
    @stack('scripts')
    <x-turnstile.scripts />
</head>

<body class="city-body flex min-h-screen flex-col text-city-ink dark:text-gray-200">
    @if(config('habbo.site.debug_mode_enabled') && config('habbo.site.site_environment') === 'production')
        <div class="w-full bg-red-500 px-4 py-2 text-center text-white">
            {{ __('It seems like debug mode is enabled while being in production. It is heavily recommended too set APP_DEBUG in the .env file to false in production mode') }}
        </div>
    @endif

    <x-messages.flash-messages />

    <div id="app">
        {{-- Header: logo + navigation --}}
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-3xl dark:bg-city-ink/80">
            <nav class="relative flex min-h-[64px] flex-col px-6 py-3 md:flex-row md:items-center md:justify-between md:gap-x-4">
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ auth()->check() ? route('me.show') : route('welcome') }}" class="flex shrink-0 items-center">
                        <img class="max-h-10" src="{{ setting('cms_logo') }}" alt="{{ setting('hotel_name') }}">
                    </a>

                    <x-navigation.navigation-menu />
                </div>

                <div class="hidden shrink-0 items-center gap-2 lg:flex">
                    <div class="flex items-center gap-2 rounded-full bg-black/5 px-3.5 py-2 text-sm font-medium text-city-ink dark:bg-white/10 dark:text-gray-200">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-500 opacity-40"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                        </span>
                        <span>
                            {{ __(':online :hotel online', ['online' => DB::table('users')->where('online', '1')->count(), 'hotel' => setting('hotel_name')]) }}
                        </span>
                    </div>

                    @auth
                        @if (hasPermission('housekeeping_access'))
                            <a data-turbolinks="false" href="{{ setting('housekeeping_url') }}" target="_blank"
                               title="{{ __('Housekeeping') }}" aria-label="{{ __('Housekeeping') }}"
                               class="flex h-9 w-9 items-center justify-center rounded-full bg-black/5 text-city-ink transition hover:scale-110 hover:bg-city-blue hover:text-white dark:bg-white/10 dark:text-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>
                        @endif
                    @endauth

                    <x-navigation.theme-mode-switcher />

                    <x-navigation.language-selector>
                        <img src="/assets/images/icons/flags/{{ session()->has('locale') ? session()->get('locale') : config('habbo.site.default_language') }}.png" alt="">
                    </x-navigation.language-selector>
                </div>

                <x-navigation.mobile-menu />
            </nav>
        </header>

        <x-site-header />

        <main class="overflow-hidden">
            <div class="mx-auto grid max-w-[1128px] grid-cols-12 gap-x-4 gap-y-8 px-6 py-10">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-footer />

    @if (setting('cloudflare_turnstile_enabled'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const turnstileWidget = document.querySelector('#cf-turnstile-widget');
                const theme = localStorage.getItem('theme');

                if (turnstileWidget && theme) {
                    turnstileWidget.setAttribute('data-theme', theme);
                }
            });
        </script>
    @endif

    @if (setting('cms_color_mode') === 'dark')
        <script>
            if (localStorage.getItem("theme") === null) {
                document.documentElement.classList.add("dark");
                localStorage.setItem("theme", 'dark');
            }
        </script>
    @endif

    @if (setting('google_recaptcha_enabled'))
        @push('javascript')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endpush
    @endif

    <script defer src="{{ asset('assets/js/alpine-ui.js') }}"></script>

    @stack('javascript')
</body>
</html>
