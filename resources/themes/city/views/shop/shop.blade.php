@php
    $currencyCode = \App\Support\StorefrontMoney::currencyCode();
    $symbol = ['EUR' => '€', 'USD' => '$', 'GBP' => '£'][$currencyCode] ?? $currencyCode . ' ';
    $money = fn (int $minor) => trim($symbol) . ' ' . number_format($minor / 100, 2, ',', '.');
    $paypalEnabled = \App\Support\PaypalConfiguration::isConfigured();
    $topUpAmounts = [5, 10, 20, 30, 50, 100];
    $activeCategory = request()->route('category');
    $balance = (int) auth()->user()->website_balance;
@endphp

<x-app-layout>
    @push('title', __('Shop'))

    <div class="col-span-12 -mt-10" x-data="{ terms: false, gift: null, confirm: null }" x-on:keydown.escape.window="terms = false; gift = null; confirm = null">
        {{-- Category bar --}}
        <nav class="city-bleed flex gap-2 overflow-x-auto border-b border-[#e4e4e7] py-3 select-none dark:border-white/10">
            <a href="{{ route('shop.index') }}" @class(['nav-item whitespace-nowrap', 'bg-black/5 text-black dark:bg-white/10 dark:text-white' => ! $activeCategory])>
                {{ __('All packages') }}
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('shop.index', $category->slug) }}" wire:key="shop-cat-{{ $category->id }}"
                   @class(['nav-item gap-2 whitespace-nowrap', 'bg-black/5 text-black dark:bg-white/10 dark:text-white' => $activeCategory?->is($category)])>
                    @if ($category->icon)
                        <img src="{{ $category->icon }}" alt="" class="max-h-5 [image-rendering:pixelated]">
                    @endif
                    {{ $category->name }}
                </a>
            @endforeach
        </nav>

        {{-- Top up hero --}}
        <div class="city-bleed py-14">
            <section class="flex gap-16 max-md:flex-col">
                <div class="flex flex-col md:max-w-[340px]">
                    <img alt="" class="mb-4 max-h-[250px] max-w-full self-center object-contain [image-rendering:pixelated]"
                         src="{{ asset('assets/images/shop/store.png') }}"
                         onerror="this.onerror=null;this.src='{{ asset('assets/images/hotel.png') }}';">

                    <div class="text-center font-medium text-[#52525b] dark:text-gray-400">
                        <p class="mb-4">
                            {!! __('Welcome to the :hotel shop! Here you can top up your balance with <strong>real</strong> money. Always ask your parents or guardians for permission.', ['hotel' => e(setting('hotel_name'))]) !!}
                        </p>
                        <p>
                            <button type="button" class="font-medium text-city-blue hover:underline" x-on:click="terms = true">
                                {{ __('Read the shop terms & conditions') }}
                            </button>
                        </p>
                    </div>
                </div>

                <div class="w-full">
                    <div class="mb-6 flex flex-row items-center gap-2 max-lg:flex-col max-lg:items-start max-lg:gap-1">
                        <h1 class="text-xl font-bold text-yellow-700 dark:text-yellow-400">{{ __('Top up balance') }}</h1>
                        <span class="city-chip w-fit">{{ __('Balance') }}: <strong class="ml-1">{{ $money($balance) }}</strong></span>
                    </div>

                    @if ($paypalEnabled)
                        <div class="grid grid-cols-2 gap-4 max-lg:grid-cols-1 max-sm:grid-cols-2 max-[480px]:grid-cols-1">
                            @foreach ($topUpAmounts as $amount)
                                <form method="POST" action="{{ route('paypal.process-transaction') }}" data-turbolinks="false">
                                    @csrf
                                    <input type="hidden" name="amount" value="{{ $amount }}">
                                    <button type="submit"
                                            class="flex w-full flex-col items-start gap-2 rounded-xl bg-yellow-500/5 p-3 text-left ring-1 ring-yellow-500 transition-all select-none hover:bg-yellow-500/10 hover:ring-2 active:bg-yellow-500/20">
                                        <span class="text-lg font-bold">
                                            <span class="font-medium text-[#52525b] dark:text-gray-400">{{ __('Top up') }}</span>
                                            {{ $money($amount * 100) }}
                                        </span>
                                        <span class="text-sm leading-tight text-[#52525b] dark:text-gray-400">{{ __('Pay with PayPal') }}</span>
                                    </button>
                                </form>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('paypal.process-transaction') }}" data-turbolinks="false" class="mt-4 flex gap-2 max-[380px]:flex-col">
                            @csrf
                            <label class="relative block flex-1">
                                <span class="sr-only">{{ __('Other amount') }}</span>
                                <span class="city-input-icon font-bold text-[#52525b]!">{{ trim($symbol) }}</span>
                                <input type="number" name="amount" min="1" max="250" step="1" required placeholder="{{ __('Other amount') }}" class="city-input">
                            </label>
                            <button type="submit" class="rounded-lg bg-yellow-500 px-5 py-2.5 font-bold text-black transition hover:bg-yellow-400">
                                {{ __('Top up') }}
                            </button>
                        </form>
                    @else
                        <div class="rounded-xl bg-yellow-500/10 p-4 text-sm ring-1 ring-yellow-500">
                            {{ __('Topping up is not available right now. Use a voucher below or try again later.') }}
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <div class="mb-10 mt-8 flex gap-6 max-md:flex-col">
            {{-- Packages --}}
            <section class="city-tile h-fit w-full flex-1 p-0">
                <h2 class="border-b border-[#e4e4e7] p-6 text-lg font-bold text-green-600 dark:border-white/10">
                    {{ $activeCategory?->name ?? __('Packages') }}
                </h2>

                <div class="flex flex-col gap-3 p-6 max-[380px]:p-3">
                    @forelse ($shopPackages as $package)
                        @php
                            $available = $package->isAvailable();
                        @endphp

                        <div class="flex gap-3 rounded-xl border border-[#e4e4e7] p-3 dark:border-white/10" wire:key="package-{{ $package->id }}">
                            <div class="flex w-16 shrink-0 items-center justify-center rounded-lg bg-[#ececef] max-[380px]:hidden dark:bg-city-ink">
                                @if ($package->image)
                                    <img alt="" src="{{ Storage::url($package->image) }}" class="max-h-14 max-w-14 [image-rendering:pixelated]">
                                @else
                                    <img alt="" src="{{ asset('assets/images/icons/currency/credits.png') }}" width="15" height="15">
                                @endif
                            </div>

                            <div class="flex w-full flex-wrap gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="mb-1 text-lg leading-snug font-medium">{{ $package->name }}</h3>

                                    @if ($package->description)
                                        <p class="mb-2 text-sm text-[#52525b] dark:text-gray-400">{{ $package->description }}</p>
                                    @endif

                                    <div class="flex flex-wrap gap-2">
                                        <span class="city-chip bg-green-500/15! font-bold text-green-700! dark:text-green-400!">{{ $money((int) $package->price) }}</span>

                                        @foreach ($package->items as $item)
                                            <span class="city-chip gap-1">
                                                @if ($item->image)
                                                    <img src="{{ Storage::url($item->image) }}" alt="" class="max-h-4 [image-rendering:pixelated]">
                                                @endif
                                                {{ $item->pivot->quantity }}x {{ $item->name }}
                                            </span>
                                        @endforeach

                                        @if ($package->stock !== null)
                                            <span class="city-chip">{{ __(':stock remaining', ['stock' => $package->stock]) }}</span>
                                        @endif

                                        @if ($package->limit_per_user)
                                            <span class="city-chip">{{ __('Limit: :limit per user', ['limit' => $package->limit_per_user]) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex h-auto w-fit items-end gap-2">
                                    @if ($available)
                                        @if ($package->is_giftable)
                                            <button type="button" class="rounded-lg bg-black/5 px-3 py-2 text-sm font-bold transition hover:bg-black/10 dark:bg-white/10" x-on:click="gift = {{ $package->id }}">
                                                {{ __('Gift') }}
                                            </button>
                                        @endif
                                        <button type="button" class="city-btn-success px-4! py-2! text-sm!" x-on:click="confirm = {{ $package->id }}">
                                            {{ __('Buy') }}
                                        </button>
                                    @else
                                        <span class="city-chip">{{ __('Not available') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($available)
                            <x-city-modal show="confirm === {{ $package->id }}" close="confirm = null" :title="__('Buy :package', ['package' => $package->name])">
                                <p class="mb-4">{{ __('Are you sure you want to buy :package for :price? This will be taken from your balance.', ['package' => $package->name, 'price' => $money((int) $package->price)]) }}</p>
                                <p class="mb-4 text-sm text-[#71717a]">{{ __('Balance') }}: {{ $money($balance) }}</p>
                                <form method="POST" action="{{ route('shop.buy-package', $package) }}">
                                    @csrf
                                    <button type="submit" class="city-btn-success w-full">{{ __('Buy for :price', ['price' => $money((int) $package->price)]) }}</button>
                                </form>
                            </x-city-modal>

                            @if ($package->is_giftable)
                                <x-city-modal show="gift === {{ $package->id }}" close="gift = null" :title="__('Gift :package', ['package' => $package->name])">
                                    <form method="POST" action="{{ route('shop.buy-package', $package) }}" class="flex flex-col gap-3">
                                        @csrf
                                        <label class="font-bold" for="receiver-{{ $package->id }}">{{ __('Who do you want to give this to?') }}</label>
                                        <input id="receiver-{{ $package->id }}" type="text" name="receiver" required placeholder="{{ __('Username') }}..." class="city-input pl-4!">
                                        <button type="submit" class="city-btn-success w-full">{{ __('Gift for :price', ['price' => $money((int) $package->price)]) }}</button>
                                    </form>
                                </x-city-modal>
                            @endif
                        @endif
                    @empty
                        <p class="text-center text-[#71717a]">{{ __('There are no packages in this category yet.') }}</p>
                    @endforelse
                </div>
            </section>

            {{-- Voucher --}}
            <aside class="flex w-full flex-col gap-6 md:max-w-[352px]">
                <section class="city-tile h-fit p-0">
                    <h2 class="border-b border-[#e4e4e7] p-6 text-lg font-bold text-green-600 dark:border-white/10">{{ __('Voucher') }}</h2>

                    <form method="POST" action="{{ route('shop.use-voucher') }}" class="flex flex-col gap-3 p-6">
                        @csrf
                        <p class="text-sm text-[#52525b] dark:text-gray-400">{{ __('Use a voucher for free credit') }}</p>
                        <input type="text" name="code" required placeholder="{{ __('Voucher code') }}..." class="city-input pl-4!">

                        @if (setting('google_recaptcha_enabled'))
                            <div class="g-recaptcha" data-sitekey="{{ config('habbo.site.recaptcha_site_key') }}"></div>
                        @endif

                        @if (setting('cloudflare_turnstile_enabled'))
                            <x-turnstile />
                        @endif

                        <button type="submit" class="w-full rounded-lg bg-city-blue px-4 py-2.5 font-bold text-white transition hover:bg-city-blue-dark">
                            {{ __('Use voucher') }}
                        </button>
                    </form>
                </section>
            </aside>
        </div>

        {{-- Terms --}}
        <x-city-modal show="terms" close="terms = false" :title="__('Shop Terms & Conditions')" size="max-w-2xl">
            <div class="flex flex-col gap-5 text-sm">
                <p>{{ __('Here at :hotel Hotel we are accepting donations to keep the hotel up & running and as a thank you, you will in return receive in-game goods.', ['hotel' => setting('hotel_name')]) }}</p>
                <div>
                    <p class="mb-1 font-bold">{{ __('Why are donations important?') }}</p>
                    <p>{{ __('Donations are important, as it will help to pay our monthly bills needed to keep the hotel up & running, as well as adding new and exciting features for you and others to enjoy!') }}</p>
                </div>
                <div>
                    <p class="mb-1 font-bold">{{ __('Our terms') }}</p>
                    <p>{{ __('Once a donation has been made and received by us, it is non-refundable under any circumstances. The donated amount which is converted into website balance cannot be converted back into cash or other forms of money. By making a donation, you acknowledge and accept these terms and agree not to initiate a chargeback or dispute with your bank or card issuer.') }}</p>
                </div>
                <div>
                    <p class="mb-1 font-bold">{{ __('Notice') }}</p>
                    <p>{{ __('It is important to consider the consequences of our spending habits, especially when it comes to financial decisions. If you find yourself tempted to spend money you do not have, take a moment to reflect.') }}</p>
                </div>
            </div>
        </x-city-modal>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
