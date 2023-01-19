<?php

namespace Bejao\Shared\Framework;

use RuntimeException;

final class MixedHelper
{
    /**
     * @param mixed $mixedString
     * @return string
     */
    public static function getString(mixed $mixedString): string
    {
        if (is_string($mixedString)) {
            return $mixedString;
        }
        if (is_numeric($mixedString)) {
            return (string)$mixedString;
        }
        $result = json_encode($mixedString);
        if (false === $result) {
            throw new RuntimeException('Can not return String');
        }
        return $result;
    }

    /**
     * @param mixed $value
     * @return float
     */
    public static function getFloat(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }
        if (is_string($value)) {
            return (float)$value;
        }
        throw new RuntimeException('Invalid Float Value ' . json_encode($value));
    }

    /**
     * @param mixed $mixed
     * @return array<string|int,mixed>
     */
    public static function getArray(mixed $mixed): array
    {
        if (is_array($mixed)) {

            return $mixed;
        }
        if (is_string($mixed)) {

            $array = json_decode($mixed, true);
            if (false === is_array($array)) {
                return [];
            }
            return $array;
        }
        if ($mixed === null) {
            return [];
        }
        throw new \RuntimeException('Invalid value for array ' . json_encode($mixed));
    }

    /**
     * @param mixed $value
     * @return int
     */
    public static function getInt(mixed $value): int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }
        if (is_string($value)) {
            return (int)$value;
        }
        throw new RuntimeException('Invalid Int Value ' . json_encode($value));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function getBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return (bool)$value;
        }
        if (is_numeric($value)) {
            return (bool)$value;

        }
        if (is_string($value)) {
            return $value === 'true' || $value === 'yes' || $value === '1';
        }
        throw new RuntimeException('Invalid Bool Value ' . json_encode($value));
    }

    /**
     * @param mixed $mixedString
     * @return string|null
     */
    public static function getStringOrNull(mixed $mixedString): ?string
    {
        if (is_null($mixedString)) {
            return null;
        }
        if (is_string($mixedString)) {
            return $mixedString;
        }
        if (is_numeric($mixedString)) {
            return (string)$mixedString;
        }
        $result = json_encode($mixedString);
        if (false === $result) {
            throw new RuntimeException('Can not return String');
        }
        return $result;
    }

    /**
     * @param mixed $mixed
     * @return string[]
     */
    public static function getStringArray(mixed $mixed): array
    {
        $array = self::getArray($mixed);
        return array_map(function ($item) {
            return self::getString($item);
        }, $array);
    }

    /**
     * @template T
     * @param mixed[] $array
     * @param T $class
     * @return T[]
     */
    public static function getIdentityArray(array $array, $class): array
    {
        return array_map(function ($item) use ($class) {
            return $class::create($item);
        }, $array);
    }
}
