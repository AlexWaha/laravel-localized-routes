<?php

use Alexwaha\Localize\LanguageProvider;
use Alexwaha\Localize\Middleware\SetLocaleMiddleware;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

it('sets the locale based on URL segment using real Application', function () {
    $app = new Application(__DIR__);

    $configArray = [
        'app' => [
            'fallback_locale' => 'en',
            'locale' => 'en',
        ],
    ];

    $config = new Repository($configArray);

    $app->instance('config', $config);

    $app->instance('translator', new class
    {
        public function setLocale($locale) {}

        public function getLocale(): string
        {
            return 'en';
        }
    });

    $languages = [
        ['locale' => 'en', 'slug' => 'en'],
        ['locale' => 'es', 'slug' => 'es'],
    ];

    $languageProvider = new LanguageProvider($config, $languages);

    $middleware = new SetLocaleMiddleware($app, $languageProvider);

    $request = Request::create('/es/some-page', 'GET');

    $middleware->handle($request, function () {
        return new Response('HTTP_OK');
    });

    expect($app->getLocale())->toBe('es');
});
