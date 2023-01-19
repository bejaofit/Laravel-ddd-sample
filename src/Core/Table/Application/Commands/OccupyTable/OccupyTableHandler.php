<?php

namespace Bejao\Core\Table\Application\Commands\OccupyTable;

use Bejao\Core\Table\Domain\Repositories\TableRepositoryInterface;
use Bejao\Shared\Infrastructure\Bus\CommandBus\CommandHandlerInterface;
use Bejao\Shared\Infrastructure\Bus\EventBus\EventBusInterface;
use DomainException;
use Throwable;

final readonly class OccupyTableHandler implements CommandHandlerInterface
{
    public function __construct(private TableRepositoryInterface $repository,
                                private EventBusInterface        $eventBus
    )
    {
    }

    /**
     * @param OccupyTableCommand $command
     * @return void
     * @throws Throwable
     */
    public function __invoke(OccupyTableCommand $command): void
    {
        $table = $this->repository->findById($command->id);
        if ($table === null) {
            throw new DomainException('Table not found');
        }
        $table->take($command->bookId, $command->guests);
        $this->repository->store($table);
        $this->eventBus->publishEvents($table->pullDomainEvents());
    }

}
