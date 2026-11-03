<x-app-layout>
    @push('title', __('Account settings'))

    @php
        $preferenceLabels = [
            'friend_requests' => __('Do you want other :hotel users to be able to send you a friend request?', ['hotel' => setting('hotel_name')]),
            'room_invites' => __('May other :hotel users invite you to their rooms?', ['hotel' => setting('hotel_name')]),
            'following' => __('May other :hotel users follow you into rooms?', ['hotel' => setting('hotel_name')]),
            'camera_follow' => __('Should the camera follow your avatar in rooms?'),
            'gifts' => __('May other :hotel users send you gifts?', ['hotel' => setting('hotel_name')]),
            'mimic' => __('Do you want other :hotel users to be able to copy your look?', ['hotel' => setting('hotel_name')]),
            'old_chat' => __('Do you want to use the old chat style?'),
        ];

        [$mailName, $mailDomain] = array_pad(explode('@', (string) $user->mail, 2), 2, '');
        $maskedMail = mb_substr($mailName, 0, 1) . str_repeat('*', max(mb_strlen($mailName) - 2, 1)) . (mb_strlen($mailName) > 1 ? mb_substr($mailName, -1) : '') . ($mailDomain !== '' ? '@' . $mailDomain : '');

        $submittedForm = old('_form');
        $scamWarning = __('If someone told you to change your details in exchange for a reward (credits, furni, rights, a new car...), do not change your details but report this scammer to the Moderators!');
    @endphp

    <div class="col-span-12 flex flex-col gap-8 lg:flex-row">
        <main class="flex w-full min-w-0 flex-col gap-14">
            {{-- Preferences --}}
            @if ($preferences !== [])
                <section>
                    <x-section-title icon="sliders">{{ __('My preferences') }}</x-section-title>

                    <form action="{{ route('settings.preferences.update') }}" method="POST" class="city-tile p-4">
                        @method('PUT')
                        @csrf

                        @foreach ($preferences as $key => $enabled)
                            <label class="flex cursor-pointer items-center gap-4 rounded-lg p-1 transition duration-75 hover:bg-[#f4f4f6] dark:hover:bg-white/5"
                                   x-data="{ on: @js((bool) old($key, $enabled)) }">
                                <input type="hidden" name="{{ $key }}" :value="on ? 1 : 0" value="{{ old($key, $enabled) ? 1 : 0 }}">

                                <button type="button" role="switch" :aria-checked="on.toString()" aria-checked="{{ $enabled ? 'true' : 'false' }}"
                                        x-on:click="on = ! on"
                                        :class="on ? 'bg-green-500' : 'bg-[#52525b]'"
                                        @class(['relative flex w-14 shrink-0 items-center rounded-full p-1 outline-black transition-all duration-75 focus:outline-2', 'bg-green-500' => $enabled, 'bg-[#52525b]' => ! $enabled])>
                                    <span class="flex shrink-0 items-center justify-center rounded-full bg-white p-1 shadow transition-all"
                                          :class="on ? 'translate-x-[24px] text-green-500' : 'translate-x-0 text-[#52525b]'">
                                        <svg x-show="on" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" aria-hidden="true" @unless ($enabled) style="display: none;" @endunless>
                                            <path d="M232.49,80.49l-128,128a12,12,0,0,1-17,0l-56-56a12,12,0,1,1,17-17L96,183,215.51,63.51a12,12,0,0,1,17,17Z" />
                                        </svg>
                                        <svg x-show="! on" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" aria-hidden="true" @if ($enabled) style="display: none;" @endif>
                                            <path d="M208.49,191.51a12,12,0,0,1-17,17L128,145,64.49,208.49a12,12,0,0,1-17-17L111,128,47.51,64.49a12,12,0,0,1,17-17L128,111l63.51-63.52a12,12,0,0,1,17,17L145,128Z" />
                                        </svg>
                                    </span>
                                </button>

                                <span>{{ $preferenceLabels[$key] ?? $key }}</span>
                            </label>
                        @endforeach

                        @error('preferences', 'preferences')
                            <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <hr class="my-6 border-[#e4e4e7] dark:border-white/10">

                        <button type="submit" class="city-btn-ghost px-6! py-3! font-bold">{{ __('Save') }}</button>
                    </form>
                </section>
            @endif

            {{-- Profile --}}
            <section>
                <x-section-title icon="user">{{ __('My profile') }}</x-section-title>

                <form action="{{ route('settings.account.update') }}" method="POST" class="city-tile p-4">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="_form" value="profile">
                    <input type="hidden" name="mail" value="{{ $user->mail }}">

                    <div class="mb-6 flex flex-col gap-2">
                        @if ($canChangeName)
                            <label class="block">
                                <span class="mb-1 block text-sm font-medium">{{ __('Username') }}</span>
                                <input name="username" type="text" value="{{ $submittedForm === 'profile' ? old('username', $user->username) : $user->username }}" maxlength="25"
                                       @class(['city-input pl-3!', 'city-input-error' => $submittedForm === 'profile' && $errors->has('username')])>
                            </label>
                        @endif

                        <label class="block">
                            <span class="mb-1 block text-sm font-medium">{{ __('Motto') }}</span>
                            <input name="motto" type="text" value="{{ $submittedForm === 'profile' ? old('motto', $user->motto) : $user->motto }}"
                                   @class(['city-input pl-3!', 'city-input-error' => $submittedForm === 'profile' && $errors->has('motto')])>
                        </label>

                        @if ($submittedForm === 'profile')
                            @foreach (['account', 'username', 'motto', 'mail'] as $field)
                                @error($field)
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            @endforeach
                        @endif
                    </div>

                    <button type="submit" class="city-btn-ghost px-6! py-3! font-bold">{{ __('Save') }}</button>
                </form>
            </section>

            {{-- Change e-mail --}}
            <section>
                <x-section-title icon="mail">{{ __('Change e-mail') }}</x-section-title>

                <form action="{{ route('settings.account.update') }}" method="POST" class="city-tile p-4">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="_form" value="mail">
                    <input type="hidden" name="motto" value="{{ $user->motto }}">

                    <div class="mb-6">{{ $scamWarning }}</div>

                    <hr class="my-6 border-[#e4e4e7] dark:border-white/10">

                    <div class="mb-6 flex flex-col gap-2">
                        <input disabled type="text" value="{{ $maskedMail }}" class="w-full rounded-xl bg-[#ececef] px-4 py-2 font-bold dark:bg-white/10" aria-label="{{ __('Current e-mail') }}">

                        <label class="relative block">
                            <span class="sr-only">{{ __('Current password') }}</span>
                            <x-city-input-icon type="lock" />
                            <input name="current_password" type="password" autocomplete="current-password" placeholder="{{ __('Current password') }}" required
                                   @class(['city-input', 'city-input-error' => $submittedForm === 'mail' && $errors->has('current_password')])>
                        </label>

                        <label class="relative block">
                            <span class="sr-only">{{ __('E-mail address') }}</span>
                            <x-city-input-icon type="mail" />
                            <input name="mail" type="email" autocomplete="email" placeholder="{{ __('E-mail address') }}" required
                                   value="{{ $submittedForm === 'mail' ? old('mail') : '' }}"
                                   @class(['city-input', 'city-input-error' => $submittedForm === 'mail' && $errors->has('mail')])>
                        </label>

                        @if ($submittedForm === 'mail')
                            @foreach (['account', 'current_password', 'mail'] as $field)
                                @error($field)
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            @endforeach
                        @endif
                    </div>

                    <button type="submit" class="city-btn-ghost px-6! py-3! font-bold">{{ __('Save') }}</button>
                </form>
            </section>

            {{-- Change password --}}
            <section>
                <x-section-title icon="key">{{ __('Change password') }}</x-section-title>

                <form action="{{ route('settings.password.update') }}" method="POST" class="city-tile p-4">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="_form" value="password">
                    <input type="hidden" name="redirect" value="account">

                    <div class="mb-6">{{ $scamWarning }}</div>

                    <hr class="my-6 border-[#e4e4e7] dark:border-white/10">

                    <div class="mb-6 flex flex-col gap-2">
                        @foreach (['current_password' => __('Current password'), 'password' => __('New password'), 'password_confirmation' => __('Repeat new password')] as $field => $label)
                            <label class="relative block">
                                <span class="sr-only">{{ $label }}</span>
                                <x-city-input-icon type="lock" />
                                <input name="{{ $field }}" type="password" placeholder="{{ $label }}" required
                                       autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}"
                                       @class(['city-input', 'city-input-error' => $submittedForm === 'password' && $errors->has($field)])>
                            </label>
                        @endforeach

                        @if ($submittedForm === 'password')
                            @foreach (['current_password', 'password'] as $field)
                                @error($field)
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            @endforeach
                        @endif
                    </div>

                    <button type="submit" class="city-btn-ghost px-6! py-3! font-bold">{{ __('Save') }}</button>
                </form>
            </section>
        </main>

        <aside class="flex w-full flex-col gap-12 lg:max-w-[352px]">
            {{-- Two-factor authentication --}}
            <section>
                <x-section-title icon="lock">{{ __('Two-factor authentication') }}</x-section-title>

                <div class="city-tile p-4">
                    @if ($user->hasEnabledTwoFactorAuthentication())
                        <span class="mb-3 block text-lg font-medium">{{ __('Your account is extra secure!') }}</span>
                        <p class="leading-tight text-[#52525b] dark:text-gray-400">{{ __('Two-factor authentication is enabled on your account.') }}</p>
                    @else
                        <span class="mb-3 block text-lg font-medium">{{ __('Oh! You have no two-factor authentication yet...') }}</span>
                        <p class="leading-tight text-[#52525b] dark:text-gray-400">{{ __('Two-factor authentication keeps your account extra safe, enable it today!') }}</p>
                    @endif

                    <hr class="my-6 border-[#e4e4e7] dark:border-white/10">

                    <a href="{{ route('settings.two-factor') }}" class="city-btn-ghost px-6! py-3! font-bold">
                        {{ $user->hasEnabledTwoFactorAuthentication() ? __('Manage two-factor') : __('Enable two-factor') }}
                    </a>
                </div>
            </section>

            {{-- Session logs --}}
            <section>
                <x-section-title icon="clock">{{ __('Session logs') }}</x-section-title>

                <div class="city-tile p-4">
                    <p class="leading-tight text-[#52525b] dark:text-gray-400">{{ __('See where and when your account was used to log in.') }}</p>

                    <hr class="my-6 border-[#e4e4e7] dark:border-white/10">

                    <a href="{{ route('settings.session-logs') }}" class="city-btn-ghost px-6! py-3! font-bold">{{ __('View session logs') }}</a>
                </div>
            </section>

            {{-- Remove friends --}}
            <section x-data="{ confirming: false }">
                <x-section-title icon="user-minus" icon-classes="bg-red-500! ring-red-500/15!">{{ __('Remove friends') }}</x-section-title>

                <form action="{{ route('settings.friends.destroy') }}" method="POST" class="city-tile p-4">
                    @method('DELETE')
                    @csrf

                    <p class="leading-tight text-[#52525b] dark:text-gray-400">
                        {{ __('Remove all your friends and friend requests from your friend list.') }}
                        <strong>{{ __('Note: this cannot be undone!') }}</strong>
                    </p>

                    @error('friends', 'friends')
                        <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <hr class="my-6 border-[#e4e4e7] dark:border-white/10">

                    <button type="button" x-show="! confirming" x-on:click="confirming = true"
                            class="inline-flex items-center justify-center rounded-xl bg-red-500 px-6 py-3 font-bold text-white transition hover:bg-red-600">
                        {{ __('Remove my friends') }}
                    </button>

                    <div x-show="confirming" style="display: none;" class="flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-500 px-6 py-3 font-bold text-white transition hover:bg-red-600">
                            {{ __('Yes, remove them') }}
                        </button>
                        <button type="button" x-on:click="confirming = false" class="city-btn-ghost px-6! py-3! font-bold">{{ __('Cancel') }}</button>
                    </div>
                </form>
            </section>
        </aside>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
