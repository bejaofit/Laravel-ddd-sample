<?php

namespace Bejao\Shared\Framework;

final class ConfigHelper
{

    public static function getInt(string $key, ?int $default = null): ?int
    {
        $value = config($key, $default);
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value;
        }
        return null;
    }
}
