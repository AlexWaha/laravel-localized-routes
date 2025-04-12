<?php

use Alexwaha\Localize\LocalizeUrlGenerator;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;

it('can generate a localized URL for non-paginated route', function () {
    $routes = new RouteCollection;

    $request = Request::create('/');
    $urlGenerator = new UrlGenerator($routes, $request);

    $config = new Repository([
        'app' => [
            'locale' => 'en',
        ],
    ]);

    $route = new Route(['GET'], '/en/home', ['as' => 'en.home']);
    $routes->add($route);

    $localizer = new LocalizeUrlGenerator($urlGenerator, $config);

    $url = $localizer->generate('home');

    expect($url)->toBe('http://localhost/en/home');
});

it('can generate a localized paginated URL when page > 1', function () {
    $routes = new RouteCollection;

    $request = Request::create('/');
    $urlGenerator = new UrlGenerator($routes, $request);

    $config = new Repository([
        'app' => [
            'locale' => 'en',
        ],
    ]);

    $routes->add(new Route(['GET'], '/en/blog', ['as' => 'en.blog.index']));
    $routes->add(new Route(['GET'], '/en/blog/page/{page}', ['as' => 'en.blog.paginated']));

    $localizer = new LocalizeUrlGenerator($urlGenerator, $config);

    $url = $localizer->generate('blog.index', ['page' => 2]);

    expect($url)->toBe('http://localhost/en/blog/page/2');
});
