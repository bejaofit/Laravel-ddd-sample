<?php

namespace Bejao\Shared\Infrastructure\Bus\QueryBus;



interface QueryBusInterface
{
    /**
     * @template T
     * @param QueryInterface<T> $query
     * @return T
     * @throws QueryBusException
     */
    public function query(QueryInterface $query);

    /**
     * @template T
     * @param QueryInterface<T> $query
     * @param int|null $ttl
     * @return T
     * @throws QueryBusException
     */
    public function queryCached(QueryInterface $query, ?int $ttl = null);


    /**
     * @template T
     * @param QueryInterface<T> $query
     * @return T
     * @throws QueryBusException
     */
    public function queryOrFail(QueryInterface $query);


}
