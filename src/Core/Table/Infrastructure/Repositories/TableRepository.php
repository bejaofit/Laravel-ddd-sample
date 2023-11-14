<?php

namespace Bejao\Core\Table\Infrastructure\Repositories;

use Bejao\Core\Table\Domain\Entities\Table;
use Bejao\Core\Table\Domain\Repositories\TableRepositoryInterface;
use Bejao\Core\Table\Domain\ValueObject\TableId;
use Bejao\Shared\Infrastructure\Persistence\EloquentRepository;

final class TableRepository extends EloquentRepository implements TableRepositoryInterface
{

    public function store(Table $table): void
    {
        $this->upsert($table, TableAR::class, $table->serialize());
    }

    public function findById(TableId $id): ?Table
    {
        /** @var TableAR|null $table */
        $table = TableAR::query()->where(['id' => $id->value()])->first();

        if (is_null($table)) {
            return null;
        }

        return Table::autoHydrate($table);
    }

    /**
     * @return Table[]
     */
    public function findAll(): array
    {
        /** @var TableAR[] $tables */
        $tables = TableAR::query()->get()->toBase()->all();

        return array_map(static fn(TableAR $table) => Table::autoHydrate($table), $tables);
    }
}
