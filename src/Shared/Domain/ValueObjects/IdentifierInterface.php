<?php

namespace Bejao\Shared\Domain\ValueObjects;

interface IdentifierInterface
{
    /**
     * @param string|int $value
     * @return IdentifierInterface
     */
    public static function create($value): self;

    /**
     * @return string|int
     */
    public function getValue();
}
