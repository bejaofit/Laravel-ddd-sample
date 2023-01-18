<?php

namespace Bejao\Shared\Framework;

final class CacheHelper
{
    /** @var array<string,mixed> */
    private static array $cache = [];


    /**
     * @param string $key
     * @param callable $callback
     * @return mixed
     */

    public static function onceByKey(string $key, callable $callback)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $value = $callback();
        self::$cache[$key] = $value;
        return $value;
    }

    public static function reset(): void
    {
        self::$cache = [];
    }

}
