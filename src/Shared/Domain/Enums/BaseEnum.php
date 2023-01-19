<?php

namespace Bejao\Shared\Domain\Enums;

use Bejao\Shared\Domain\Exceptions\InvalidEnumException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Throwable;

abstract class BaseEnum
{
    /** @var array <string,array<string,string>> */
    private static array $constCache = [];
    /** @var array <string,array<int,string>> */
    private static array $constValueCache = [];

    private string $value;

    final protected function __construct()
    {
    }

    /**
     * @param string $value
     * @return static
     * @throws InvalidEnumException
     */
    public static function fromValue(string $value): self
    {
        $enum = new static();
        try {
            if (self::isValidValue($value, false === is_numeric($value))) {
                $enum->value = $value;
            } else {
                throw new InvalidEnumException(static::class . ' - ' . self::class . ' -' . $value . '-');
            }
        } catch (ReflectionException $e) {
            Log::error($e->getMessage());
        }
        return $enum;
    }

    /**
     * @param string|null $value
     * @param array<string,string|int> $mapWrongValues
     * @return static|null
     */
    public static function fromValueMapWrongValuesOrNull(?string $value, array $mapWrongValues): ?self
    {
        if (null === $value) {
            return null;
        }

        $enum = new static();
        try {
            if (self::isValidValue($value, false === is_numeric($value))) {
                $enum->value = $value;
            } else {
                $enum->value = (string)$mapWrongValues[$value];
                if (false === self::isValidValue($enum->value, false === is_numeric($enum->value))) {
                    throw new InvalidEnumException(static::class . ' - ' . self::class . ' - ' . $enum->value);
                }
            }
        } catch (ReflectionException $e) {
            Log::error($e->getMessage());
        }
        return $enum;
    }

    /**
     * @param string|null $value
     * @return static|null
     */
    public static function fromValueOrNull(?string $value): ?self
    {
        if (null === $value) {
            return null;
        }
        return self::fromValue($value);
    }

    /**
     * @return static
     * @throws ReflectionException
     *
     */
    public static function random(): self
    {
        $values = self::obtainValues();
        return self::fromValue($values[array_rand($values)]);
    }

    /**
     * @param int|string $value
     * @param bool $strict
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidValue($value, bool $strict = true): bool
    {
        $values = array_values(self::obtainConstants());
        return in_array($value, $values, $strict);
    }

    /**
     * @return array<string,string>
     * @throws ReflectionException
     */
    public static function obtainConstants(): array
    {
        $calledClass = static::class;
        if (false === array_key_exists($calledClass, self::$constCache)) {
            /** @var array<string,string> $constants */
            $constants = (new ReflectionClass($calledClass))->getConstants();
            self::$constCache[$calledClass] = $constants;
        }
        return self::$constCache[$calledClass];
    }

    /**
     * @return array<int,string>
     * @throws ReflectionException
     */
    public static function obtainValues(): array
    {
        $calledClass = static::class;
        if (false === array_key_exists($calledClass, self::$constValueCache)) {
            /** @var array<int,string> $arrayValues */
            $arrayValues = array_values(self::obtainConstants());
            self::$constValueCache[$calledClass] = $arrayValues;
        }
        return self::$constValueCache[$calledClass];
    }

    /**
     * @param string $name
     * @param mixed $args
     * @return static
     * @throws InvalidEnumException
     */
    public static function __callStatic(string $name, $args): self
    {
        if (method_exists(static::class, $name)) {
            return self::$name($args);
        }

        try {
            $res = self::fromValue($name);
            return $res;
        } catch (Throwable $e) {

        }

        $constantName = strtoupper(Str::snake($name));
        return static::fromName($constantName);
    }

    /**
     * @param string $name
     * @return static
     * @throws InvalidEnumException
     */
    public static function fromName(string $name): self
    {
        $enum = new static();
        try {
            if (self::isValidName($name)) {
                $constants = self::obtainConstants();
                $enum->value = $constants[$name];
            } else {
                throw new InvalidEnumException(static::class . ' - ' . self::class . ' - ' . $name);
            }
        } catch (ReflectionException $e) {
            Log::error($e->getMessage());
        }
        return $enum;
    }

    /**
     * @param string $name
     * @param bool $strict
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidName(string $name, bool $strict = false): bool
    {
        $constants = self::obtainConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map(static function ($item) {

            return mb_strtolower($item);

        }, array_keys($constants));
        return in_array(strtolower($name), $keys, false);
    }

    /**
     * @param array<int,string> $values
     * @return bool
     */
    public function valueExists(array $values): bool
    {
        foreach ($values as $value) {
            if ($value === $this->getValue()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    public function equals(self $compareTo): bool
    {
        return $this->getValue() === $compareTo->getValue();
    }
}
