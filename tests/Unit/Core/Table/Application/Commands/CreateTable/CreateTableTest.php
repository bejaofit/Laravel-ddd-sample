<?php

namespace Tests\Unit\Core\Table\Application\Commands\CreateTable;

use Bejao\Core\Table\Domain\Events\TableCreatedEvent;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class CreateTableTest extends TestCase
{
    public function testCreateTable(): void
    {
        //Having a command
        $command = CreateTableCommandMother::create();

        //when we handle the command
        Event::fake();
        $this->commandBus()->dispatch($command);

        //then a table should be created
        $this->assertDatabaseHas('tables', [
            'id' => $command->id->value(),
        ]);
        Event::assertDispatched(TableCreatedEvent::class);
    }

}
