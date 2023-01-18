<?php

namespace Bejao\Shared\Domain\ValueObjects;

use Exception;

class AutoIncrementIdentifier extends IntegerIdentifier
{
    /** @var array<string,int> */
    public static array $lastAutoincrement = [];
    public string $random;

    /**
     * @return static
     * @throws Exception
     */
    public static function autoincrement(): self
    {
        /** @var string $random */
        $random = (string)(microtime(true) * 1000000 + random_int(1, 1000000));
        static::$lastAutoincrement[$random] = -1;
        $instance = new static(-1);
        $instance->random = $random;
        return $instance;
    }


    public function getValue(): int
    {
        if (parent::getValue() === -1) {
            return static::$lastAutoincrement[$this->random] ?? parent::getValue();
        }
        return parent::getValue();
    }

    public function setAutoIncrement(int $value): void
    {
        static::$lastAutoincrement[$this->random] = $value;
    }

    /**
     * @param int $value
     * @return static
     */
    public static function fromInt(int $value): self
    {
        return self::create($value);
    }
}
