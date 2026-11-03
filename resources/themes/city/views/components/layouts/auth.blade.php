@props(['heading' => ''])

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


    @livewireStyles
    @livewireScriptConfig
    @vite(['resources/themes/' . setting('theme', 'city') . '/css/app.css', 'resources/themes/' . setting('theme', 'city') . '/js/app.js'], 'build')


    @if (setting('google_recaptcha_enabled'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    @stack('scripts')
    <x-turnstile.scripts />
</head>

<body class="city-auth-bg min-h-screen text-city-ink">
    <x-messages.flash-messages />

    <main class="mx-auto flex min-h-screen w-[calc(100%-32px)] max-w-[600px] flex-col items-center justify-center gap-8 pb-12 sm:w-[calc(100%-64px)]">
        <header class="flex flex-col items-center">
            <a href="{{ route('welcome') }}">
                <img class="mb-6 mt-8 max-h-[102px] select-none" src="{{ setting('cms_logo') }}" alt="{{ setting('hotel_name') }}">
            </a>

            @if ($heading)
                <h1 class="m-0 select-none text-center text-2xl font-bold text-white drop-shadow-sm">{{ $heading }}</h1>
            @endif
        </header>

        <section class="city-auth-card flex w-full flex-col gap-8 rounded-3xl bg-white px-6 py-10 sm:px-12 sm:py-14 dark:bg-city-ink-soft dark:text-gray-200">
            {{ $slot }}
        </section>
    </main>

    @stack('javascript')
</body>
</html>
