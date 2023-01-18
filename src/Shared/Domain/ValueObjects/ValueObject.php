<?php

namespace Bejao\Shared\Domain\ValueObjects;

use ReflectionClass;
use ReflectionProperty;

class ValueObject implements ValueObjectInterface
{
    /**
     * @return array<int,mixed>
     */
    public function attributes(): array
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }
}
