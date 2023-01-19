<?php

namespace Bejao\Shared\Domain\Entities;


use BackedEnum;
use Bejao\Shared\Domain\Enums\BaseEnum;
use Bejao\Shared\Domain\ValueObjects\IdentifierInterface;
use Bejao\Shared\Domain\ValueObjects\ValueObjectInterface;
use Carbon\Carbon as BaseCarbon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use RuntimeException;

final class BaseEntityDeserializer
{
    /** @var array<string,mixed> */
    private static array $reflectionCache = [];

    /**
     * @param array<string,mixed> $array
     * @param ReflectionClass<BaseEntity> $class
     * @return array<string,mixed>
     */
    public static function calculateAttributesFromArray(
        array           $array,
        ReflectionClass $class
    ): array
    {

        $params = [];
        foreach ($array as $attribute => $value) {
            $key = 'type_' . $class->getFileName() . $attribute;
            try {

                if (array_key_exists($key, self::$reflectionCache)) {
                    /** @var ReflectionNamedType|null $type */
                    $type = self::$reflectionCache[$key];
                } else {
                    /** @var ReflectionNamedType|null $type */
                    $type = $class->getProperty($attribute)->getType();
                    self::$reflectionCache[$key] = $type;
                }

                if (null === $type) {
                    continue;
                }

                if ($value === null) {
                    $params[$attribute] = null;
                } else {
                    $params[$attribute] = self::calculateReflectionValue($type, $value);
                }
            } catch (ReflectionException $e) {
                self::$reflectionCache[$key] = null;
            }
        }

        return $params;
    }

    /**
     * @param ReflectionClass $class
     * @param array<string,mixed> $array
     * @param string $dbEngine
     * @return array<string,mixed>
     * @throws ReflectionException
     */
    public static function calculateValueObjects(
        ReflectionClass $class,
        array           $array,
        string          $dbEngine = 'mysql'
    ): array
    {
        $valueObjects = [];

        foreach ($class->getProperties() as $property) {
            // if (false === isset($array[$property->getName()])) {
            //      continue;
            // }
            /** @var  ReflectionNamedType|null $type */
            $type = $property->getType();
            if (null === $type) {
                continue;
            }
            /** @var class-string $typeName */
            $typeName = $type->getName();

            if (!str_contains($typeName, '\\')) {
                continue;
            }
            if (isset(self::$reflectionCache['ReflectionClass' . $typeName])) {
                /** @var ReflectionClass $voClass */
                $voClass = self::$reflectionCache['ReflectionClass' . $typeName];
            } else {
                $voClass = new ReflectionClass($typeName);
                self::$reflectionCache['ReflectionClass' . $typeName] = $voClass;
            }

            $voName = $property->getName();

            if ($voClass->implementsInterface(ValueObjectInterface::class)) {
                /** @var ValueObjectInterface $valueObject */
                $valueObject = $voClass->newInstanceWithoutConstructor();
                foreach ($array as $key => $value) {
                    if (str_starts_with($key, $voName . '_')) {
                        self::updateNormalValueObject($voName, $key, $voClass, $value, $dbEngine, $valueObject);
                    }
                }
                $valueObjects[$voName] = $valueObject;
            }
        }
        return $valueObjects;
    }

    /**
     * @param ReflectionNamedType $type
     * @param mixed $value
     * @return BaseEnum|IdentifierInterface|array|mixed
     * @throws ReflectionException
     */
    private static function calculateReflectionValue(ReflectionNamedType $type, mixed $value): mixed
    {
        $newValue = $value;
        $typeName = $type->getName();
        if (is_string($value) && 'array' === $typeName) {
            $newValue = json_decode($value, true);
        }
        if (str_contains($typeName, '\\')) {
            /** @var class-string $objectClass */
            $objectClass = $typeName;

            if (isset(self::$reflectionCache['ReflectionClass' . $objectClass])) {
                /** @var ReflectionClass $typeClass */
                $typeClass = self::$reflectionCache['ReflectionClass' . $objectClass];
            } else {
                $typeClass = new ReflectionClass($objectClass);
                self::$reflectionCache['ReflectionClass' . $objectClass] = $typeClass;
            }

            $parentClass = $typeClass->getParentClass();
            if (is_string($value) && $parentClass && $parentClass->getName() === BaseEnum::class) {
                /** @var BaseEnum $typeName */
                $newValue = $typeName::fromValue($value);
            }

            if (is_string($value) && $typeClass->isEnum()) {
                /** @var BackedEnum $typeName */
                $newValue = $typeName::from($value);
            }


            $interfaces = $typeClass->getInterfaces();
            if ((is_string($value) || is_int($value)) && $value !== '' && isset($interfaces[IdentifierInterface::class])) {
                /** @var IdentifierInterface $identity */
                $identity = $typeName;
                $newValue = $identity::create($value);
            }
            if ($typeClass->getName() === BaseCarbon::class) {
                Log::error('Carbon Class used directly');
                throw new RuntimeException('Carbon Class used directly');
            }

            if (is_numeric($value) && $typeClass->getName() === Carbon::class) {

                $newValue = Carbon::createFromTimestamp($value);
            } elseif (is_string($value) && $typeClass->getName() === Carbon::class) {

                $newValue = new Carbon(date($value));
            }
            if ($typeClass->implementsInterface(ValueObjectInterface::class)) {
                if ($value === '' && $typeClass->implementsInterface(IdentifierInterface::class)) {
                    return null;
                }
                /** @var ValueObjectInterface $valueObject */
                $valueObject = $typeClass->newInstanceWithoutConstructor();
                self::updateSingleValueObject($valueObject, $typeClass, $value);
                $newValue = $valueObject;
            }

        }
        return $newValue;
    }

    /**
     * @param ValueObjectInterface $valueObject
     * @param ReflectionClass $voClass
     * @param mixed $value
     * @return void
     * @throws ReflectionException
     */
    protected static function updateSingleValueObject(ValueObjectInterface $valueObject, ReflectionClass $voClass, $value): void
    {
        /** @var ReflectionNamedType|null $type */
        $type = $voClass->getProperties()[0]->getType();
        if (null === $type) {
            return;
        }
        $properties = $voClass->getProperties();
        $voProperty = $properties[0];
        $valueObject->{$voProperty->getName()} = self::calculateReflectionValue($type, $value);
    }

    /**
     * @param string $voName
     * @param string $key
     * @param ReflectionClass $voClass
     * @param mixed $value
     * @param string $dbEngine
     * @param ValueObjectInterface $valueObject
     * @return void
     * @throws ReflectionException
     */
    protected static function updateNormalValueObject(string $voName, string $key, ReflectionClass $voClass, $value, string $dbEngine, ValueObjectInterface $valueObject): void
    {
        $valueProperty = str_replace($voName . '_', '', $key);
        /** @var ReflectionNamedType|null $type */
        $type = $voClass->getProperty($valueProperty)->getType();
        if (null === $type) {
            return;
        }
        $valueObject->{$valueProperty} = self::calculateReflectionValue($type, $value);
    }
}
