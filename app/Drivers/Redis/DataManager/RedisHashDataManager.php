<?php

namespace UniMan\Drivers\Redis\DataManager;

use RedisProxy\RedisProxy;
use UniMan\Core\Utils\Filter;

class RedisHashDataManager
{
    private $connection;

    public function __construct(RedisProxy $connection)
    {
        $this->connection = $connection;
    }

    public function itemsCount(string $table, array $filter): int
    {
        if (!$filter) {
            return $this->connection->hlen($table);
        }

        $totalItems = 0;
        foreach ($filter as $filterParts) {
            if (isset($filterParts['key'][Filter::OPERATOR_EQUAL])) {
                $res = $this->connection->hget($table, $filterParts['key'][Filter::OPERATOR_EQUAL]);
                if ($res) {
                    $item = [
                        'key' => $filterParts['key'][Filter::OPERATOR_EQUAL],
                        'length' => strlen($res),
                        'value' => $res,
                    ];
                    if (Filter::apply($item, $filter)) {
                        $totalItems++;
                    }
                }
                return $totalItems;
            }
        }
        $iterator = '';
        do {
            $pattern = null;
            $res = $this->connection->hscan($table, $iterator, $pattern, 1000);
            $res = $res ?: [];
            foreach ($res as $key => $value) {
                $item = [
                    'key' => $key,
                    'length' => strlen($value),
                    'value' => $value,
                ];
                if (Filter::apply($item, $filter)) {
                    $totalItems++;
                }
            }
        } while ($iterator !== 0);
        return $totalItems;
    }

    public function items(string $table, int $page, int $onPage, array $filter = []): array
    {
        $items = [];
        foreach ($filter as $filterParts) {
            if (!isset($filterParts['key'][Filter::OPERATOR_EQUAL])) {
                continue;
            }
            $res = $this->connection->hget($table, $filterParts['key'][Filter::OPERATOR_EQUAL]);
            if (!$res) {
                return $items;
            }
            $item = [
                'key' => $filterParts['key'][Filter::OPERATOR_EQUAL],
                'length' => strlen($res),
                'value' => $res,
            ];
            if (Filter::apply($item, $filter)) {
                $items[$item['key']] = $item;
            }
            return $items;
        }

        $skipped = 0;
        $offset = ($page - 1) * $onPage;
        $iterator = '';
        do {
            $pattern = null;
            $res = $this->connection->hscan($table, $iterator, $pattern, $onPage * 10);
            if (!$res) {
                return $items;
            }
            foreach ($res as $key => $value) {
                $item = [
                    'key' => $key,
                    'length' => strlen($value),
                    'value' => $value,
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
        } while ($iterator !== 0 && count($items) < $onPage);
        return $items;
    }
}
