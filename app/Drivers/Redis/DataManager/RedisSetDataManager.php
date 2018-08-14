<?php

namespace UniMan\Drivers\Redis\DataManager;

use RedisProxy\RedisProxy;
use UniMan\Core\Utils\Filter;

class RedisSetDataManager
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

    public function items(string $table, int $onPage, array $filter = []): array
    {
        $items = [];
        $iterator = '';
        do {
            $pattern = null;
            $res = $this->connection->sscan($table, $iterator, $pattern, $onPage * 10);
            if (!$res) {
                return $items;
            }
            foreach ($res as $member) {
                $item = [
                    'member' => $member,
                    'length' => strlen($member),
                ];
                if (!Filter::apply($item, $filter)) {
                    continue;
                }
                $items[$member] = $item;
                if (count($items) === $onPage) {
                    break;
                }
            }
        } while ($iterator !== 0 && count($items) < $onPage);
        return $items;
    }
}
