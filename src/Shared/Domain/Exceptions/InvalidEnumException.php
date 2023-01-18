<?php

namespace Bejao\Shared\Domain\Exceptions;

use DomainException;

class InvalidEnumException extends DomainException
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }

}
