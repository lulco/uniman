<?php

namespace UniMan\Drivers\Redis\DataManager;

use RedisProxy\RedisProxy;
use UniMan\Core\Utils\Filter;

class RedisListDataManager
{
    private $connection;

    public function __construct(RedisProxy $connection)
    {
        $this->connection = $connection;
    }

    public function itemsCount(string $key, array $filter): int
    {
        if (!$filter) {
            return $this->connection->llen($key);
        }
        $totalItems = 0;
        foreach ($this->connection->lrange($key, 0, -1) as $index => $element) {
            $item = $this->createElement($index, $element);
            if (Filter::apply($item, $filter)) {
                $totalItems++;
            }
        }
        return $totalItems;
    }

    public function items(string $key, int $page, int $onPage, array $filter = []): array
    {
        $items = [];
        $offset = ($page - 1) * $onPage;
        if (!$filter) {
            $res = $this->connection->lrange($key, $offset, $offset + $onPage - 1);
            foreach ($res as $pos => $element) {
                $index = $offset + $pos;
                $items[$index] = $this->createElement($index, $element);
            }
            return $items;
        }

        $skipped = 0;
        foreach ($this->connection->lrange($key, 0, -1) as $index => $element) {
            $item = $this->createElement($index, $element);
            if (!Filter::apply($item, $filter)) {
                continue;
            }
            if ($skipped < $offset) {
                $skipped++;
                continue;
            }
            $items[$index] = $item;
            if (count($items) === $onPage) {
                break;
            }
        }
        return $items;
    }

    private function createElement(int $index, string $element): array
    {
        return [
            'index' => $index,
            'element' => $element,
            'length' => strlen($element),
        ];
    }
}
