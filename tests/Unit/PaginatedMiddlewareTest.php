<?php

use Alexwaha\Localize\Middleware\PaginatedMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Response;

it('redirects to index route if page is 1 on paginated route', function () {
    $routes = new RouteCollection;

    $routes->add(new Route(['GET'], '/blog', ['as' => 'blog.index']));
    $routes->add($paginatedRoute = new Route(['GET'], '/blog/page/{page?}', ['as' => 'blog.paginated']));

    $request = Request::create('/blog/page/1', 'GET', ['page' => 1]);

    $paginatedRoute->bind($request);
    $request->setRouteResolver(fn () => $paginatedRoute);

    $urlGenerator = new UrlGenerator($routes, $request);
    $redirector = new Redirector($urlGenerator);

    $middleware = new PaginatedMiddleware($urlGenerator, $redirector);

    $response = $middleware->handle($request, function () {
        return new Response('HTTP_OK');
    });

    expect($response->isRedirect())
        ->toBeTrue()
        ->and($response->getTargetUrl())
        ->toBe('http://localhost/blog');
});

it('does not redirect when page is greater than 1', function () {

    $routes = new RouteCollection;

    $routes->add(new Route(['GET'], '/blog', ['as' => 'blog.index']));
    $routes->add($paginatedRoute = new Route(['GET'], '/blog/page/{page?}', ['as' => 'blog.paginated']));

    $request = Request::create('/blog/page/2', 'GET', ['page' => 2]);

    $urlGenerator = new UrlGenerator($routes, $request);
    $redirector = new Redirector($urlGenerator);

    $paginatedRoute->bind($request);
    $request->setRouteResolver(fn () => $paginatedRoute);

    $middleware = new PaginatedMiddleware($urlGenerator, $redirector);

    $response = $middleware->handle($request, function () {
        return new Response('HTTP_OK');
    });

    expect($response->isOk())->toBeTrue();
});
