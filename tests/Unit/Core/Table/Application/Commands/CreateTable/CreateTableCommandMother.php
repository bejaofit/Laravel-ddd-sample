<?php

namespace Tests\Unit\Core\Table\Application\Commands\CreateTable;

use Bejao\Core\Table\Application\Commands\CreateTable\CreateTableCommand;
use Bejao\Core\Table\Domain\ValueObject\TableId;

final class CreateTableCommandMother
{
    public static function create(): CreateTableCommand
    {
        return new CreateTableCommand(
            TableId::random()
        );
    }

}
