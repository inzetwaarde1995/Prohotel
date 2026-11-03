@props(['errorBag' => '', 'classes' => '', 'name', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => true, 'autofocus' => false, 'readonly' => false])

<input
    class="{{ $classes }} w-full rounded-lg border-2 border-city-line bg-white focus:border-city-green focus:ring-0 dark:border-white/10 dark:bg-city-ink dark:text-gray-200 @error($name, $errorBag) border-red-500 ring-red-500 @enderror"
    id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
    autocomplete="{{ $name }}" placeholder="{{ $placeholder }}" @if ($readonly) required @endif
    @if ($autofocus) autofocus="{{ $name }}" @endif
    @if ($readonly) readonly @endif>

@error($name, $errorBag)
    <p class="mt-1 text-xs italic text-red-500">
        {{ $message }}
    </p>
@enderror
