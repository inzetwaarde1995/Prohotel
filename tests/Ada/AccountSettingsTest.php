<?php

use App\Emulator\Contracts\PlayerRepository;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Renaming is now enforced server-side against the emulator's single-use
 * grant. Ada has no schema for that grant, so its driver answers false and the
 * rename must be refused rather than reaching for an Arcturus-only table.
 *
 * The motto and email paths through the same action are covered in
 * tests/Ada/AdaSupportTest.php.
 */
beforeEach(function () {
    installHotel();
});

test('ada refuses a rename it never granted', function () {
    $user = User::factory()->create(['online' => false]);
    $original = $user->username;

    expect(app(PlayerSettingsRepository::class)->canChangeName($user))->toBeFalse();

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => $user->mail,
            'motto' => $user->motto,
            'username' => 'RenamedOnAda',
        ])
        ->assertSessionHasErrors('username');

    expect($user->fresh()->username)->toBe($original)
        ->and(DB::table('players')->where('id', $user->id)->value('username'))->toBe($original);
});

test('an online ada player cannot change settings without a bridge', function () {
    // No RCON means no way to tell a live session, so the player has to be
    // offline. The guard must not call the bridge to work that out.
    $user = User::factory()->create();

    DB::table('player_data')->where('player_id', $user->id)->update(['is_online' => true]);

    $this->actingAs($user->fresh())
        ->put(route('settings.account.update'), [
            'mail' => 'ada-online@example.com',
            'motto' => 'Should not stick',
        ])
        ->assertSessionHasErrors();

    expect($user->fresh()->mail)->not->toBe('ada-online@example.com');
});

test('ada stores preferences across its player tables', function () {
    $user = User::factory()->create();
    $settings = app(PlayerSettingsRepository::class);

    expect($settings->preferences($user))->toMatchArray(['friend_requests' => true, 'room_invites' => true, 'old_chat' => false])
        ->not->toHaveKey('following');

    $settings->updatePreferences($user, ['friend_requests' => false, 'room_invites' => false, 'following' => false]);

    expect((bool) DB::table('player_data')->where('player_id', $user->id)->value('allow_friend_requests'))->toBeFalse()
        ->and((bool) DB::table('player_game_settings')->where('player_id', $user->id)->value('block_room_invites'))->toBeTrue();
});

test('ada removes every friendship of a player', function () {
    [$user, $friend] = User::factory()->count(2)->create()->all();

    DB::table('player_friendships')->insert(['origin_player_id' => $friend->id, 'target_player_id' => $user->id, 'status' => 1, 'created_at' => now()]);

    expect(app(PlayerRepository::class)->removeAllFriends($user))->toBe(1);
});
