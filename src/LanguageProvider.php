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

use Alexwaha\Localize\Contracts\LanguageInterface;
use Alexwaha\Localize\Contracts\LanguageProviderInterface;
use Illuminate\Contracts\Config\Repository;

class LanguageProvider implements LanguageProviderInterface
{
    public array $languages;

    public Repository $config;

    public function __construct(Repository $config, array $languages)
    {
        $this->languages = $languages;
        $this->config = $config;
    }

    /**
     * @return array<LanguageInterface>
     */
    public function getLanguages(): array
    {
        return array_map(function ($data) {
            return new Language(
                $data['locale'],
                $data['slug'],
                $this->config->get('app.fallback_locale') === $data['locale'],
            );
        }, $this->languages);
    }

    public function getLocaleBySegment(string $segment): string
    {
        foreach ($this->getLanguages() as $language) {
            if ($language->getSlug() === $segment) {
                return $language->getLocale();
            }
        }

        return $this->config->get('app.fallback_locale');
    }
}
