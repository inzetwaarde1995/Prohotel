@php
    $categoryList = $categories instanceof \Illuminate\Database\Eloquent\Model ? collect([$categories]) : collect($categories);

    $currencies = [
        0 => ['label' => __('Duckets'), 'icon' => 'duckets', 'bg' => 'bg-purple-500/10'],
        5 => ['label' => __('Diamonds'), 'icon' => 'diamonds', 'bg' => 'bg-sky-500/10'],
    ];

    $tabs = $categoryList
        ->filter(fn ($category) => $category->furniture->isNotEmpty())
        ->values()
        ->map(fn ($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'items' => $category->furniture->map(fn ($rare) => [
                'id' => $rare->id,
                'name' => $rare->name,
                'icon' => sprintf('%s/%s', setting('furniture_icons_path'), $rare->furniture_icon),
                'credits' => (int) preg_replace('/\D/', '', (string) $rare->credit_value),
                'currency' => (int) preg_replace('/\D/', '', (string) $rare->currency_value),
                'currencyLabel' => $currencies[$rare->currency_type]['label'] ?? __('Other'),
                'currencyIcon' => asset('assets/images/icons/currency/' . ($currencies[$rare->currency_type]['icon'] ?? 'diamonds') . '.png'),
                'currencyBg' => $currencies[$rare->currency_type]['bg'] ?? 'bg-sky-500/10',
                'ltd' => $rare->isLimitedEdition(),
                'url' => $rare->item_id ? route('values.value', $rare) : null,
            ])->values(),
        ]);
@endphp

<x-app-layout>
    @push('title', __('Rare values'))

    <div class="col-span-12"
         x-data="{
             tabs: @js($tabs),
             active: 0,
             sort: 'name-asc',
             search: '',
             get items() {
                 const tab = this.tabs[this.active];

                 if (! tab) {
                     return [];
                 }

                 const term = this.search.trim().toLowerCase();
                 const items = tab.items.filter((item) => term === '' || item.name.toLowerCase().includes(term));
                 const [field, direction] = this.sort.split('-');
                 const factor = direction === 'asc' ? 1 : -1;

                 return items.sort((a, b) => field === 'name'
                     ? a.name.localeCompare(b.name) * factor
                     : (a[field] - b[field]) * factor);
             },
             format(value) {
                 return new Intl.NumberFormat(@js(str_replace('_', '-', app()->getLocale()))).format(value);
             },
         }">
        <header class="mb-6 flex items-center justify-between gap-3 max-md:flex-col max-md:items-start">
            <x-section-title icon="coins" classes="mb-0! max-md:w-full max-md:border-b max-md:border-[#e4e4e7] max-md:pb-3 dark:max-md:border-white/10">
                {{ __('Rare values') }}
            </x-section-title>

            <div class="flex flex-wrap items-center gap-3 max-md:w-full">
                <label class="flex items-center gap-2 font-medium text-[#52525b] dark:text-gray-400">
                    <span class="sr-only">{{ __('Sort') }}</span>
                    <select x-model="sort" class="cursor-pointer rounded-lg border-0 bg-transparent py-2 pl-2 pr-8 font-medium focus:ring-2 focus:ring-city-blue/30">
                        <option value="name-asc">{{ __('Sort alphabetically (A-Z)') }}</option>
                        <option value="name-desc">{{ __('Sort alphabetically (Z-A)') }}</option>
                        <option value="credits-desc">{{ __('Value: high to low') }}</option>
                        <option value="credits-asc">{{ __('Value: low to high') }}</option>
                    </select>
                </label>

                <form x-on:submit.prevent class="flex gap-2 max-[380px]:w-full max-[380px]:flex-col" role="search">
                    <label class="max-[380px]:w-full">
                        <span class="sr-only">{{ __('Search') }}</span>
                        <input type="search" x-model.debounce.150ms="search" placeholder="{{ __('Search') }}..." class="city-input pl-4!">
                    </label>
                </form>
            </div>
        </header>

        <template x-if="tabs.length === 0">
            <div class="city-tile p-6 text-center text-[#71717a]">{{ __('There are no rare values yet.') }}</div>
        </template>

        <template x-if="tabs.length > 0">
            <div>
                <div role="tablist" class="-mb-px flex max-w-[calc(100%-8px)] items-start gap-1 overflow-x-auto">
                    <template x-for="(tab, index) in tabs" :key="tab.id">
                        <button type="button" role="tab" x-on:click="active = index" :aria-selected="active === index" x-text="tab.name"
                                :class="active === index ? 'bg-white pb-[13px]! text-black cursor-default dark:bg-city-ink-soft dark:text-white' : 'bg-[#f4f4f6] text-[#52525b] hover:text-black dark:bg-white/5 dark:text-gray-400'"
                                class="shrink-0 select-none rounded-t-lg border border-b-0 border-[#d4d4d8] px-4 py-3 text-lg leading-tight font-bold transition-all duration-150 dark:border-white/10"></button>
                    </template>
                </div>

                <div role="tabpanel" class="city-tile rounded-tl-none p-4">
                    <div class="grid grid-cols-3 gap-6 max-lg:grid-cols-2 max-md:grid-cols-1">
                        <template x-for="item in items" :key="item.id">
                            <div class="city-tile flex gap-4 p-4 max-[380px]:flex-col max-[380px]:items-center max-[380px]:text-center">
                                <div class="flex w-16 shrink-0 items-center justify-center rounded-lg bg-[#ececef] max-[380px]:w-full max-[380px]:p-4 dark:bg-city-ink">
                                    <img alt="" class="pointer-events-none select-none [image-rendering:pixelated]" :src="item.icon" loading="lazy"
                                         onerror="this.onerror=null;this.style.visibility='hidden';">
                                </div>

                                <div class="flex min-w-0 flex-1 flex-col max-[380px]:w-full">
                                    <strong class="mb-3 flex items-center gap-1.5 leading-tight">
                                        <span class="break-words" x-text="item.name"></span>
                                        <img x-show="item.ltd" class="h-4 w-4 shrink-0" src="{{ asset('assets/images/icons/ltd.png') }}" alt="LTD" title="Limited Edition">
                                    </strong>

                                    <div class="mt-auto flex flex-wrap items-center gap-1 max-[380px]:flex-col">
                                        <span class="flex select-none items-center justify-center gap-1 rounded-full bg-yellow-500/10 p-2" :title="@js(__('Credits'))">
                                            <img alt="" width="15" height="15" class="[image-rendering:pixelated]" src="{{ asset('assets/images/icons/currency/credits.png') }}">
                                            <span class="text-sm leading-none" x-text="format(item.credits)"></span>
                                        </span>

                                        <span x-show="item.currency > 0" class="flex select-none items-center justify-center gap-1 rounded-full p-2" :class="item.currencyBg" :title="item.currencyLabel">
                                            <img alt="" width="15" height="15" class="[image-rendering:pixelated]" :src="item.currencyIcon">
                                            <span class="text-sm leading-none" x-text="format(item.currency)"></span>
                                        </span>

                                        <template x-if="item.url">
                                            <a :href="item.url" class="ml-auto rounded-lg bg-city-blue px-3 py-1.5 text-sm font-bold text-white transition hover:bg-city-blue-dark max-[380px]:ml-0 max-[380px]:w-full">
                                                {{ __('Info') }}
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <p x-show="items.length === 0" style="display: none;" class="p-4 text-center font-medium text-red-600">
                        {{ __('No results found.') }}
                    </p>
                </div>
            </div>
        </template>
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
