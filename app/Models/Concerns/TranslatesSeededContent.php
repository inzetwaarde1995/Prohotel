<?php

namespace App\Models\Concerns;

/**
 * Shows the translated version of seeded (default) content in the current locale.
 *
 * Translations live in lang/{locale}/help-center.php and are keyed by the
 * original seeded English text, so content that was edited in housekeeping
 * is returned unchanged.
 */
trait TranslatesSeededContent
{
    /**
     * The translation group (inside lang/{locale}/help-center.php) of this model.
     */
    abstract protected function seededTranslationGroup(): string;

    public function translated(string $attribute): string
    {
        $value = (string) $this->getAttribute($attribute);
        $translations = trans('help-center.' . $this->seededTranslationGroup());

        if (! is_array($translations)) {
            return $value;
        }

        $translation = $this->seededTranslation($translations, $attribute);

        if (! is_string($translation) || $translation === '') {
            return $value;
        }

        $hotel = (string) setting('hotel_name');

        return str_replace(':hotel', in_array($attribute, $this->seededHtmlAttributes(), true) ? e($hotel) : $hotel, $translation);
    }

    /**
     * Attributes that are rendered as HTML, so the hotel name is escaped in them.
     *
     * @return list<string>
     */
    protected function seededHtmlAttributes(): array
    {
        return ['content'];
    }

    /**
     * Find the translation of the attribute, keyed by the seeded record name.
     *
     * @param  array<string, mixed>  $translations
     */
    protected function seededTranslation(array $translations, string $attribute): mixed
    {
        $name = (string) $this->getAttribute('name');
        $entry = $translations[$name] ?? null;

        foreach ($this->seededHotelNames() as $hotel) {
            $entry ??= $translations[str_replace($hotel, ':hotel', $name)] ?? null;
        }

        return is_array($entry) ? ($entry[$attribute] ?? null) : null;
    }

    /**
     * Hotel names the seeders may have written into the stored content.
     *
     * @return list<string>
     */
    protected function seededHotelNames(): array
    {
        return array_values(array_unique(array_filter([
            (string) setting('hotel_name'),
            (string) config('habbo.site.default_name'),
        ])));
    }
}
