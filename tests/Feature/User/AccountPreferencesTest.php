<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
    setSetting('theme', 'city');
});

test('the city account page shows the supported preferences', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('settings.account.show'))
        ->assertOk()
        ->assertSee('name="friend_requests"', false)
        ->assertDontSee('name="gifts"', false)
        ->assertSee(route('settings.friends.destroy'), false);
});

test('an offline player can update their preferences', function () {
    $user = User::factory()->create(['online' => false]);

    $this->actingAs($user)
        ->put(route('settings.preferences.update'), ['friend_requests' => '0', 'following' => '1'])
        ->assertRedirect(route('settings.account.show'))
        ->assertSessionHas('success');

    expect(DB::table('users_settings')->where('user_id', $user->id)->value('block_friendrequests'))->toBe('1');
});

test('an online player cannot change preferences or remove friends', function () {
    $user = User::factory()->create(['online' => true]);

    $this->actingAs($user)
        ->put(route('settings.preferences.update'), ['friend_requests' => '0'])
        ->assertSessionHasErrors('preferences', errorBag: 'preferences');

    $this->actingAs($user)
        ->delete(route('settings.friends.destroy'))
        ->assertSessionHasErrors('friends', errorBag: 'friends');

    expect(DB::table('users_settings')->where('user_id', $user->id)->value('block_friendrequests'))->toBe('0');
});

test('an offline player can remove all their friends', function () {
    [$user, $friend] = User::factory()->count(2)->create(['online' => false])->all();
    DB::table('messenger_friendships')->insert(['user_one_id' => $user->id, 'user_two_id' => $friend->id]);

    $this->actingAs($user)
        ->delete(route('settings.friends.destroy'))
        ->assertRedirect(route('settings.account.show'))
        ->assertSessionHas('success');

    expect(DB::table('messenger_friendships')->count())->toBe(0);
});

test('a password change from the city account page returns there', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('settings.password.update'), [
            'current_password' => 'password',
            'password' => 'ChangedPassword123!',
            'password_confirmation' => 'ChangedPassword123!',
            'redirect' => 'account',
        ])
        ->assertRedirect(route('settings.account.show'));
});
