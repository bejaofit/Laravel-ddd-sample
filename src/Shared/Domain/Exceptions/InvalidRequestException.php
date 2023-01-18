<?php

namespace Bejao\Shared\Domain\Exceptions;

use DomainException;

final class InvalidRequestException extends DomainException
{
    /** @var int $code */
    protected $code = 422;

    public static function fromMessage(string $message): self
    {
        return new self($message);
    }
}
