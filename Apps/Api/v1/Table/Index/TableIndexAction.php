<?php

namespace Apps\Api\v1\Table\Index;

use Bejao\Core\Table\Application\Queries\FindAllTables\FindAllTablesQuery;
use Bejao\Core\Table\Application\Queries\FindAllTables\TableDto;
use Bejao\Shared\Infrastructure\Bus\QueryBus\QueryBusInterface;

final readonly class TableIndexAction
{

    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    /**
     * @return array<TableDto>
     */
    public function __invoke(): array
    {
        return $this->queryBus->query(new FindAllTablesQuery());
    }

}
