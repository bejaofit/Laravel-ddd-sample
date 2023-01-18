<?php

namespace Bejao\Shared\Infrastructure\Bus\EventBus;


use Bejao\Shared\Domain\Events\DomainEvent;
use Illuminate\Support\Facades\Auth;
use Throwable;

final class SimpleEventBus implements EventBusInterface
{
    /**
     * @param DomainEvent[] $events
     * @throws Throwable
     */
    public function publishEvents(array $events): void
    {
        foreach ($events as $event) {
            //TODO: Move to upsert

            $eventModel = EventModelAR::create($event);
            if ($eventModel->userId === null) {
                $userId =(int) Auth::id();
                $event->userId = $userId;
                $eventModel->userId = $event->userId;
            }
            $eventModel->saveOrFail();
            $event->eventId = $eventModel->id;
            event($event);
        }
    }

}
