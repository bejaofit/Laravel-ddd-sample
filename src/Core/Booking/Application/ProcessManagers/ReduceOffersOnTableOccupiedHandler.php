<?php

namespace Bejao\Core\Booking\Application\ProcessManagers;

use Bejao\Core\Table\Domain\Events\TableOccupiedEvent;

final class ReduceOffersOnTableOccupiedHandler
{

    public function handle(TableOccupiedEvent $event): void
    {

        //TODO: Reduce available tables
    }
}
