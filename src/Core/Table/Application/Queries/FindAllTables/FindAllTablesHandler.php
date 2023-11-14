<?php

namespace Bejao\Core\Table\Application\Queries\FindAllTables;

use Bejao\Core\Table\Domain\Entities\Table;
use Bejao\Core\Table\Domain\Repositories\TableRepositoryInterface;
use Bejao\Shared\Infrastructure\Bus\QueryBus\QueryHandlerInterface;

final readonly class FindAllTablesHandler implements QueryHandlerInterface
{
    public function __construct(
        private TableRepositoryInterface $repository
    )
    {
    }

    /**
     * @param FindAllTablesQuery $query
     * @return array<TableDto>
     */
    public function __invoke(FindAllTablesQuery $query): array
    {
        $tables = $this->repository->findAll();
        return array_map(static fn(Table $table) => new TableDto(
            $table->id->getValue(),
            $table->status->value,
            $table->guests,
            $table->bookId?->getValue()), $tables);
    }

}
