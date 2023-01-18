<?php

namespace Bejao\Shared\Application\Queries;

final class PaginatedCollection
{
    public ?int $pageSize = null;
    public ?int $page = null;
    public ?int $totalCount = null;
    /** @var array<int,mixed> */
    public array $items = [];

    public function setPagination(int $offset, int $limit): void
    {
        $this->pageSize = $limit;
        $this->page = (int)($offset / $limit) + 1;
    }
}
