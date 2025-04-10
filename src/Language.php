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
    private string $code;

    private string $prefix;

    private bool $isDefault;

    public function __construct(
        string $code, string $prefix, bool $isDefault
    ) {
        $this->code = $code;
        $this->prefix = $prefix;
        $this->isDefault = $isDefault;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
