<?php

namespace Bejao\Shared\Infrastructure\Bus\CommandBus;

use Bejao\Shared\Application\Commands\CommandInterface;
use Bejao\Shared\Framework\Container;
use Closure;
use RuntimeException;
use Throwable;
use function get_class;

final class SimpleCommandBus implements CommandBusInterface
{
    /** @var array<string|Closure> */
    public static array $routes = [];

    /**
     * @param CommandInterface $command
     * @return void
     */
    final public function dispatch(CommandInterface $command): void
    {
        $commandClass = get_class($command);
        $commandHandlerName = self::$routes[$commandClass] ?? preg_replace('/Command$/', 'Handler', $commandClass);
        if (null === $commandHandlerName) {
            throw new RuntimeException('Handler not found for ' . $commandClass);
        }
        if ($commandHandlerName instanceof Closure) {
            $commandHandlerName($command);
        } else {
            /** @var CommandHandlerInterface $commandHandler */
            $commandHandler = Container::getObjectInstance($commandHandlerName);
            $commandHandler->__invoke($command);
        }

    }


    /**
     * @param CommandInterface $command
     * @param string $exceptionClass
     * @return void
     * @throws Throwable
     */
    public function dispatchIgnoreException(CommandInterface $command, string $exceptionClass): void
    {
        try {
            $this->dispatch($command);
        } catch (Throwable $e) {
            if ($e instanceof $exceptionClass) {
                return;
            }
            throw $e;
        }
    }
}
