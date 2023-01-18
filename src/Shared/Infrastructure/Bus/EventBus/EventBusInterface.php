<?php

namespace Bejao\Shared\Infrastructure\Bus\EventBus;

use Bejao\Shared\Domain\Events\DomainEvent;
use Throwable;

interface EventBusInterface
{
    /**
     * @param DomainEvent[] $events
     * @throws Throwable
     */
    public function publishEvents(array $events): void;
}
