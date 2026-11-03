@php
    $actionCards = $categories->where('small_box', false)->values();
    $faqItems = $categories->where('small_box', true)->values();
@endphp

<x-app-layout>
    @push('title', __('Help Tool'))

    <div class="col-span-12 flex flex-col gap-14">
        {{-- Help Tool --}}
        <section>
            <x-section-title icon="info">{{ __('Help Tool') }}</x-section-title>

            @if ($actionCards->isEmpty())
                <div class="city-tile rounded-xl p-1">
                    <h2 class="mb-1 flex w-full rounded-lg bg-city-blue px-4 py-2 font-bold text-white shadow-[0_2px_0_0_rgba(0,0,0,0.05)]">
                        {{ __('Contact :hotel', ['hotel' => setting('hotel_name')]) }}
                    </h2>
                    <div class="m-4">
                        <p class="mb-6 text-[#52525b] dark:text-gray-400">{{ __('Do you have a problem or need help? Get in touch with us!') }}</p>
                        <a href="{{ route('help-center.ticket.create') }}" class="inline-flex rounded-lg bg-city-blue px-5 py-3 font-bold text-white transition hover:bg-city-blue-dark">
                            {{ __('Create a ticket') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ($actionCards as $category)
                        <div class="city-tile flex flex-col rounded-xl p-1" wire:key="help-card-{{ $category->id }}">
                            <h2 class="mb-1 flex w-full rounded-lg bg-city-blue px-4 py-2 font-bold text-white shadow-[0_2px_0_0_rgba(0,0,0,0.05)]">
                                {{ $category->translated('name') }}
                            </h2>

                            <div class="m-4 flex flex-1 flex-col">
                                <div class="prose prose-sm mb-6 max-w-none flex-1 text-[#3f3f46] dark:prose-invert dark:text-gray-300">
                                    @if ($category->image_url)
                                        <img alt="" class="float-right ml-3 mt-0! [image-rendering:pixelated]"
                                             src="{{ asset('assets/images/help-center/' . $category->image_url) }}">
                                    @endif

                                    {!! $category->translated('content') !!}
                                </div>

                                @if ($category->button_text)
                                    <a data-turbolinks="false" href="{{ $category->button_url ?: route('help-center.ticket.create') }}"
                                       class="inline-flex w-fit rounded-lg bg-city-blue px-5 py-3 font-bold text-white transition hover:bg-city-blue-dark">
                                        {{ $category->translated('button_text') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- My tickets --}}
        <section>
            <header class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <x-section-title icon="news" classes="mb-0!">{{ __('My tickets') }}</x-section-title>

                <a href="{{ route('help-center.ticket.create') }}" class="rounded-lg bg-city-blue px-4 py-2 font-bold text-white transition hover:bg-city-blue-dark">
                    {{ __('Create a ticket') }}
                </a>
            </header>

            <div class="city-tile divide-y divide-[#e4e4e7] overflow-hidden dark:divide-white/10">
                @forelse ($myTickets as $ticket)
                    <a href="{{ route('help-center.ticket.show', $ticket) }}" wire:key="my-ticket-{{ $ticket->id }}"
                       class="flex items-center justify-between gap-4 px-4 py-3 transition hover:bg-[#f4f4f6] dark:hover:bg-white/5">
                        <span class="min-w-0">
                            <span class="block truncate font-bold">{{ $ticket->title }}</span>
                            <span class="text-sm text-[#71717a]">#{{ $ticket->id }} · {{ \Illuminate\Support\Carbon::parse($ticket->created_at)->translatedFormat('j M Y') }}</span>
                        </span>

                        <span @class([
                            'city-chip shrink-0',
                            'bg-green-500/15! text-green-700! dark:text-green-400!' => $ticket->open,
                        ])>{{ $ticket->open ? __('Open') : __('Closed') }}</span>
                    </a>
                @empty
                    <p class="p-4 text-[#52525b] dark:text-gray-400">{{ __('You have not created any tickets yet.') }}</p>
                @endforelse
            </div>

            @if (hasPermission('manage_website_tickets'))
                <a href="{{ route('help-center.ticket.index') }}" class="mt-3 inline-block font-medium text-city-blue hover:underline">
                    {{ __('View all open tickets') }}
                </a>
            @endif
        </section>

        {{-- FAQ --}}
        @if ($faqItems->isNotEmpty() || $rules->isNotEmpty())
            <section x-data="{ open: null }">
                <x-section-title icon="info">{{ __('FAQ') }}</x-section-title>

                <div class="flex flex-col gap-2">
                    @foreach ($faqItems as $category)
                        <div class="city-tile p-4" wire:key="faq-{{ $category->id }}">
                            <button type="button" class="flex w-full items-center justify-between gap-3 text-left"
                                    x-on:click="open = open === {{ $category->id }} ? null : {{ $category->id }}"
                                    :aria-expanded="open === {{ $category->id }}">
                                <h3 class="text-lg font-bold">{{ $category->translated('name') }}</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-city-blue transition-transform duration-200"
                                     :class="open === {{ $category->id }} && 'rotate-45'" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>

                            <div x-show="open === {{ $category->id }}" x-collapse style="display: none;">
                                <div class="prose prose-sm mt-4 max-w-none text-[#3f3f46] dark:prose-invert dark:text-gray-300">
                                    @if ($category->image_url)
                                        <img alt="" class="float-right ml-3 mt-0! [image-rendering:pixelated]"
                                             src="{{ asset('assets/images/help-center/' . $category->image_url) }}">
                                    @endif

                                    {!! $category->translated('content') !!}
                                </div>

                                @if ($category->button_text && $category->button_url)
                                    <a data-turbolinks="false" href="{{ $category->button_url }}"
                                       class="mt-4 inline-flex rounded-lg bg-city-blue px-4 py-2 font-bold text-white transition hover:bg-city-blue-dark">
                                        {{ $category->translated('button_text') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if ($rules->isNotEmpty())
                        <div class="city-tile p-4">
                            <button type="button" class="flex w-full items-center justify-between gap-3 text-left"
                                    x-on:click="open = open === 'rules' ? null : 'rules'" :aria-expanded="open === 'rules'">
                                <h3 class="text-lg font-bold">{{ __('The :hotel rules', ['hotel' => setting('hotel_name')]) }}</h3>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-city-blue transition-transform duration-200"
                                     :class="open === 'rules' && 'rotate-45'" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>

                            <div x-show="open === 'rules'" x-collapse style="display: none;">
                                <div class="mt-4 flex flex-col gap-4 text-sm text-[#3f3f46] dark:text-gray-300">
                                    @foreach ($rules as $ruleCategory)
                                        <div wire:key="rule-cat-{{ $ruleCategory->id }}">
                                            <p class="mb-1 font-bold">{{ $ruleCategory->translated('name') }}</p>
                                            <ul class="flex flex-col gap-1">
                                                @foreach ($ruleCategory->rules as $rule)
                                                    <li><strong>{{ $rule->paragraph }}.</strong> {{ $rule->translated('rule') }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>

                                <a href="{{ route('help-center.rules.index') }}" class="mt-4 inline-block font-medium text-city-blue hover:underline">
                                    {{ __('View all rules') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </div>

    <div class="col-span-12">
        <x-newest-furniture />
    </div>
</x-app-layout>
