<?php

namespace Bejao\Shared\Domain\Exceptions;

use DomainException;

class InvalidIdentifierException extends DomainException
{

    /**
     * @param string $class
     * @param mixed $value
     */
    public function __construct(string $class, $value)
    {
        parent::__construct($class . ' ' . $value);
    }

}
