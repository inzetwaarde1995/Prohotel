<?php

namespace App\Actions\User;

use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Emulator\Data\PlayerPreference;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdatePlayerPreferences
{
    public function __construct(private readonly PlayerSettingsRepository $settings) {}

    /**
     * The emulator keeps these preferences in memory while the player is
     * online and writes them back on logout, so they can only change offline.
     *
     * @param  array<value-of<PlayerPreference>, bool>  $preferences
     *
     * @throws ValidationException
     */
    public function execute(User $user, array $preferences): void
    {
        if ($user->online) {
            throw ValidationException::withMessages([
                'preferences' => __('You must be offline to change your preferences'),
            ])->errorBag('preferences');
        }

        $supported = $this->settings->preferences($user);

        $this->settings->updatePreferences($user, array_intersect_key($preferences, $supported));
    }
}
