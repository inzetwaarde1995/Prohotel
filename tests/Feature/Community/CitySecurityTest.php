<?php

use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
    setSetting('theme', 'city');
});

test('popular rooms hide invisible rooms', function () {
    $user = User::factory()->create();

    foreach (['open' => 'Public room', 'invisible' => 'Secret room'] as $state => $name) {
        DB::table('rooms')->insert(['owner_id' => $user->id, 'owner_name' => $user->username, 'name' => $name, 'description' => '', 'state' => $state, 'users' => 5]);
    }

    expect(Room::query()->popular()->pluck('name')->all())->toBe(['Public room']);
});

test('vacancy descriptions are escaped', function () {
    WebsiteOpenPosition::create([
        'position_kind' => 'rank',
        'permission_id' => DB::table('permissions')->min('id'),
        'description' => '<script>alert(1)</script>',
        'apply_from' => now()->subDay(),
        'apply_to' => now()->addDay(),
    ]);

    $this->get(route('vacancies.index'))
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});

test('array search input does not break pages', function () {
    $this->get(route('article.index', ['search' => ['x']]))->assertOk();
    $this->get(route('tags.index', ['tag' => ['x']]))->assertOk();

    $this->actingAs(User::factory()->create())
        ->post(route('tags.store'), ['tag' => ['x']])
        ->assertSessionHasErrors('tag', errorBag: 'tags');
});

test('the hotel name is escaped in translated html content', function () {
    app()->setLocale('nl');
    setSetting('hotel_name', '<b>Hotel</b>');

    expect((new WebsiteHelpCenterCategory(['name' => 'Room Ads']))->translated('content'))
        ->toContain('&lt;b&gt;Hotel&lt;/b&gt;')
        ->not->toContain('<b>Hotel</b>');
});
