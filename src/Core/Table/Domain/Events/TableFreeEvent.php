<?php

namespace Bejao\Core\Table\Domain\Events;

use Bejao\Core\Table\Domain\Entities\Table;
use Bejao\Shared\Domain\Events\DomainEvent;

final class TableFreeEvent extends DomainEvent
{
    public static function create(Table $table): self
    {
        $instance = new self($table->id->value());
        $instance->data = $table->serialize();
        return $instance;
    }

}
