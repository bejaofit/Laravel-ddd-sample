<?php

namespace Bejao\Shared\Infrastructure\Bus\EventBus;

use Bejao\Shared\Domain\Events\DomainEvent;

/**
 * @method __invoke(DomainEvent $event)
 */
interface EventSubscriberInterface
{


}
