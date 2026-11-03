<?php

namespace App\Emulator\Drivers\Plus;

use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Emulator\Data\PlayerPreference;
use App\Emulator\Support\PreferenceColumns;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class PlusPlayerSettingsRepository implements PlayerSettingsRepository
{
    /**
     * users_settings.home_room references rooms, and INSERT IGNORE would
     * swallow that failure along with the row, so a configured home room that
     * no longer exists falls back to none.
     */
    public function created(User $user): void
    {
        $homeRoom = (int) $user->home_room;
        if ($homeRoom !== 0 && ! DB::table('rooms')->where('id', $homeRoom)->exists()) {
            $homeRoom = 0;
        }

        DB::table('users_settings')->insertOrIgnore(['user_id' => $user->id, 'home_room' => $homeRoom]);
        DB::table('user_statistics')->insertOrIgnore(['id' => $user->id]);
    }

    public function canChangeName(User $user): bool
    {
        return false;
    }

    public function setCanChangeName(User $user, bool $allowed): void {}

    /** @return array<value-of<PlayerPreference>, bool> */
    public function preferences(User $user): array
    {
        return $this->preferenceColumns()->read($user->id);
    }

    /** @param  array<value-of<PlayerPreference>, bool>  $preferences */
    public function updatePreferences(User $user, array $preferences): void
    {
        $this->preferenceColumns()->write($user->id, $preferences, fn (bool $enabled): int => (int) $enabled);
    }

    /** Plus keeps its switches as tinyint flags on users_settings. */
    private function preferenceColumns(): PreferenceColumns
    {
        return new PreferenceColumns([
            PlayerPreference::FriendRequests->value => ['users_settings', 'block_newfriends', true],
            PlayerPreference::RoomInvites->value => ['users_settings', 'ignore_invites', true],
            PlayerPreference::Gifts->value => ['users_settings', 'allow_gifts', false],
            PlayerPreference::Mimic->value => ['users_settings', 'allow_mimic', false],
            PlayerPreference::OldChat->value => ['users_settings', 'chat_preference', false],
        ], 'user_id');
    }
}
