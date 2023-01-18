<?php

namespace Bejao\Core\Table\Domain\Entities;

use Bejao\Core\Booking\Domain\ValueObject\BookId;
use Bejao\Core\Table\Domain\Enums\TableStatusEnum;
use Bejao\Core\Table\Domain\Events\TableBookedEvent;
use Bejao\Core\Table\Domain\Events\TableCreatedEvent;
use Bejao\Core\Table\Domain\Events\TableFreeEvent;
use Bejao\Core\Table\Domain\Events\TableOccupiedEvent;
use Bejao\Core\Table\Domain\ValueObject\TableId;
use Bejao\Shared\Domain\Entities\BaseEntity;

final class Table extends BaseEntity
{
    public TableId $id;
    public ?BookId $bookId;
    public int $guests;
    public TableStatusEnum $status;

    public static function create(
        TableId $id,
    ): self
    {
        $table = new self();
        $table->id = $id;
        $table->bookId = null;
        $table->guests = 0;
        $table->status = TableStatusEnum::FREE;
        $table->record(TableCreatedEvent::create($table));
        return $table;
    }

    public function take(
        ?BookId $bookId,
        int     $guests
    ): void
    {
        $this->bookId = $bookId;
        $this->guests = $guests;
        $this->status = TableStatusEnum::OCCUPIED;
        $this->record(TableOccupiedEvent::create($this));
    }

    public function book(
        BookId $bookId,
        int    $guests
    ): void
    {
        $this->bookId = $bookId;
        $this->guests = $guests;
        $this->status = TableStatusEnum::RESERVED;
        $this->record(TableBookedEvent::create($this));

    }

    public function pay(): void
    {
        $this->bookId = null;
        $this->guests = 0;
        $this->status = TableStatusEnum::FREE;
        $this->record(TableFreeEvent::create($this));
    }
}
