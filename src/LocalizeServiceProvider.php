<?php

/**
 * @author  Alexander Vakhovski (AlexWaha)
 *
 * @link    https://alexwaha.com
 *
 * @email   support@alexwaha.com
 *
 * @license MIT
 */

namespace Alexwaha\Localize;

use Alexwaha\Localize\Contracts\LanguageProviderInterface;
use Alexwaha\Localize\Middleware\PaginatedMiddleware;
use Alexwaha\Localize\Middleware\SetLocaleMiddleware;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class LocalizeServiceProvider extends ServiceProvider
{
    protected static array $localizedRoutes = [];

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/multilang-routes.php', 'multilang-routes'
        );

        $this->app->singleton(LocalizeUrlGenerator::class);
        $this->app->singleton(LanguageProviderInterface::class, function (Application $app) {
            $config = $app->get('config');
            $provider = $config->get('multilang-routes.language_provider');

            return $app->make($provider['class'], $provider['params']);
        });
    }

    public function boot(Application $app, Router $router, Redirector $redirector, LanguageProviderInterface $languageProvider): void
    {
        $this->publishes([
            __DIR__.'/../config/multilang-routes.php' => $app->configPath('multilang-routes.php'),
        ], 'multilang-routes-config');

        $router
            ->aliasMiddleware('localize.setLocale', SetLocaleMiddleware::class)
            ->aliasMiddleware('localize.paginated', PaginatedMiddleware::class);

        $router->macro('localize', function (string $name, array $parameters = [], bool $paginated = false) use ($app) {
            return $app->make(LocalizeUrlGenerator::class)
                ->generate($name, $parameters, $paginated);
        });

        $router->macro('hasLocalize', function (string $name) use ($router, $app): bool {
            $localizedName = $app->getLocale().'.'.$name;

            return $router->has($localizedName) || $router->has($name);
        });

        $router->macro('localizedRoutes', function (Closure $callback, array $middleware = []) {
            LocalizeServiceProvider::registerLocalizedRoutes($callback, $middleware);
        });

        $this->loadRoutesFrom($app->basePath('routes/web.php'));

        $registrar = new RouteRegistrar($router);

        foreach ($languageProvider->getLanguages() as $language) {
            if ($language->isDefault()) {
                $registrar->prefix($language->getSlug())->group(function () use ($router, $redirector) {
                    $router->get('/', fn () => $redirector->to('/', Response::HTTP_MOVED_PERMANENTLY));
                    $router->get('{any}', fn ($any) => $redirector->to($any, Response::HTTP_MOVED_PERMANENTLY))
                        ->where('any', '.*');
                });
            }

            foreach (self::$localizedRoutes as $route) {
                $registrar->prefix($language->isDefault() ? '' : $language->getSlug())
                    ->middleware($route['middleware'])
                    ->name($language->getLocale().'.')
                    ->group($route['callback']);
            }
        }
    }

    public static function registerLocalizedRoutes(Closure $callback, array $middleware = []): void
    {
        self::$localizedRoutes[] = [
            'middleware' => $middleware,
            'callback' => $callback,
        ];
    }
}
