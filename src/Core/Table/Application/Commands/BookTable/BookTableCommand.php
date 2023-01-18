<?php

namespace Bejao\Core\Table\Application\Commands\BookTable;

use Bejao\Core\Booking\Domain\ValueObject\BookId;
use Bejao\Core\Table\Domain\ValueObject\TableId;
use Bejao\Shared\Application\Commands\CommandInterface;

final readonly class BookTableCommand implements CommandInterface
{
    public function __construct(
        public TableId $id,
        public BookId  $bookId,
        public int     $guests
    )
    {

    }


}
