<?php

namespace Bejao\Shared\Domain\Enums;

/**
 * @method static static es()
 */
final class LanguageEnum extends BaseEnum
{
    public const ES = 'es';
    public const EN = 'en';

    public static function fromValue(string $value): self
    {
        [$value] = explode('_', $value);
        return parent::fromValue($value);
    }
}
