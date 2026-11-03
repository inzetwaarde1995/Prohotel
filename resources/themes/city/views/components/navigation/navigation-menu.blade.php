<div class="relative hidden w-full flex-col items-center gap-y-2 py-3! md:flex! md:w-auto md:flex-row! md:gap-x-2 md:gap-y-0 md:py-0!" id="mobile-menu">
    @auth
        <x-navigation.dropdown icon="home" route-group="user*">
            {{ auth()->user()->username }}

            <x-slot:children>
                <x-navigation.dropdown-child :route="route('me.show')">
                    {{ __('Home') }}
                </x-navigation.dropdown-child>

				<x-navigation.dropdown-child :route="route('draw-badge')">
                    {{ __('Badge Drawer') }}
                </x-navigation.dropdown-child>

                <x-navigation.dropdown-child :route="route('home.show', auth()->user()->username)">
                    {{ __('My Profile') }}
                </x-navigation.dropdown-child>

                <x-navigation.dropdown-child :route="route('settings.account.show')">
                    {{ __('User settings') }}
                </x-navigation.dropdown-child>

                @if (hasPermission('generate_logo'))
                    <x-navigation.dropdown-child :route="route('logo-generator.index')" :turbolink="false" target="_blank">
                        {{ __('Logo generator') }}
                    </x-navigation.dropdown-child>
                @endif

                @if (hasPermission('view_server_logs'))
                    <x-navigation.dropdown-child route="/log-viewer" :turbolink="false" target="_blank">
                        {{ __('Error logs') }}
                    </x-navigation.dropdown-child>
                @endif

                @if (hasPermission('housekeeping_access'))
                    <x-navigation.dropdown-child :route="setting('housekeeping_url')" :turbolink="false" target="_blank">
                        {{ __('Housekeeping') }}
                    </x-navigation.dropdown-child>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="border-t border-city-line dark:border-white/10">
                    @csrf
                    <button type="submit" class="dropdown-item w-full text-left text-red-600! dark:hover:bg-gray-700">
                        {{ __('Logout') }}
                    </button>
                </form>
            </x-slot:children>
        </x-navigation.dropdown>
    @else
        <a href="{{ route('welcome') }}"
           class="nav-item dark:text-gray-200 {{ request()->routeIs('welcome') ? 'bg-black/5 dark:bg-white/10' : '' }}">
            {{ __('Home') }}
        </a>
    @endauth

        <x-navigation.dropdown icon="community" route-group="community*">
            {{ __('Community') }}

            <x-slot:children>
                <x-navigation.dropdown-child :route="route('community.index')">
                    {{ __('Community') }}
                </x-navigation.dropdown-child>

                <x-navigation.dropdown-child :route="route('article.index')">
                    {{ __('News') }}
                </x-navigation.dropdown-child>

                <x-navigation.dropdown-child :route="route('tags.index')">
                    {{ __('Tags') }}
                </x-navigation.dropdown-child>

                <x-navigation.dropdown-child :route="route('values.index')">
                    {{ __('Rare values') }}
                </x-navigation.dropdown-child>

                <x-navigation.dropdown-child :route="route('vacancies.index')">
                    {{ __('Vacancies') }}
                </x-navigation.dropdown-child>
            </x-slot:children>
        </x-navigation.dropdown>

    @auth
        <a data-turbolinks="false" href="{{ route('shop.index') }}"
           class="nav-item dark:text-gray-200 {{ request()->routeIs('shop.*') ? 'bg-black/5 dark:bg-white/10' : '' }}">
            {{ __('Shop') }}
        </a>
    @endauth

    @auth
    <x-navigation.dropdown icon="rules" route-group="help-center*">
        {{ __('Help Tool') }}

        <x-slot:children>
            <x-navigation.dropdown-child :route="route('help-center.index')">
                {{ __('Help Tool') }}
            </x-navigation.dropdown-child>

            @if(hasPermission('manage_website_tickets'))
                <x-navigation.dropdown-child :route="route('help-center.ticket.index')">
                    {{ __('Open tickets') }}
                </x-navigation.dropdown-child>
            @endif


            <x-navigation.dropdown-child :route="route('help-center.rules.index')">
                {{ __('Rules') }}
            </x-navigation.dropdown-child>
        </x-slot:children>
    </x-navigation.dropdown>

    <a href="{{ setting('discord_invitation_link') }}" target="_blank" class="nav-item dark:text-gray-200">
        {{ __('Discord') }}
    </a>

    @else
        <a href="{{ route('help-center.rules.index') }}"
           class="nav-item dark:text-gray-200 {{ request()->routeIs('help-center.*') ? 'bg-black/5 dark:bg-white/10' : '' }}">
            {{ __('Help Tool') }}
        </a>
    @endauth

    <div class="w-full flex md:hidden gap-x-1 justify-center">
        <x-navigation.language-selector>
            <img src="/assets/images/icons/flags/{{ session()->has('locale') ? session()->get('locale') : config('habbo.site.default_language') }}.png"
                 alt="">
        </x-navigation.language-selector>
    </div>
</div>
