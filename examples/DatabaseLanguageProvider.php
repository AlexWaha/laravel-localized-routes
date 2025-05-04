<?php

/**
 * @author  Alexander Vakhovski (AlexWaha)
 *
 * @link    https://alexwaha.com
 *
 * @email   support@alexwaha.com
 *
 * @license GPLv3
 */

namespace App\Providers;

use Alexwaha\Localize\Contracts\LanguageInterface;
use Alexwaha\Localize\Contracts\LanguageProviderInterface;
use App\Models\Language;
use Schema;

class DatabaseLanguageProvider implements LanguageProviderInterface
{
    private ?array $defaultLanguages = null;

    /**
     * @return array<LanguageInterface>
     */
    public function getLanguages(): array
    {
        if (! $this->canAccessDatabase()) {
            return $this->getDefaultLanguages();
        }

        $languages = Language::active()->get();

        return array_map(function (Language $language) {
            return new \Alexwaha\Localize\Language(
                $language->locale,
                $language->slug,
                $language->is_default
            );
        }, $languages->all());
    }

    protected function getDefaultLanguages(): array
    {
        if ($this->defaultLanguages === null) {
            $this->defaultLanguages = array_map(
                fn ($lang) => new \Alexwaha\Localize\Language(
                    $lang['locale'],
                    $lang['slug'],
                    $lang['locale'] === config('app.fallback_locale')
                ),
                config('multilang-routes.language_provider.params.languages', [])
            );
        }

        return $this->defaultLanguages;
    }

    public function getLocaleBySegment(?string $segment = null): string
    {
        $fallbackLocale = config('app.fallback_locale');

        if (! $this->canAccessDatabase()) {
            return $fallbackLocale;
        }

        $language = Language::where('slug', $segment)->first();

        return $language?->locale ?? $fallbackLocale;
    }

    private function canAccessDatabase(): bool
    {
        return Schema::hasTable('languages');
    }
}