<?php

namespace UniMan\Drivers\Redis\DataManager;

use RedisProxy\RedisProxy;
use UniMan\Core\Utils\Filter;

class RedisKeyDataManager
{
    private $connection;

    public function __construct(RedisProxy $connection)
    {
        $this->connection = $connection;
    }

    public function itemsCount(array $filter): int
    {
        $totalItems = 0;
        foreach ($this->connection->keys('*') as $key) {
            if ($this->connection->type($key) !== RedisProxy::TYPE_STRING) {
                continue;
            }
            $result = $this->connection->get($key);
            $item = [
                'key' => $key,
                'value' => $result,
                'length' => strlen($result),
            ];

            if (Filter::apply($item, $filter)) {
                $totalItems++;
            }
        }
        return $totalItems;
    }

    public function items(int $page, int $onPage, array $filter = []): array
    {
        $skipped = 0;
        $offset = ($page - 1) * $onPage;
        $items = [];
        foreach ($this->connection->keys('*') as $key) {
            if ($this->connection->type($key) !== RedisProxy::TYPE_STRING) {
                continue;
            }
            $result = $this->connection->get($key);

            $item = [
                'key' => $key,
                'value' => $result,
                'length' => strlen($result),
            ];

            if (!Filter::apply($item, $filter)) {
                continue;
            }

            if ($skipped < $offset) {
                $skipped++;
                continue;
            }
            
            $items[$key] = $item;
            if (count($items) === $onPage) {
                break;
            }
        }
        return $items;
    }
}
