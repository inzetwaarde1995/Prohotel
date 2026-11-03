<?php

use App\Models\Miscellaneous\WebsitePermission;
use App\Models\User;
use App\Services\PermissionsService;

test('City shows the housekeeping shortcut in the top bar only with permission', function (bool $housekeeping) {
    installHotel();
    setSetting('theme', 'city');
    setSetting('housekeeping_url', 'https://housekeeping.example.test/admin');

    WebsitePermission::query()->where('permission', 'housekeeping_access')->update(['min_rank' => $housekeeping ? 1 : 7]);
    app(PermissionsService::class)->refresh();

    $response = $this->actingAs(User::factory()->create(['rank' => 1]))->get(route('me.show'))->assertOk();

    $document = new DOMDocument;
    $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $shortcuts = (new DOMXPath($document))->query('//header//a[@href="https://housekeeping.example.test/admin" and @aria-label="' . __('Housekeeping') . '"]');

    expect($shortcuts)->toHaveCount($housekeeping ? 1 : 0);
})->with([
    'with permission' => [true],
    'without permission' => [false],
]);
