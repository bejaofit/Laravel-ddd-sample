<?php

namespace Bejao\Shared\Domain\ValueObjects;

use Exception;

/**
 * For using as integer ID were the creation will be slow, not suitable for batch or concurrency. Allows 9999 per second.
 */
abstract class SlowIntegerIdentifier extends IntegerIdentifier
{
    private static int $lastGenerated = 0;

    /**
     * @param int $value
     * @return static
     */
    public static function create($value): self
    {
        return new static($value);
    }

    /**
     * @return static
     * @throws Exception
     */
    public static function random(): self
    {
        $id = self::generateId();
        if ($id === self::$lastGenerated) {
            time_nanosleep(0, 100);
            $id = self::generateId();
        }
        self::$lastGenerated = $id;
        return new static($id);
    }

    /**
     * @return int
     */
    public static function generateId():int
    {
        return (int) ((microtime(true) - 1657558535) * 100000);
    }


}
