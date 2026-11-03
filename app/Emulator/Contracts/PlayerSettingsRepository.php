<?php

namespace App\Emulator\Contracts;

use App\Emulator\Data\PlayerPreference;
use App\Models\User;

interface PlayerSettingsRepository
{
    public function created(User $user): void;

    public function canChangeName(User $user): bool;

    public function setCanChangeName(User $user, bool $allowed): void;

    /**
     * The preferences this emulator stores for the player, keyed by PlayerPreference value.
     *
     * @return array<value-of<PlayerPreference>, bool>
     */
    public function preferences(User $user): array;

    /**
     * Store the given preferences; preferences the emulator does not support are ignored.
     *
     * @param  array<value-of<PlayerPreference>, bool>  $preferences
     */
    public function updatePreferences(User $user, array $preferences): void;
}
