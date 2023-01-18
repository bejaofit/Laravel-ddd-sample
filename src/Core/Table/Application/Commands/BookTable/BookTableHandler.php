<?php

namespace Bejao\Core\Table\Application\Commands\BookTable;

use Bejao\Core\Table\Domain\Repositories\TableRepositoryInterface;
use Bejao\Shared\Infrastructure\Bus\CommandBus\CommandHandlerInterface;
use Bejao\Shared\Infrastructure\Bus\EventBus\EventBusInterface;
use DomainException;

final readonly class BookTableHandler implements CommandHandlerInterface
{
    public function __construct(private TableRepositoryInterface $repository,
                                private EventBusInterface        $eventBus
    )
    {
    }


    public function __invoke(BookTableCommand $command): void
    {
        $table = $this->repository->findById($command->id);
        if ($table === null) {
            throw new DomainException('Table not found');
        }
        $table->book($command->bookId, $command->guests);
        $this->repository->store($table);
        $this->eventBus->publishEvents($table->pullDomainEvents());
    }

}
