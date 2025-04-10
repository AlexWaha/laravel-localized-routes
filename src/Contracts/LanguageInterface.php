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

interface LanguageInterface
{
    public function getCode(): string;

    public function getPrefix(): string;

    public function isDefault(): bool;
}
