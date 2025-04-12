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
use Alexwaha\Localize\Middleware\SetLocale;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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

    public function boot(LanguageProviderInterface $languageProvider, Redirector $redirector): void
    {
        $this->publishes([
            __DIR__.'/../config/multilang-routes.php' => App::configPath('multilang-routes.php'),
        ], 'multilang-routes-config');

        App::get('router')->aliasMiddleware('localize.setLocale', SetLocale::class);

        Route::macro('localize', function (string $name, array $parameters = [], bool $paginated = false) {
            return App::make(LocalizeUrlGenerator::class)
                ->generate($name, $parameters, $paginated);
        });

        Route::macro('hasLocalize', function (string $name): bool {
            $locale = App::getLocale();
            $localizedName = $locale.'.'.$name;

            return Route::has($localizedName) || Route::has($name);
        });

        Route::macro('localizedRoutes', function (Closure $callback, array $middleware = []) {
            LocalizeServiceProvider::registerLocalizedRoutes($callback, $middleware);
        });

        $this->loadRoutesFrom(App::basePath('routes/web.php'));

        foreach ($languageProvider->getLanguages() as $language) {
            if ($language->isDefault()) {
                Route::prefix($language->getSlug())->group(function () use ($redirector) {
                    Route::get('/', fn () => $redirector->to('/', '301'));
                    Route::get('{any}', fn ($any) => $redirector->to($any, '301'))
                        ->where('any', '.*');
                });
            }

            foreach (self::$localizedRoutes as $route) {
                Route::prefix($language->isDefault() ? '' : $language->getSlug())
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
