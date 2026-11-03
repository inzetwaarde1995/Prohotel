<div class="mb-6 flex flex-col rounded-xl bg-white p-2 shadow-[0_2px_0_0_rgba(0,0,0,0.05)] dark:bg-city-ink-soft">
    <h2 class="mb-4 flex rounded-lg bg-city-blue px-4 py-2 font-bold text-white shadow-[0_2px_0_0_rgba(0,0,0,0.05)]">
        {{ __('Login to :hotel', ['hotel' => setting('hotel_name')]) }}
    </h2>

    <form class="px-4" action="{{ route('login') }}" method="POST">
        @csrf

        <label class="relative mb-3 block">
            <span class="sr-only">{{ __('Username') }}</span>
            <span class="city-input-icon">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                </svg>
            </span>
            <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required
                   placeholder="{{ __('Username') }}..."
                   @class(['city-input', 'city-input-error' => $errors->login->has('username')])>
        </label>

        <label class="relative mb-3 block">
            <span class="sr-only">{{ __('Password') }}</span>
            <span class="city-input-icon">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8 7a5 5 0 113.61 4.804l-1.903 1.903A1 1 0 019 14H8v1a1 1 0 01-1 1H6v1a1 1 0 01-1 1H3a1 1 0 01-1-1v-2a1 1 0 01.293-.707L8.196 8.39A5.002 5.002 0 018 7zm5-3a.75.75 0 000 1.5A1.5 1.5 0 0114.5 7 .75.75 0 0016 7a3 3 0 00-3-3z" clip-rule="evenodd" />
                </svg>
            </span>
            <input id="password" name="password" type="password" autocomplete="current-password" required
                   placeholder="{{ __('Password') }}..."
                   @class(['city-input', 'city-input-error' => $errors->login->has('password')])>
        </label>

        @foreach (['username', 'password'] as $field)
            @error($field, 'login')
                <p class="mb-3 text-sm text-red-500">{{ $message }}</p>
            @enderror
        @endforeach

        @if (setting('google_recaptcha_enabled'))
            <div class="g-recaptcha mb-3" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
        @endif

        @if (setting('cloudflare_turnstile_enabled'))
            <div class="mb-3"><x-turnstile /></div>
        @endif

        <button type="submit" class="city-btn-success mb-3 w-full">
            {{ __('Login') }}
        </button>
    </form>

    <a href="{{ route('forgot.password.get') }}" class="mx-auto mb-2 w-fit font-medium text-city-blue hover:underline">
        {{ __('Did you forget your password?') }}
    </a>
</div>
