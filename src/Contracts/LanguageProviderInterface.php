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

namespace Alexwaha\Localize\Contracts;

interface LanguageProviderInterface
{
    /**
     * @return array<LanguageInterface>
     */
    public function getLanguages(): array;

    public function getLocaleBySegment(?string $segment = null): string;
}
