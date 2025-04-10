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
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

class LocalizeUrlGenerator
{
    public Config $config;

    /**
     * @var UrlGenerator
     */
    public UrlGeneratorContract $urlGenerator;

    public function __construct(Config $config, UrlGeneratorContract $urlGenerator)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(string $name, array $parameters = [], bool $paginated = false): string
    {
        $locale = $this->config->get('app.locale');

        if (isset($parameters['page'])) {
            $page = (int) $parameters['page'];

            if ($page <= 1) {
                unset($parameters['page']);

                if (Str::endsWith($name, '.paginated')) {
                    $name = Str::replace('.paginated', '.index', $name);
                }
            } else {
                if (Str::endsWith($name, '.index')) {
                    $name = Str::replaceLast('.index', '.paginated', $name);
                }
            }

        }

        if (! $paginated) {
            $name = $locale.'.'.$name;
        }

        $this->urlGenerator->defaults(['locale' => $locale]);

        return $this->urlGenerator->route($name, $parameters);
    }
}
