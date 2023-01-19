<?php

namespace Bejao\Shared\Domain\Entities;


use Bejao\Shared\Domain\Enums\BaseEnum;
use Bejao\Shared\Domain\Events\DomainEvent;
use Bejao\Shared\Domain\ValueObjects\AutoIncrementIdentifier;
use Bejao\Shared\Domain\ValueObjects\IdentifierInterface;
use Bejao\Shared\Domain\ValueObjects\ValueObject;
use Bejao\Shared\Framework\ArrayHelper;
use Bejao\Shared\Framework\ObjectHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use function count;

/**
 * @property IdentifierInterface $id
 */
abstract class BaseEntity
{
    /** @var array<string,mixed> */
    private array $checkPoint;

    /**
     * @var DomainEvent[]
     */
    protected array $domainEvents = [];
    private bool $isNew;
    /** @var array<string,mixed> */
    private static array $reflectionCache = [];

    final protected function __construct()
    {
        $this->isNew = true;
    }

    /**
     * @return void
     */
    protected function makeCheckPoint(): void
    {
        $this->checkPoint = $this->serialize();
    }

    /**
     * TODO: Take in account ValueObjects
     * @return bool
     */
    public function hasChange(): bool
    {
        return (false === ArrayHelper::arrayEqual($this->checkPoint, $this->serialize()));
    }

    /**
     * @param Model $model
     * @param array<string,mixed> $array
     * @param array<string,mixed> $manualMappingArray
     * @return static
     */
    public static function autoHydrate(Model $model, array $array = [], array $manualMappingArray = []): self
    {
        //TODO: Is ignoring array
        $element = new static();
        $element->setIsNew(false);
        try {
            $array = $model->getAttributes();
            if (count($manualMappingArray)) {
                foreach ($manualMappingArray as $key => $item) {
                    unset($array[$key]);
                }
            }

            $class = new ReflectionClass(static::class);
            $params = BaseEntityDeserializer::calculateAttributesFromArray($array, $class);
            foreach ($params as $key => $value) {
                $element->{$key} = $value;
                unset($array[$key]);
            }
            $valueObjects = BaseEntityDeserializer::calculateValueObjects($class, $array);


            foreach ($valueObjects as $key => $valueObject) {
                $element->{$key} = $valueObject;
            }
            foreach ($manualMappingArray as $key => $item) {
                $element->{$key} = $item;
            }
        } catch (ReflectionException $e) {
            Log::error($e->getMessage());
            Log::error(print_r($array, true));
            echo $e->getMessage();
            throw new RuntimeException($e->getMessage());
        }
        return $element;
    }

    /**
     * @param array <string,mixed> $array
     * @return static
     */
    public static function hydrate(array $array = []): self
    {
        $element = new static();
        $element->setIsNew(false);
        foreach ($array as $key => $item) {
            $element->{$key} = $item;

        }

        return $element;
    }

    /**
     * @param ?string $initiatorId
     * @param int|null $userId
     * @return array<DomainEvent>
     */
    final public function pullDomainEvents(?string $initiatorId = null, ?int $userId = null): array
    {
        $domainEvents = [];

        foreach ($this->domainEvents as $domainEvent) {
            if ($initiatorId || $userId) {
                $domainEvent->userId ??= $userId;
                if (null === $domainEvent->initiator) {
                    $domainEvent->initiator = $initiatorId ? (string)$initiatorId : null;
                }
            }
            if (isset($this->id) && $this->id instanceof AutoIncrementIdentifier) {
                $id = (string)$this->id->getValue();
                $domainEvent->relatedId = $id;
                if ($domainEvent->stream) {
                    $domainEvent->stream = str_replace('-1', $id, $domainEvent->stream);
                }
            }
            $domainEvents[] = $domainEvent;
        }

        $this->resetDomainEvents();

        return $domainEvents;
    }

    final public function resetDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * @param bool $isNew
     */
    public function setIsNew(bool $isNew): void
    {
        $this->isNew = $isNew;
    }


    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    /**
     * @param array<string>|null $fieldList
     * @return array<string,mixed>
     */
    public function serialize(?array $fieldList = null): array
    {
        $attributesTransformed = [];
        $attributes = $this->calculateAttributeArray($this->getAttributesWithValues(), $fieldList);

        /**
         * @var string $key
         * @var mixed $value
         */
        foreach ($attributes as $key => $value) {
            $valueTransformed = $value;
            if (is_array($value)) {
                $valueTransformed = json_encode($value);
            }
            if ($value instanceof BaseEnum) {
                $valueTransformed = $value->getValue();
            }
            if ($value instanceof Carbon) {
                $valueTransformed = $value->timestamp;
            }
            if (is_object($value) && ObjectHelper::implements($value, IdentifierInterface::class)) {
                /** @var IdentifierInterface $value */
                $valueTransformed = $value->getValue();
            }
            if ($value instanceof ValueObject) {
                $attributes = $value->attributes();
                if (count($attributes) === 1) {
                    $voAttribute = $attributes[0];
                    $attributesTransformed[$key] = $value->{$voAttribute};
                } else {
                    foreach ($attributes as $voAttribute) {
                        $attributesTransformed[$key . '_' . $voAttribute] = $value->{$voAttribute};
                    }
                }
            } else {
                $attributesTransformed[$key] = $valueTransformed;
            }
        }
        return $attributesTransformed;
    }

    /**
     * @param array<string,mixed> $attributeList
     * @param array<string>|null $fieldList
     * @return array<string,mixed>
     */
    private function calculateAttributeArray(array $attributeList, ?array $fieldList): array
    {
        if (null === $fieldList) {
            return $attributeList;
        }
        $resArray = [];
        foreach ($fieldList as $field) {
            $resArray[$field] = $attributeList[$field];
        }
        return $resArray;
    }

    /**
     * @param array<string>|null $names
     * @param array<string> $except
     * @return array<string,mixed>
     */
    public function getAttributesWithValues(array $names = null, array $except = []): array
    {
        $values = [];
        if (null === $names) {
            $names = $this->getPublicAttributeArray();
        }
        foreach ($names as $name) {
            $values[$name] = $this->{$name};
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * @return array<string>
     */
    public function getPublicAttributeArray(): array
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
