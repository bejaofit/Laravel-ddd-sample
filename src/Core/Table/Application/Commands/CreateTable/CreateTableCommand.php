<?php

namespace Bejao\Core\Table\Application\Commands\CreateTable;

use Bejao\Core\Table\Domain\ValueObject\TableId;
use Bejao\Shared\Application\Commands\CommandInterface;

final readonly class CreateTableCommand implements CommandInterface
{
    public function __construct(
        public TableId $id
    )
    {
    }

}
