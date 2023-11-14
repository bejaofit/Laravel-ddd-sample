<?php

namespace Tests\Feature\Table;

use App\Models\User;
use Tests\TestCase;
use Tests\Unit\Core\Table\Application\Commands\CreateTable\CreateTableCommandMother;

final class TableIndexTest extends TestCase
{

    public function testIndexWorks(): void
    {
        //having a table
        $command = CreateTableCommandMother::create();
        $this->commandBus()->dispatch($command);
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        //when we call the index endpoint
        $response = $this->get('/api/tables');

        //then we should get a 200 response with the table
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($command->id->value(), $response->json('0.id'));

    }

}
