<?php

namespace Bejao\Shared\Infrastructure\Bus\QueryBus;

use Bejao\Shared\Framework\CacheHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use function get_class;

final class QueryBus implements QueryBusInterface
{
    /** @var array<string> */
    public array $routes = [];



    public function query(QueryInterface $query)
    {
        $key = get_class($query);
        $queryHandlerName = $this->routes[$key] ?? preg_replace('/Query$/', 'Handler', $key);
        if ($queryHandlerName === null) {
            throw new RuntimeException('Handler not found for query: ' . $key);
        }
        /** @var QueryHandlerInterface $queryHandler */
        $queryHandler = App::make($queryHandlerName);
        return $queryHandler->__invoke($query);
    }

    public function queryOrFail(QueryInterface $query)
    {
        $res = $this->query($query);
        if ($res === null || (is_array($res) && count($res) === 0)) {
            throw new ModelNotFoundException((string)json_encode($query));
        }
        return $res;
    }


    public function queryCached(QueryInterface $query, ?int $ttl = null)
    {
        $key = md5(get_class($query) . serialize($query));
        if ($ttl === null) {
            return CacheHelper::onceByKey($key, function () use ($query) {
                return $this->query($query);
            });
        }
        return Cache::remember($key, $ttl, function () use ($query) {
            return $this->query($query);
        });

    }


}
