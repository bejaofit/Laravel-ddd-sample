<?php

namespace Bejao\Shared\Domain\ValueObjects;


use Bejao\Shared\Domain\Exceptions\InvalidIdentifierException;

abstract class IntegerIdentifier implements IdentifierInterface, \JsonSerializable
{
    private int $value;

    /**
     * @param int|string $value
     * @return static
     */
    public static function create($value): self
    {
        $intValue = (int)$value;
        if ($intValue === 0) {
            throw new InvalidIdentifierException(self::class, $intValue);
        }
        return new static($intValue);
    }


    /**
     * @param int|string|null $value
     * @return static
     */
    public static function createOrNull($value): ?self
    {
        if (0 === $value) {
            return null;
        }
        if (null === $value) {
            return null;
        }
        $intValue = (int)$value;
        if ($intValue === 0) {
            throw new InvalidIdentifierException(self::class, $intValue);
        }
        return new static($intValue);
    }

    final public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * For Compatibility with Copilot
     * @return int
     */
    public function value(): int
    {
        return $this->getValue();
    }

    public function equals(IdentifierInterface $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
