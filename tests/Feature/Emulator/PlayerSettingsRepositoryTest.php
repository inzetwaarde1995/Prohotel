<?php

use App\Emulator\Contracts\PlayerRepository;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
    setSetting('start_duckets', '0');
    setSetting('start_diamonds', '0');
    setSetting('start_points', '0');
    setSetting('give_hc_on_register', '0');
});

test('arcturus owns name change settings through its repository', function () {
    $user = User::factory()->create();
    $settings = app(PlayerSettingsRepository::class);

    expect(DB::table('users_settings')->where('user_id', $user->id)->exists())->toBeTrue()
        ->and($settings->canChangeName($user))->toBeFalse();

    $settings->setCanChangeName($user, true);

    expect($settings->canChangeName($user))->toBeTrue();
});

test('arcturus stores positively phrased preferences in its block columns', function () {
    $user = User::factory()->create();
    $settings = app(PlayerSettingsRepository::class);

    expect($settings->preferences($user))->toMatchArray(['friend_requests' => true, 'following' => true, 'old_chat' => false])
        ->not->toHaveKey('gifts');

    $settings->updatePreferences($user, ['friend_requests' => false, 'old_chat' => true, 'gifts' => false]);

    expect(DB::table('users_settings')->where('user_id', $user->id)->first())
        ->block_friendrequests->toBe('1')
        ->old_chat->toBe('1')
        ->and($settings->preferences($user))->toMatchArray(['friend_requests' => false, 'old_chat' => true]);
});

test('arcturus removes every friendship and friend request of a player', function () {
    [$user, $friend, $other] = User::factory()->count(3)->create()->all();

    DB::table('messenger_friendships')->insert([
        ['user_one_id' => $user->id, 'user_two_id' => $friend->id],
        ['user_one_id' => $friend->id, 'user_two_id' => $user->id],
        ['user_one_id' => $friend->id, 'user_two_id' => $other->id],
    ]);
    DB::table('messenger_friendrequests')->insert(['user_from_id' => $other->id, 'user_to_id' => $user->id]);

    expect(app(PlayerRepository::class)->removeAllFriends($user))->toBe(2)
        ->and(DB::table('messenger_friendships')->count())->toBe(1)
        ->and(DB::table('messenger_friendrequests')->count())->toBe(0);
});
