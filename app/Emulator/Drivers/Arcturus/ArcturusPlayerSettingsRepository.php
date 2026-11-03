<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Emulator\Data\PlayerPreference;
use App\Emulator\Support\PreferenceColumns;
use App\Models\Game\Player\UserSetting;
use App\Models\Game\Player\UserSubscription;
use App\Models\User;

class ArcturusPlayerSettingsRepository implements PlayerSettingsRepository
{
    public function created(User $user): void
    {
        $giveHc = (setting('give_hc_on_register') ?: '0') === '1';

        UserSetting::query()->create([
            'user_id' => $user->id,
            'last_hc_payday' => $giveHc ? now()->addYears(10)->unix() : 0,
        ]);

        if ($giveHc) {
            UserSubscription::query()->insert([
                'user_id' => $user->id,
                'subscription_type' => 'HABBO_CLUB',
                'timestamp_start' => now()->unix(),
                'duration' => (int) (setting('hc_on_register_duration') ?: 0),
                'active' => 1,
            ]);
        }
    }

    public function canChangeName(User $user): bool
    {
        return (bool) UserSetting::query()
            ->where('user_id', $user->id)
            ->value('can_change_name');
    }

    public function setCanChangeName(User $user, bool $allowed): void
    {
        UserSetting::query()
            ->where('user_id', $user->id)
            ->update(['can_change_name' => $allowed ? '1' : '0']);
    }

    /** @return array<value-of<PlayerPreference>, bool> */
    public function preferences(User $user): array
    {
        return $this->preferenceColumns()->read($user->id);
    }

    /** @param  array<value-of<PlayerPreference>, bool>  $preferences */
    public function updatePreferences(User $user, array $preferences): void
    {
        $this->preferenceColumns()->write($user->id, $preferences, fn (bool $enabled): string => $enabled ? '1' : '0');
    }

    /** Arcturus stores its switches as '0'/'1' enums on users_settings. */
    private function preferenceColumns(): PreferenceColumns
    {
        return new PreferenceColumns([
            PlayerPreference::FriendRequests->value => ['users_settings', 'block_friendrequests', true],
            PlayerPreference::RoomInvites->value => ['users_settings', 'block_roominvites', true],
            PlayerPreference::Following->value => ['users_settings', 'block_following', true],
            PlayerPreference::CameraFollow->value => ['users_settings', 'block_camera_follow', true],
            PlayerPreference::OldChat->value => ['users_settings', 'old_chat', false],
        ], 'user_id');
    }
}
