<?php

namespace Bejao\Shared\Framework;

use Bejao\Shared\Domain\Entities\BaseEntity;
use Bejao\Shared\Domain\Enums\BaseEnum;
use Bejao\Shared\Domain\ValueObjects\IdentifierInterface;
use Bejao\Shared\Domain\ValueObjects\IntegerIdentifier;
use Bejao\Shared\Domain\ValueObjects\ValueObject;

/**
 * TOOD: Fix Phpstan
 */
final class ArrayHelper
{
    /**
     * @param array $sourceArray
     * @param array $schema
     * @return array
     * @phpstan-ignore-next-line
     */
    public static function filterSchema(array $sourceArray, array $schema): array
    {
        $result = [];
        foreach ($sourceArray as $key => $sourceItem) {
            {
                if (array_key_first($schema) === '*') {
                    $result[$key] = self::filterSchema($sourceItem, $schema['*']);
                    continue;
                }
                if (is_array($sourceItem)) {
                    $value = self::filterSchema($sourceItem, $schema[$key]);
                    $result[$key] = $value;
                } else {
                    if (in_array($key, $schema)) {
                        $result[$key] = $sourceItem;
                    }
                }
            }
        }
        return $result;


    }

    /**
     * @param array $objectArray
     * @param array $schema
     * @return array
     * @phpstan-ignore-next-line
     */
    public static function filterObjectArrayBySchema(array $objectArray, array $schema): array
    {
        /** @phpstan-ignore-next-line */
        $array = json_decode(json_encode($objectArray), true);
        /** @phpstan-ignore-next-line */
        return self::filterSchema($array, $schema);
    }

    /** @phpstan-ignore-next-line */
    public static function filterObjectBySchema(object $objectArray, array $schema): array
    {
        /** @phpstan-ignore-next-line */
        $array = [json_decode(json_encode($objectArray), true)];
        return self::filterSchema($array, ['*' => $schema]);
    }

    /** @phpstan-ignore-next-line */
    public static function indexArray(array $items, string $attribute = 'id'): array
    {
        $res = [];
        foreach ($items as $item) {
            $res[$item->{$attribute}] = $item;
        }
        return $res;
    }

    /**
     * @param IntegerIdentifier[]|null $valueObjects
     * @return int[]|null
     */
    public static function identityArrayToInt(?array $valueObjects): ?array
    {
        if ($valueObjects === null) {
            return null;
        }
        return array_map(static function (IntegerIdentifier $item) {
            return (int)$item->getValue();
        }, $valueObjects);
    }

    /**
     * Merge first level arrays into a single array
     * @param array<int|string,array<mixed>> $items
     * @return array<mixed>
     */
    public static function mergeFirstLevel(array $items, bool $keepKey = false): array
    {
        $result = [];
        foreach ($items as $subArray) {
            foreach ($subArray as $key => $item) {
                if ($keepKey) {
                    $result[$key] = $item;
                } else {
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * @template T
     * @param int[] $array
     * @param T $class
     * @return T[]
     */
    public static function intToIdentityArray(array $array, $class): array
    {
        /** @var T[] $res */
        $res = array_map(static function (int $id) use ($class) {
            return new $class($id);
        }, $array);
        return $res;
    }

    /**
     * @template T
     * @param string[] $array
     * @param T $class
     * @return T[]
     */
    public static function stringToIdentityArray(array $array, $class): array
    {
        /** @var T[] $res */
        $res = array_map(static function (string $id) use ($class) {
            return new $class($id);
        }, $array);
        return $res;
    }

    /**
     * @param IdentifierInterface[]|null $valueObjects
     * @return string[]|null
     */
    public static function identityArrayToString(?array $valueObjects): ?array
    {
        if ($valueObjects === null) {
            return null;
        }
        return array_map(static function ($item) {
            return (string)$item->getValue();
        }, $valueObjects);
    }

    /**
     * @param BaseEnum[]|null $valueObjects
     * @return string[]|null
     */
    public static function enumArrayToString(?array $valueObjects): ?array
    {
        if ($valueObjects === null) {
            return null;
        }
        return array_map(static function (BaseEnum $item) {
            return $item->getValue();
        }, $valueObjects);
    }

    /**
     * @param ValueObject|null $valueObject
     * @return null|string|int|array<string,mixed>
     * @phpstan-ignore-next-line
     */
    public static function valueObjectToArray(?ValueObject $valueObject): ?array
    {
        if (null === $valueObject) {
            return null;
        }
        $result = [];
        $attributes = get_object_vars($valueObject);
        foreach ($attributes as $key => $attribute) {
            if ($attribute instanceof BaseEnum) {
                $attribute = $attribute->getValue();
            }
            if ($attribute instanceof IdentifierInterface) {
                $attribute = $attribute->getValue();
            }
            if ($attribute instanceof ValueObject) {
                $attribute = self::valueObjectToArray($attribute);
            }
            $result[$key] = $attribute;
        }
        if (count($result) === 1) {
            /** @phpstan-ignore-next-line */
            return current($result);
        }
        return $result;
    }

    /**
     * @param array<string,mixed> $array
     * @param string $attribute
     * @return float|null
     */
    public static function getFloatOrNull(array $array, string $attribute): ?float
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }
        return MixedHelper::getFloat($data);
    }

    /**
     * @param array<string,mixed> $array
     * @param string $attribute
     * @return int|null
     */
    public static function getIntOrNull(array $array, string $attribute): ?int
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }
        return MixedHelper::getInt($data);
    }

    /**
     * @param array<string,mixed> $array
     * @param string $attribute
     * @return string|null
     */
    public static function getStringOrNull(array $array, string $attribute): ?string
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }
        return MixedHelper::getString($data);
    }

    /**
     * @param array<string,mixed> $array
     * @param string $attribute
     * @return bool|null
     */
    public static function getBoolOrNull(array $array, string $attribute): ?bool
    {
        $data = $array[$attribute] ?? null;
        if ($data === null) {
            return null;
        }
        return MixedHelper::getBoolean($data);
    }

    /**
     * we may have a problem with identities, value not returned by json_decode
     * @param array<mixed> $array1
     * @param array<mixed> $array2
     * @return bool
     */
    public static function arrayEqual(array $array1, array $array2): bool
    {
        return json_encode($array1) === json_encode($array2);
    }

    /**
     * @template T
     * @param string[] $array
     * @param class-string<T> $class
     * @return T[]
     */
    public static function stringToEnum(array $array, string $class): array
    {
        /** @var T[] $res */
        $res = array_map(static function (string $value) use ($class) {
            /** @var BaseEnum $baseEnum */
            $baseEnum = $class;
            return $baseEnum::fromValue($value);
        }, $array);
        return $res;
    }

    /**
     * @template T
     * @param array<T> $array
     * @param bool $keepKeyAssoc
     * @return array<T>
     */
    public static function arrayUnique(array $array, bool $keepKeyAssoc = false): array
    {
        $duplicateKeys = [];
        $tmp = [];

        foreach ($array as $key => $val) {
            if (is_object($val)) {
                $val = (array)$val;
            }

            if (!in_array($val, $tmp)) {
                $tmp[] = $val;
            } else {
                $duplicateKeys[] = $key;
            }
        }

        foreach ($duplicateKeys as $key) {
            unset($array[$key]);
        }

        return $keepKeyAssoc ? $array : array_values($array);
    }

    /**
     * @param BaseEntity[] $entities
     * @return IdentifierInterface[]
     */
    public static function extractEntitiesIds(array $entities): array
    {
        /** @var IdentifierInterface[] $identities */
        $identities = collect($entities)->pluck('id')->all();
        return $identities;
    }

}
