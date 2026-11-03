@props(['icon', 'routeGroup' => '', 'classes' => '', 'childClasses' => 'min-w-[150px]', 'uppercase' => false])

<div
    x-data="{
        open: false,
        toggle() {
            if (this.open) {
                return this.close()
            }

            this.$refs.button.focus()

            this.open = true
        },
        close(focusAfter) {
            if (! this.open) return

            this.open = false

            focusAfter && focusAfter.focus()
        }
    }"
        x-on:keydown.escape.prevent.stop="close($refs.button)"
        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
        x-id="['dropdown-button']"
        @class([
            'relative rounded-full px-3 py-1.5 text-[15px] font-medium text-city-ink hover:bg-black/5 transition duration-150 ease-in-out dark:text-gray-200 dark:hover:bg-white/10 z-5',
            $classes,
            'bg-black/5 dark:bg-white/10' => request()->is($routeGroup),
        ])"
>
    <!-- Button -->
    <button
        x-ref="button"
        x-on:click="toggle()"
        :aria-expanded="open"
        :aria-controls="$id('dropdown-button')"
        type="button"
        @class([
            'flex items-center gap-1 h-full',
            'uppercase' => $uppercase,
        ])"
    >
        {{ $slot }}

        <!-- Heroicon: chevron-down -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
             fill="currentColor">
            <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"/>
        </svg>
    </button>

    <!-- Panel -->
    <div
        x-ref="panel"
        x-show="open"
        x-transition.origin.top.left
        x-on:click.outside="close($refs.button)"
        :id="$id('dropdown-button')"
        style="display: none;"
         @class(['absolute left-0 mt-2 rounded-lg border border-city-line bg-white dark:border-white/10 dark:bg-city-ink-soft shadow-md whitespace-nowrap overflow-hidden z-10', $childClasses])
    >
        {{ $children }}
    </div>
</div>
