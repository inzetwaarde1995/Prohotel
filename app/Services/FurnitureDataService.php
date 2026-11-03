<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class FurnitureDataService
{
    /**
     * Get the display name for a furniture classname (items_base.item_name).
     */
    public function name(string $classname): ?string
    {
        $names = $this->names();

        return $names[$classname] ?? $names[Str::before($classname, '*')] ?? null;
    }

    /**
     * All furniture names from FurnitureData.json, keyed by classname.
     *
     * @return array<string, string>
     */
    public function names(): array
    {
        $source = (string) setting('nitro_furnidata_file', '');

        if ($source === '') {
            return [];
        }

        $isUrl = Str::startsWith($source, ['http://', 'https://']);

        if (! $isUrl && ! is_readable($source)) {
            return [];
        }

        $version = $isUrl ? now()->format('YmdH') : (string) filemtime($source);
        $cacheKey = sprintf('furnidata_names:%s:%s', md5($source), $version);

        return Cache::remember($cacheKey, now()->addDay(), fn (): array => $this->parse($source, $isUrl));
    }

    /**
     * @return array<string, string>
     */
    private function parse(string $source, bool $isUrl): array
    {
        try {
            $contents = $isUrl
                ? Http::timeout(10)->get($source)->throw()->body()
                : file_get_contents($source);

            $data = json_decode((string) $contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return [];
        }

        $names = [];

        foreach (['roomitemtypes', 'wallitemtypes'] as $type) {
            foreach ($data[$type]['furnitype'] ?? [] as $furni) {
                if (! empty($furni['classname']) && ! empty($furni['name'])) {
                    $names[$furni['classname']] = $furni['name'];
                }
            }
        }

        return $names;
    }
}
