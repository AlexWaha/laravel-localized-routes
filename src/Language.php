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

class Language implements LanguageInterface
{
    private string $locale;

    private string $slug;

    private bool $isDefault;

    public function __construct(
        string $locale, string $slug, bool $isDefault
    ) {
        $this->locale = $locale;
        $this->slug = $slug;
        $this->isDefault = $isDefault;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
