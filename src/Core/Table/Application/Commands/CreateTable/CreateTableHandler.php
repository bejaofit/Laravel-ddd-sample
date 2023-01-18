<?php

namespace Bejao\Core\Table\Application\Commands\CreateTable;

use Bejao\Core\Table\Domain\Entities\Table;
use Bejao\Core\Table\Domain\Repositories\TableRepositoryInterface;
use Bejao\Shared\Infrastructure\Bus\CommandBus\CommandHandlerInterface;
use Bejao\Shared\Infrastructure\Bus\EventBus\EventBusInterface;
use Throwable;

final readonly class CreateTableHandler implements CommandHandlerInterface
{
    public function __construct(
        private TableRepositoryInterface $repository,
        private EventBusInterface        $eventBus
    )
    {
    }

    /**
     * @param CreateTableCommand $command
     * @return void
     * @throws Throwable
     */
    public function __invoke(CreateTableCommand $command): void
    {
        $table = Table::create($command->id);
        $this->repository->store($table);
        $this->eventBus->publishEvents($table->pullDomainEvents());
    }
}

{

}
