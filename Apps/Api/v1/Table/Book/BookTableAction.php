<?php

namespace Apps\Api\v1\Table\Book;

use Bejao\Core\Booking\Domain\ValueObject\BookId;
use Bejao\Core\Table\Application\Commands\BookTable\BookTableCommand;
use Bejao\Core\Table\Domain\ValueObject\TableId;
use Bejao\Shared\Infrastructure\Bus\CommandBus\CommandBusInterface;

final readonly class BookTableAction
{

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(BookId $bookId, TableId $tableId, int $guests): void
    {
        $this->commandBus->dispatch(new BookTableCommand($tableId, $bookId, $guests));
    }
}

