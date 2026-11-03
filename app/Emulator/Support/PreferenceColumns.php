<?php

namespace App\Emulator\Support;

use App\Emulator\Data\PlayerPreference;
use Illuminate\Support\Facades\DB;

/**
 * Reads and writes PlayerPreference switches stored as flag columns.
 *
 * Each driver describes where a preference lives as
 * [table, column, inverted], where inverted marks "block ..." columns that
 * are true when the positively phrased preference is off.
 */
final class PreferenceColumns
{
    /**
     * @param  array<value-of<PlayerPreference>, array{0: string, 1: string, 2: bool}>  $map
     */
    public function __construct(
        private readonly array $map,
        private readonly string $playerColumn,
    ) {}

    /**
     * @return array<value-of<PlayerPreference>, bool>
     */
    public function read(int $playerId): array
    {
        $rows = [];
        $preferences = [];

        foreach ($this->map as $preference => [$table, $column, $inverted]) {
            $rows[$table] ??= (array) (DB::table($table)->where($this->playerColumn, $playerId)->first() ?? []);

            if (! array_key_exists($column, $rows[$table])) {
                continue;
            }

            $preferences[$preference] = ((bool) (int) $rows[$table][$column]) !== $inverted;
        }

        return $preferences;
    }

    /**
     * @param  array<value-of<PlayerPreference>, bool>  $preferences
     * @param  callable(bool): mixed  $toColumnValue
     */
    public function write(int $playerId, array $preferences, callable $toColumnValue): void
    {
        $updates = [];

        foreach ($this->map as $preference => [$table, $column, $inverted]) {
            if (! array_key_exists($preference, $preferences)) {
                continue;
            }

            $updates[$table][$column] = $toColumnValue($preferences[$preference] !== $inverted);
        }

        DB::transaction(function () use ($updates, $playerId): void {
            foreach ($updates as $table => $columns) {
                DB::table($table)->where($this->playerColumn, $playerId)->update($columns);
            }
        });
    }
}
