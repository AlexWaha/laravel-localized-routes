<?php

use Alexwaha\Localize\LocalizeUrlGenerator;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;

it('can generate a localized URL', function () {
    $routes = new RouteCollection;

    $request = Request::create('/');
    $events = new Dispatcher;
    $router = new Router($events);
    $urlGenerator = new UrlGenerator($routes, $request);

    $config = new Repository([
        'app' => [
            'locale' => 'en',
        ],
    ]);

    $route = new Route(['GET'], '/en/home', ['as' => 'en.home']);
    $routes->add($route);

    $localizer = new LocalizeUrlGenerator($router, $config, $urlGenerator);

    $url = $localizer->generate('home');

    expect($url)->toBe('http://localhost/en/home');
});

it('can generate a localized paginated URL with route names using index/paginated', function () {
    $events = new Dispatcher;
    $router = new Router($events);

    $request = Request::create('/');

    $config = new Repository([
        'app' => [
            'locale' => 'en',
        ],
    ]);

    $routes = $router->getRoutes();

    $routes->add((new Route(['GET'], '/en/blog', ['as' => 'en.blog.index']))
        ->middleware(['localize.setLocale', 'localize.paginated']));

    $routes->add((new Route(['GET'], '/en/blog/page/{page?}', ['as' => 'en.blog.paginated']))
        ->middleware(['localize.setLocale', 'localize.paginated']));

    $urlGenerator = new UrlGenerator($routes, $request);

    $localizer = new LocalizeUrlGenerator($router, $config, $urlGenerator);

    $url = $localizer->generate('blog.index', ['page' => 2]);

    expect($url)->toBe('http://localhost/en/blog/page/2');
});

it('can generate a localized paginated URL without route names index/paginated', function () {
    $routes = new RouteCollection;

    $request = Request::create('/');
    $events = new Dispatcher;
    $router = new Router($events);
    $urlGenerator = new UrlGenerator($routes, $request);

    $config = new Repository([
        'app' => [
            'locale' => 'en',
        ],
    ]);

    $routes->add(new Route(['GET'], '/en/blog', ['as' => 'en.blog']));

    $localizer = new LocalizeUrlGenerator($router, $config, $urlGenerator);

    $url = $localizer->generate('blog', ['page' => 2]);

    expect($url)->toBe('http://localhost/en/blog?page=2');
});
