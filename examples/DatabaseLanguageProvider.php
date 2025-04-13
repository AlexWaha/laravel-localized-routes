<?php
/**
 * @author  Alexander Vakhovski (AlexWaha)
 * @link    https://alexwaha.com
 * @email   support@alexwaha.com
 * @license MIT
 */

namespace App\Providers;

use Alexwaha\Localize\Contracts\LanguageProviderInterface;
use Alexwaha\Localize\Contracts\LanguageInterface;
use App\Models\Language;

class DatabaseLanguageProvider implements LanguageProviderInterface
{
    /**
     * @return array<LanguageInterface>
     */
    public function getLanguages(): array
    {
        $languages = Language::all();

        return array_map(function (Language $language) {
            return new \Alexwaha\Localize\Language(
                $language->locale,
                $language->slug,
                $language->is_default
            );
        }, $languages->all());
    }

    /**
     * @param  string|null  $segment
     * @return string
     */
    public function getLocaleBySegment(?string $segment = null): string
    {
        $language = Language::where('slug', $segment)->first();

        return $language?->locale ?? config('app.fallback_locale');
    }
}
