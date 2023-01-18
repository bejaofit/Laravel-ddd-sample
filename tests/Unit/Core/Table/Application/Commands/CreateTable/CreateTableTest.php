<?php

namespace Tests\Unit\Core\Table\Application\Commands\CreateTable;

use Tests\TestCase;

final class CreateTableTest extends TestCase
{
    public function testCreateTable(): void
    {
        //Having a command
        $command = CreateTableCommandMother::create();

        //when we handle the command
        $this->commandBus()->dispatch($command);

        //then a table should be created
        $this->assertDatabaseHas('tables', [
            'id' => $command->id->value(),
        ]);
    }

}
