<?php

namespace Apps\Api\v1\Table\Book;

use Apps\Shared\AbstractBejaoFormRequest;
use Bejao\Core\Booking\Domain\ValueObject\BookId;
use Bejao\Core\Table\Domain\ValueObject\TableId;

final class BookTableRequest extends AbstractBejaoFormRequest
{
    public function getBookId(): BookId
    {
        return BookId::create($this->getHelper()->getString('bookId'));
    }

    public function getTableId(): TableId
    {
        return TableId::create($this->getHelper()->routeString('tableId'));
    }

    public function getGuests(): int
    {
        return $this->getHelper()->getInt('guests');
    }
}
