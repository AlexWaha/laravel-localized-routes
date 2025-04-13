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

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

class LocalizeUrlGenerator
{
    public Router $router;

    public Config $config;

    public string $locale;

    /**
     * @var UrlGenerator
     */
    public UrlGeneratorContract $urlGenerator;

    public function __construct(Router $router, Config $config, UrlGeneratorContract $urlGenerator)
    {
        $this->router = $router;
        $this->config = $config;
        $this->locale = $this->config->get('app.locale');
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(string $name, array $parameters = []): string
    {
        if (! $this->isLocalized($name)) {
            $name = $this->locale.'.'.$name;
        }

        if (isset($parameters['page'])) {
            $page = (int) $parameters['page'];

            if ($page <= 1) {
                unset($parameters['page']);

                if (Str::endsWith($name, '.paginated')) {
                    $name = Str::replaceLast('.paginated', '.index', $name);
                }
            }

            if ($page > 1 && Str::endsWith($name, '.index')) {
                $name = Str::replaceLast('.index', '.paginated', $name);
            }
        }

        $this->urlGenerator->defaults(['locale' => $this->locale]);

        return $this->urlGenerator->route($name, $parameters);
    }

    public function isLocalized(string $name): bool
    {
        return Str::startsWith($name, $this->locale.'.');
    }
}
