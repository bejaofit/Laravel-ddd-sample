<?php

namespace Bejao\Shared\Infrastructure\Persistence;

use Bejao\Shared\Domain\ValueObjects\IdentifierInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{

    public $incrementing = false;

    /**
     * @param IdentifierInterface $id
     * @return static
     */
    public static function findByIdOrFail(IdentifierInterface $id): self
    {
        /** @var static $result */
        $result = self::query()->findOrFail($id->getValue());
        return $result;
    }
}
