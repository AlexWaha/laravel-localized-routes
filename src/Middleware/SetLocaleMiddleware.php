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

namespace Alexwaha\Localize\Middleware;

use Alexwaha\Localize\Contracts\LanguageProviderInterface;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    public Application $app;

    protected LanguageProviderInterface $languageProvider;

    public function __construct(Application $app, LanguageProviderInterface $languageProvider)
    {
        $this->languageProvider = $languageProvider;
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $localeSegment = $request->segment(1);
        $locale = $this->languageProvider->getLocaleBySegment($localeSegment);

        $this->app->setLocale($locale);

        return $next($request);
    }
}
