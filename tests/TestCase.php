<?php

namespace Tests;

use Bejao\Shared\Infrastructure\Bus\CommandBus\CommandBusInterface;
use Bejao\Shared\Infrastructure\Bus\EventBus\EventBusInterface;
use Bejao\Shared\Infrastructure\Bus\QueryBus\QueryBusInterface;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public static bool $byPassDB = false;

    protected function refreshTestDatabase(): void
    {
        if (self::$byPassDB) {
            return;
        }

        if (!RefreshDatabaseState::$migrated) {
            Artisan::call('db:wipe');
            (new DatabaseSeeder)->run();
            $this->artisan('migrate');

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function commandBus(): CommandBusInterface
    {
        /** @var CommandBusInterface $commandBus */
        $commandBus = App::make(CommandBusInterface::class);
        return $commandBus;
    }

    protected function queryBus(): QueryBusInterface
    {
        /** @var QueryBusInterface $queryBus */
        $queryBus = App::make(QueryBusInterface::class);
        return $queryBus;
    }

    protected function eventBus(): EventBusInterface
    {
        /** @var EventBusInterface $eventBus */
        $eventBus = App::make(EventBusInterface::class);
        return $eventBus;
    }
}
