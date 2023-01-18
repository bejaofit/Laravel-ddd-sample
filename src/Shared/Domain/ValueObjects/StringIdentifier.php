<?php

namespace Bejao\Shared\Domain\ValueObjects;

abstract class StringIdentifier implements IdentifierInterface, \JsonSerializable
{
    private string $value;

    /**
     * @param int|string $value
     * @return static
     */
    public static function create($value): self
    {
        if ($value === '') {
            throw new \RuntimeException('Error Empty String Id');
        }
        return new static((string)$value);
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * @param int|string|null $value
     * @return static
     */
    public static function createOrNull(int|string|null $value): ?self
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return new static((string)$value);
    }

    final public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
