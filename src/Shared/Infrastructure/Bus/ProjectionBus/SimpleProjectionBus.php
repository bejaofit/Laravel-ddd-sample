<?php

namespace Bejao\Shared\Infrastructure\Bus\ProjectionBus;

use Bejao\Shared\Application\Projections\ProjectionInterface;
use Bejao\Shared\Framework\Container;
use RuntimeException;
use function get_class;

final class SimpleProjectionBus implements ProjectionBusInterface
{
    /** @var array <string> */
    public array $routes = [];

    /**
     * @param ProjectionInterface $projection
     * @return mixed|void
     */
    final public function project(ProjectionInterface $projection)
    {
        $projectionClass = get_class($projection);
        $projectionHandlerName = $this->routes[$projectionClass] ?? preg_replace('/Projection$/', 'Handler', $projectionClass);
        if (null === $projectionHandlerName) {
            throw new RuntimeException('Handler not found for ' . $projectionClass);
        }
        /** @var ProjectionHandlerInterface $projectionHandler */
        $projectionHandler = Container::getObjectInstance($projectionHandlerName);
        return $projectionHandler->__invoke($projection);
    }


}
