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

    public function itemsCount(string $table, array $filter): int
    {
        if (!$filter) {
            return $this->connection->scard($table);
        }
        $iterator = '';
        $totalItems = 0;
        do {
            $res = $this->connection->sscan($table, $iterator, null, 1000);
            if (!$res) {
                return $totalItems;
            }
            foreach ($res as $member) {
                $item = [
                    'member' => $member,
                    'length' => strlen($member),
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
        $skipped = 0;
        $offset = ($page - 1) * $onPage;
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

                if ($skipped < $offset) {
                    $skipped++;
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
