<?php

use Alexwaha\Localize\LanguageProvider;
use Illuminate\Config\Repository as Config;

it('can instantiate LanguageProvider and return languages', function () {
    $config = new Config([
        'app' => [
            'fallback_locale' => 'en',
        ],
    ]);

    $languages = [
        ['locale' => 'en', 'slug' => 'en'],
        ['locale' => 'es', 'slug' => 'es'],
    ];

    $provider = new LanguageProvider($config, $languages);

    $langs = $provider->getLanguages();

    expect($langs)
        ->toHaveCount(2)
        ->and($langs[0]->getLocale())
        ->toBe('en')
        ->and($langs[1]->getSlug())
        ->toBe('es');
});
