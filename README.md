# Laravel Multilang Routes

[![Latest Version](https://img.shields.io/packagist/v/alexwaha/laravel-multilang-routes.svg?style=flat-square)](https://packagist.org/packages/alexwaha/laravel-multilang-routes)
[![Laravel Versions](https://img.shields.io/badge/laravel-10%20%7C%2011%20%7C%2012-blue?style=flat-square)](https://laravel.com/)
[![PHP Version](https://img.shields.io/badge/php-8.2%2B-blue?style=flat-square)](https://www.php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alexwaha/laravel-multilang-routes.svg?style=flat-square)](https://packagist.org/packages/alexwaha/laravel-multilang-routes)
[![License](https://img.shields.io/packagist/l/alexwaha/laravel-multilang-routes.svg?style=flat-square)](LICENSE)

Simple and lightweight package to localize your Laravel routes.

## Features

- Localized versions of your routes without complex configurations
- Seamless support for route groups, slugs, and middleware
- Automatically switches URLs based on the active locale
- Redirects `/fallback_locale/...` to `/...` with 301 if needed
- Works with Laravel 10, 11, and 12

## Installation

Install the package via Composer:

```bash
composer require alexwaha/laravel-multilang-routes
```

Publish package config file:
```bash
php artisan vendor:publish --tag=multilang-localize-config
```

This will publish the config file to `config/multilang-routes.php`:

You can customize the list of supported languages and their URL slugs.

## Usage

Define your routes using the `localizedRoutes` macro.

```php
use Illuminate\Support\Facades\Route;

Route::localizedRoutes(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
```

Or routes with middlewares, for ex.: `web`
```php
use Illuminate\Support\Facades\Route;

Route::localizedRoutes(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
}, ['web']);
```
Or named routes with prefix
```php
Route::localizedRoutes(function () {
    Route::name('blog.')->prefix('/blog')->group(function () {
        Route::get('', [BlogController::class, 'index'])->name('index');
        Route::get('/{post}', [BlogController::class, 'show'])->name('show');
    });
}, ['web']);
```

This automatically generates localized routes like:

- `/en/about`
- `/es/about`
- `/de/about`

>The slug in the URL is required for the `setLocale` middleware, which changes the application language based on the `locale` slug.

### Blade usage

Generate a localized URL in Blade templates using `Route::localize()` method:

```php
<a href="{{ Route::localize('about') }}">About</a>
```

Or generate URLs with parameters:

```php
<a href="{{ Route::localize('blog.show', ['post' => $post]) }}">View Post</a>
```

### Middleware: `localize.setLocale`
This package provides a middleware that automatically sets the locale based on the URL.

Register the middleware in your routes:
```php
Route::middleware(['localize.setLocale'])->group(function () {
    Route::localizedRoutes(function () {
        Route::get('/', function () {
            return view('welcome');
        });
    });
});
```
Or

```php
Route::localizedRoutes(function () {
    Route::get('/', function () {
        return view('welcome');
    });
}, ['web', 'localize.setLocale']);
```
The middleware uses the first segment of the URL to detect the locale.

If the segment does not match any configured slug, the application will fall back to the default locale.

### Default Locale Redirection

If a user visits a URL with the default fallback locale (e.g., `/en/about` when `en` is the fallback), they will be automatically redirected with a **301 Permanent Redirect** to the non-sluged version (`/about`).

This ensures clean URLs for your default language.


### Configuration

You can configure which languages are available and their corresponding URL slugs inside the `config/multilang-routes.php` file.

Each language entry should contain:

| Key    | Description                             | Example                              |
|--------|-----------------------------------------|--------------------------------------|
| locale | Application locale `(app()->setLocale)` | en,es,de                             |
| slug   | URL slug for routes                     | en,es,de \| english, spanish, german |

#### Example:
```php
return [
    'language_provider' => [
        'class' => \Alexwaha\Localize\LanguageProvider::class,
        'params' => [
            'languages' => [
                [
                    'locale' => 'en',
                    'slug' => 'en',
                ],
                [
                    'locale' => 'es',
                    'slug' => 'es',
                ],
            ],
        ],
    ],
];
```
You can also customize the language provider by replacing `language_provider.class` in the config.

## Requirements

- PHP >= 8.2
- Laravel 10.x, 11.x, or 12.x

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

Developed by [Alex Waha](https://alexwaha.com)

---

Feel free to submit issues or contribute to this project!
