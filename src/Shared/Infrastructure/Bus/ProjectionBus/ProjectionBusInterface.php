<?php

namespace Bejao\Shared\Infrastructure\Bus\ProjectionBus;


use Bejao\Shared\Application\Projections\ProjectionInterface;

interface ProjectionBusInterface
{
    /**
     * @param ProjectionInterface $projection
     * @return mixed
     */
    public function project(ProjectionInterface $projection);

}
