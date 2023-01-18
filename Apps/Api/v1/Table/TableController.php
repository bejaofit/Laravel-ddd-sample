<?php

namespace Apps\Api\v1\Table;

use App\Http\Controllers\Controller;
use Apps\Api\v1\Table\Book\BookTableAction;
use Apps\Api\v1\Table\Book\BookTableRequest;
use Apps\Api\v1\Table\Index\TableIndexAction;
use Apps\Api\v1\Table\Index\TableIndexRequest;
use Bejao\Core\Table\Application\Queries\FindAllTables\TableDto;

final class TableController extends Controller
{
    /**
     * @param TableIndexRequest $request
     * @param TableIndexAction $action
     * @return array<TableDto>
     */
    public function index(TableIndexRequest $request, TableIndexAction $action): array
    {
        return $action();
    }


    public function book(BookTableRequest $request, BookTableAction $action): void
    {
        $action($request->getBookId(), $request->getTableId(), $request->getGuests());
    }
}

