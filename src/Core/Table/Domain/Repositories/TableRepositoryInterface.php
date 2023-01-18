<?php

namespace Bejao\Core\Table\Domain\Repositories;

use Bejao\Core\Table\Domain\Entities\Table;
use Bejao\Core\Table\Domain\ValueObject\TableId;

interface TableRepositoryInterface
{
    public function store(Table $table): void;

    public function findById(TableId $id): ?Table;

    /**
     * @return array<Table>
     */
    public function findAll(): array;
}
