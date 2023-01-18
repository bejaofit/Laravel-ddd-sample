<?php

namespace App\Providers;

use Bejao\Core\Table\Domain\Repositories\TableRepositoryInterface;
use Bejao\Core\Table\Infrastructure\Repositories\TableRepository;
use Bejao\Shared\Infrastructure\Bus\CommandBus\CommandBusInterface;
use Bejao\Shared\Infrastructure\Bus\CommandBus\SimpleCommandBus;
use Bejao\Shared\Infrastructure\Bus\EventBus\EventBusInterface;
use Bejao\Shared\Infrastructure\Bus\EventBus\SimpleEventBus;
use Bejao\Shared\Infrastructure\Bus\QueryBus\QueryBus;
use Bejao\Shared\Infrastructure\Bus\QueryBus\QueryBusInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(EventBusInterface::class, SimpleEventBus::class);
        $this->app->bind(CommandBusInterface::class, SimpleCommandBus::class);
        $this->app->bind(QueryBusInterface::class, QueryBus::class);
        $this->app->bind(TableRepositoryInterface::class, TableRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
