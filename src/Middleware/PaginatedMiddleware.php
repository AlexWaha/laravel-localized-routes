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

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handle an incoming request.
 */
class PaginatedMiddleware
{
    /**
     * @var UrlGenerator
     */
    protected UrlGeneratorContract $urlGenerator;

    protected Redirector $redirector;

    public function __construct(UrlGeneratorContract $urlGenerator, Redirector $redirector)
    {
        $this->urlGenerator = $urlGenerator;
        $this->redirector = $redirector;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $page = $request->route('page') ?? $request->query('page', 1);

        if (! is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $currentRouteName = $request->route()->getName();

        if ($page == 1 && str_ends_with($currentRouteName, 'paginated')) {
            $canonicalRouteName = substr($currentRouteName, 0, -strlen('paginated')).'index';

            $routeParams = $request->route()->parameters();
            unset($routeParams['page']);

            $queryParams = $request->query();
            unset($queryParams['page']);

            $url = $this->urlGenerator->route($canonicalRouteName, $routeParams);

            if (! empty($queryParams)) {
                $url .= '?'.http_build_query($queryParams);
            }

            return $this->redirector->to($url, Response::HTTP_MOVED_PERMANENTLY);
        }

        if (! $request->has('page') || ! is_numeric($request->query('page'))) {
            $request->merge(['page' => $page]);
        }

        return $next($request);
    }
}
