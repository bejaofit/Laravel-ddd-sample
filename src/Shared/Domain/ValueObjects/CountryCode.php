<?php

namespace Bejao\Shared\Domain\ValueObjects;

use RuntimeException;

final class CountryCode extends StringIdentifier
{
    public static function create($value): self
    {
        if (strlen((string)$value) !== 2) {
            throw new RuntimeException('Invalid value for country ' . $value);
        }
        return parent::create(strtoupper((string)$value));
    }

}
