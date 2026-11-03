<?php

use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteRule;
use App\Models\Help\WebsiteRuleCategory;

test('seeded help center content is shown in dutch', function () {
    app()->setLocale('nl');

    $category = new WebsiteHelpCenterCategory(['name' => 'Room Ads', 'content' => 'English', 'button_text' => 'Submit a ticket']);

    expect($category->translated('name'))->toBe('Kamer advertenties')
        ->and($category->translated('button_text'))->toBe('Ticket indienen')
        ->and($category->translated('content'))->not->toContain(':hotel');
});

test('seeded rules are shown in dutch', function () {
    app()->setLocale('nl');

    $category = new WebsiteRuleCategory(['name' => 'General Rules']);
    $rule = new WebsiteRule(['rule' => 'Do not evade an IP Address ban. ']);

    expect($category->translated('name'))->toBe('Algemene regels')
        ->and($rule->translated('rule'))->toBe('Omzeil geen IP-adres ban.');
});

test('edited content and other locales keep the stored text', function () {
    app()->setLocale('nl');

    expect((new WebsiteRule(['rule' => 'A custom hotel rule.']))->translated('rule'))->toBe('A custom hotel rule.');

    app()->setLocale('en');

    expect((new WebsiteHelpCenterCategory(['name' => 'Room Ads']))->translated('name'))->toBe('Room Ads');
});

test('help items seeded with the hotel name are shown in dutch', function () {
    app()->setLocale('nl');
    config(['habbo.site.default_name' => 'Retro']);

    expect((new WebsiteHelpCenterCategory(['name' => 'Retro Way']))->translated('button_text'))->toEndWith(' Way')
        ->and((new WebsiteHelpCenterCategory(['name' => 'Ban Appeals']))->translated('name'))->toBe('Ban bezwaren');
});
