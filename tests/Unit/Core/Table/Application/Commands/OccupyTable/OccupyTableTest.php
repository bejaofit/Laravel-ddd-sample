<?php

namespace Tests\Unit\Core\Table\Application\Commands\OccupyTable;

use Bejao\Core\Table\Application\Commands\OccupyTable\OccupyTableCommand;
use Bejao\Core\Table\Domain\Events\TableOccupiedEvent;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Unit\Core\Table\Application\Commands\CreateTable\CreateTableCommandMother;

final class OccupyTableTest extends TestCase
{
    public function testOccupyWithoutBookingWorks(): void
    {
        //Having a table
        $command = CreateTableCommandMother::create();
        $this->commandBus()->dispatch($command);

        //when we occupy the table
        Event::fake();
        $this->commandBus()->dispatch(new OccupyTableCommand($command->id,null, 2));

        //then a table should be created
        $this->assertDatabaseHas('tables', [
            'id' => $command->id->value(),'status'=>'occupied'
        ]);
        Event::assertDispatched(TableOccupiedEvent::class);
    }

}
