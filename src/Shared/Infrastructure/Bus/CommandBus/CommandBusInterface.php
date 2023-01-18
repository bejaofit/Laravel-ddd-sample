<?php

namespace Bejao\Shared\Infrastructure\Bus\CommandBus;


use Bejao\Shared\Application\Commands\CommandInterface;

interface CommandBusInterface
{
    /**
     * @param CommandInterface $command
     * @return void
     */
    public function dispatch(CommandInterface $command): void;

    /**
     * @param CommandInterface $command
     * @param string $exceptionClass
     * @return void
     */
    public function dispatchIgnoreException(CommandInterface $command, string $exceptionClass): void;

}
