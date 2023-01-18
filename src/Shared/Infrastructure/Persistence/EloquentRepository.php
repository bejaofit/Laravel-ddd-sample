<?php

namespace Bejao\Shared\Infrastructure\Persistence;

use Bejao\Shared\Domain\Entities\BaseEntity;
use Bejao\Shared\Domain\ValueObjects\AutoIncrementIdentifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class EloquentRepository
{
    /**
     * @param BaseEntity $baseEntity
     * @param string $ARClassName
     * @param array<string,mixed> $data
     * @return void
     */
    public function upsert(BaseEntity $baseEntity, string $ARClassName, array $data): void
    {
        /** @var Model $ARClass */
        $ARClass = $ARClassName;
        if ($baseEntity->isNew()) {
            /** @var Model $newValue */
            $newValue = $ARClass::query()->create($data);
            if (isset($baseEntity->id) && $baseEntity->id instanceof AutoIncrementIdentifier) {
                /** @var int $id */
                $id = $newValue->getKey();
                $baseEntity->id->setAutoIncrement($id);
            }
        } else {
            /** @var Model|null $ar */
            /** @phpstan-ignore-next-line */
            $ar = $ARClass::query()->find($baseEntity->id->getValue());
            if (null === $ar) {
                $ARClass::query()->create($data);
            } else {
                $ar->fill($data);
                $ar->save();
            }
        }
    }

}
