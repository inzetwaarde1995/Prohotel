@php
    $registrationLooks = config('habbo.registration_looks', []);
    $registerErrors = $errors->getBag('register')->merge($errors->getBag('default'));

    $stepFields = [
        1 => ['username', 'mail', 'registration'],
        2 => ['password', 'password_confirmation', 'beta_code'],
        3 => ['gender', 'look', 'terms', 'g-recaptcha-response', 'cf-turnstile-response'],
    ];

    $initialStep = 1;
    foreach ($stepFields as $step => $fields) {
        foreach ($fields as $field) {
            if ($registerErrors->has($field)) {
                $initialStep = $step;
                break 2;
            }
        }
    }

    $oldGender = old('gender', 'M') === 'F' ? 'F' : 'M';
    $oldLook = old('look', $registrationLooks[$oldGender][0] ?? '');
@endphp

<x-layouts.auth :heading="__('Create a free account!')">
    @push('title', __('Create account'))

    <form method="POST" action="{{ route('register.store') }}" novalidate
          x-data="{
              step: {{ $initialStep }},
              gender: @js($oldGender),
              look: @js($oldLook),
              looks: @js($registrationLooks),
              next() {
                  const fields = [...this.$refs['step' + this.step].querySelectorAll('input')];
                  const invalid = fields.find((field) => ! field.checkValidity());

                  if (invalid) {
                      invalid.reportValidity();
                      return;
                  }

                  if (this.step === 2 && this.$refs.password.value !== this.$refs.passwordConfirmation.value) {
                      this.$refs.passwordConfirmation.setCustomValidity(@js(__('The passwords do not match.')));
                      this.$refs.passwordConfirmation.reportValidity();
                      return;
                  }

                  this.step++;
              },
              back() {
                  if (this.step === 1) {
                      window.location.href = @js(route('welcome'));
                      return;
                  }

                  this.step--;
              },
              pickGender(gender) {
                  this.gender = gender;
                  this.look = this.looks[gender][0];
              },
          }"
          x-on:keydown.enter="if (step < 3 && $event.target.tagName === 'INPUT') { $event.preventDefault(); next(); }">
        @csrf

        <input type="hidden" name="referral_code" value="{{ $referral_code ?? '' }}">
        <input type="hidden" name="terms" value="1">

        <header class="mb-8 flex w-full items-center justify-between">
            <button type="button" x-on:click="back()" class="city-btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                </svg>
                {{ __('Go back') }}
            </button>

            <span class="text-lg">
                {{ __('Step') }} <span x-text="step">{{ $initialStep }}</span> {{ __('of') }} 3
            </span>
        </header>

        @if ($registerErrors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
                @foreach ($registerErrors->all() as $message)
                    <p>{{ $message }}</p>
                @endforeach
            </div>
        @endif

        {{-- Step 1: name & email --}}
        <div x-ref="step1" x-show="step === 1" @if ($initialStep !== 1) style="display: none;" @endif>
            <label class="relative mb-4 block">
                <span class="sr-only">{{ __('Username') }}</span>
                <span class="city-input-icon">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input name="username" type="text" value="{{ old('username') }}" required maxlength="25" autocomplete="off"
                       placeholder="{{ __('Username') }}..."
                       @class(['city-input', 'city-input-error' => $registerErrors->has('username')])>
            </label>

            <label class="relative mb-4 block">
                <span class="sr-only">{{ __('Email') }}</span>
                <span class="city-input-icon text-city-slate!">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                        <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                    </svg>
                </span>
                <input name="mail" type="email" value="{{ old('mail') }}" required autocomplete="email"
                       placeholder="{{ __('Email') }}..."
                       @class(['city-input', 'city-input-error' => $registerErrors->has('mail')])>
            </label>

            <p class="mb-4 text-center font-bold">
                {{ __('Your username is what other users will know you as, so pick one you like!') }}
            </p>

            <button type="button" x-on:click="next()" class="city-btn-success mt-6 w-full">
                {{ __('Next') }}
            </button>
        </div>

        {{-- Step 2: password --}}
        <div x-ref="step2" x-show="step === 2" @if ($initialStep !== 2) style="display: none;" @endif>
            <label class="relative mb-4 block">
                <span class="sr-only">{{ __('Password') }}</span>
                <span class="city-input-icon">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 7a5 5 0 113.61 4.804l-1.903 1.903A1 1 0 019 14H8v1a1 1 0 01-1 1H6v1a1 1 0 01-1 1H3a1 1 0 01-1-1v-2a1 1 0 01.293-.707L8.196 8.39A5.002 5.002 0 018 7zm5-3a.75.75 0 000 1.5A1.5 1.5 0 0114.5 7 .75.75 0 0016 7a3 3 0 00-3-3z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input x-ref="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                       placeholder="{{ __('Password') }}..."
                       @class(['city-input', 'city-input-error' => $registerErrors->has('password')])>
            </label>

            <label class="relative mb-4 block">
                <span class="sr-only">{{ __('Repeat Password') }}</span>
                <span class="city-input-icon">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 7a5 5 0 113.61 4.804l-1.903 1.903A1 1 0 019 14H8v1a1 1 0 01-1 1H6v1a1 1 0 01-1 1H3a1 1 0 01-1-1v-2a1 1 0 01.293-.707L8.196 8.39A5.002 5.002 0 018 7zm5-3a.75.75 0 000 1.5A1.5 1.5 0 0114.5 7 .75.75 0 0016 7a3 3 0 00-3-3z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input x-ref="passwordConfirmation" x-on:input="$el.setCustomValidity('')" name="password_confirmation" type="password" required autocomplete="new-password"
                       placeholder="{{ __('Repeat Password') }}..."
                       class="city-input">
            </label>

            @if (setting('requires_beta_code'))
                <label class="relative mb-4 block">
                    <span class="sr-only">{{ __('Beta code') }}</span>
                    <span class="city-input-icon text-city-slate!">#</span>
                    <input name="beta_code" type="text" value="{{ old('beta_code') }}" required autocomplete="off"
                           placeholder="{{ __('Enter your beta code') }}..."
                           @class(['city-input', 'city-input-error' => $registerErrors->has('beta_code')])>
                </label>
            @endif

            <p class="mb-4 text-center font-bold">
                {{ __('Pick a new password that you have never used anywhere else. That keeps your account extra safe!') }}
            </p>

            <button type="button" x-on:click="next()" class="city-btn-success mt-6 w-full">
                {{ __('Next') }}
            </button>
        </div>

        {{-- Step 3: avatar --}}
        <div x-ref="step3" x-show="step === 3" @if ($initialStep !== 3) style="display: none;" @endif>
            <input type="hidden" name="gender" :value="gender" value="{{ $oldGender }}">
            <input type="hidden" name="look" :value="look" value="{{ $oldLook }}">

            <div class="mb-6 flex w-full flex-row max-[320px]:flex-col" role="radiogroup" aria-label="{{ __('Gender') }}">
                <button type="button" role="radio" x-on:click="pickGender('M')" :aria-checked="gender === 'M'"
                        :class="gender === 'M' ? 'bg-city-blue text-white border-city-blue' : 'text-city-blue hover:bg-city-blue/10'"
                        class="flex-1 rounded-l-xl border border-[#d4d4d8] p-4 text-center text-lg font-medium transition duration-75 max-[320px]:rounded-none max-[320px]:rounded-t-xl dark:border-white/10">
                    {{ __('Boy') }}
                </button>
                <button type="button" role="radio" x-on:click="pickGender('F')" :aria-checked="gender === 'F'"
                        :class="gender === 'F' ? 'bg-purple-500 text-white border-purple-500' : 'text-purple-500 hover:bg-purple-500/10'"
                        class="flex-1 rounded-r-xl border border-l-0 border-[#d4d4d8] p-4 text-center text-lg font-medium transition duration-75 max-[320px]:rounded-none max-[320px]:rounded-b-xl max-[320px]:border-l dark:border-white/10">
                    {{ __('Girl') }}
                </button>
            </div>

            <div class="mb-3 flex flex-col gap-4 sm:flex-row" role="radiogroup" aria-label="{{ __('Look') }}">
                <template x-for="figure in looks[gender]" :key="figure">
                    <button type="button" role="radio" x-on:click="look = figure" :aria-checked="look === figure"
                            :class="look === figure ? 'bg-[#e8e8ee] dark:bg-white/10' : 'hover:bg-[#f4f4f6] dark:hover:bg-white/5'"
                            class="flex flex-1 items-center justify-center rounded-lg border border-[#e4e4e7] px-3 py-6 transition duration-75 dark:border-white/10">
                        <img width="64" height="110" alt="" class="pointer-events-none select-none object-none object-center [image-rendering:pixelated]"
                             :src="@js(setting('avatar_imager')) + figure + '&direction=3&head_direction=3&gesture=sml'">
                    </button>
                </template>
            </div>

            <p class="mt-6 text-sm font-medium">
                {{ __('By registering you agree to the') }}
                <a href="{{ route('help-center.rules.index') }}" target="_blank" class="underline hover:text-city-blue">{{ __(':hotel terms & rules.', ['hotel' => setting('hotel_name')]) }}</a>
            </p>

            @if (setting('google_recaptcha_enabled'))
                <div class="g-recaptcha mt-6 flex justify-center" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
            @endif

            @if (setting('cloudflare_turnstile_enabled'))
                <div class="mt-6 flex justify-center">
                    <x-turnstile />
                </div>
            @endif

            <button type="submit" class="city-btn-success mt-6 w-full">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-layouts.auth>
