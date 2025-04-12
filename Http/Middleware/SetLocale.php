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

namespace Alexwaha\Localize\Http\Middleware;

use Alexwaha\Localize\Contracts\LanguageProviderInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected LanguageProviderInterface $languageProvider;

    public function __construct(LanguageProviderInterface $languageProvider)
    {
        $this->languageProvider = $languageProvider;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $localeSegment = $request->segment(1);
        $locale = $this->languageProvider->getLocaleBySegment($localeSegment);

        App::setLocale($locale);

        return $next($request);
    }
}
