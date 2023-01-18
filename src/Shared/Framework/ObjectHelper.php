<?php

namespace Bejao\Shared\Framework;

use ReflectionClass;
use ReflectionProperty;
use function array_key_exists;

final class ObjectHelper
{
    /**
     * @param object $source
     * @param object $target
     */
    public static function copyPublicProperties(object $source, object $target): void
    {
        $sourceReflection = new ReflectionClass($source);
        $targetReflection = new ReflectionClass($target);

        foreach ($sourceReflection->getProperties(ReflectionProperty::IS_PUBLIC) as $sourceProperty) {
            $propertyName = $sourceProperty->getName();
            if ($targetReflection->hasProperty($propertyName)) {
                $target->{$propertyName} = $source->{$propertyName};
            }
        }
    }

    /**
     * @param object $instance
     * @param string $className
     * @return bool
     */
    public static function implements(object $instance, string $className): bool
    {
        /** @var class-string[]|false $classes */
        $classes = class_implements($instance);
        if (false === $classes) {
            return false;
        }
        return array_key_exists($className, $classes);
    }
}
