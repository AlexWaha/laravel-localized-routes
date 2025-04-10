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
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LocalizeServiceProvider extends ServiceProvider
{
    protected static array $localizedRoutes = [];

    protected static array $anyRoutes = [];

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/localize-routes.php', 'localize-routes'
        );

        $this->app->singleton(LocalizeUrlGenerator::class);
        $this->app->singleton(LanguageProviderInterface::class, function (Application $app) {
            $config = $app->get('config');
            $provider = $config->get('localize-routes.language_provider');

            return new $app->make($provider['class'], $provider['params']);
        });
    }

    public function boot(LanguageProviderInterface $languageProvider): void
    {
        $this->publishes([
            __DIR__.'/../config/localize-routes.php' => App::configPath('localize-routes.php'),
        ]);

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
            self::registerLocalizedRoutes($callback, $middleware);
        });

        Route::macro('anyRoutes', function (Closure $callback, array $middleware = []) {
            self::registerAnyRoutes($callback, $middleware);
        });

        $this->loadRoutesFrom(App::basePath('routes/web.php'));

        foreach ($languageProvider->getLanguages() as $language) {
            if ($language->isDefault()) {
                foreach (self::$anyRoutes as $route) {
                    Route::prefix($language->getPrefix())
                        ->middleware($route['middleware'])
                        ->group($route['callback']);
                }
            }

            foreach (self::$localizedRoutes as $route) {
                Route::prefix($language->getPrefix())
                    ->middleware($route['middleware'])
                    ->name($language->getCode().'.')
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

    public static function registerAnyRoutes(Closure $callback, array $middleware = []): void
    {
        self::$anyRoutes[] = [
            'middleware' => $middleware,
            'callback' => $callback,
        ];
    }
}
