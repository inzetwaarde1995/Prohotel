<?php

namespace App\Emulator\Drivers\Ada;

use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Emulator\Data\PlayerPreference;
use App\Emulator\Support\PreferenceColumns;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdaPlayerSettingsRepository implements PlayerSettingsRepository
{
    /**
     * Ada resolves club membership by subscription name, the same one
     * Arcturus writes into users_subscriptions.
     */
    private const CLUB = 'HABBO_CLUB';

    public function created(User $user): void
    {
        if ((setting('give_hc_on_register') ?: '0') !== '1') {
            return;
        }

        DB::table('player_subscriptions')->insert([
            'player_id' => $user->id,
            'subscription_id' => $this->clubSubscriptionId(),
            'created_at' => now(),
            'expires_at' => now()->addSeconds(max(0, (int) (setting('hc_on_register_duration') ?: 0))),
        ]);
    }

    /**
     * Ada has no per-player name-change grant, so housekeeping never offers
     * one; NameChangePermission is left off this driver's feature list.
     */
    public function canChangeName(User $user): bool
    {
        return false;
    }

    public function setCanChangeName(User $user, bool $allowed): void {}

    /**
     * Ada does not constrain subscription names, so resolve the lowest id to
     * keep picking the same row if duplicates ever appear.
     */
    private function clubSubscriptionId(): int
    {
        $id = DB::table('subscriptions')->where('name', self::CLUB)->orderBy('id')->value('id');

        return (int) ($id ?? DB::table('subscriptions')->insertGetId(['name' => self::CLUB]));
    }

    /** @return array<value-of<PlayerPreference>, bool> */
    public function preferences(User $user): array
    {
        return $this->preferenceColumns()->read($user->id);
    }

    /** @param  array<value-of<PlayerPreference>, bool>  $preferences */
    public function updatePreferences(User $user, array $preferences): void
    {
        $this->preferenceColumns()->write($user->id, $preferences, fn (bool $enabled): bool => $enabled);
    }

    /** Ada splits its switches over player_data and player_game_settings. */
    private function preferenceColumns(): PreferenceColumns
    {
        return new PreferenceColumns([
            PlayerPreference::FriendRequests->value => ['player_data', 'allow_friend_requests', false],
            PlayerPreference::RoomInvites->value => ['player_game_settings', 'block_room_invites', true],
            PlayerPreference::CameraFollow->value => ['player_game_settings', 'block_camera_follow', true],
            PlayerPreference::OldChat->value => ['player_game_settings', 'prefer_old_chat', false],
        ], 'player_id');
    }
}
