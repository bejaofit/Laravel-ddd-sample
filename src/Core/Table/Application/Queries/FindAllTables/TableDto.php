<?php

namespace Bejao\Core\Table\Application\Queries\FindAllTables;

final readonly class TableDto
{
    public function __construct(
        public string  $id,
        public string  $status,
        public int     $guests,
        public ?string $bookId

    )
    {
    }
}
