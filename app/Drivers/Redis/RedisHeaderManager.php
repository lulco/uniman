<?php

namespace UniMan\Drivers\Redis;

use UniMan\Core\Column;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;

class RedisHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column('database', 'redis.headers.databases.database'))
            ->setSortable(true);
        $columns[] = (new Column('keys', 'redis.headers.databases.keys'))
            ->setSortable(true)
            ->setNumeric(true);
        $columns[] = (new Column('expires', 'redis.headers.databases.expires'))
            ->setSortable(true)
            ->setNumeric(true);
        $columns[] = (new Column('avg_ttl', 'redis.headers.databases.avg_ttl'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setTime(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $keyColumns = [];
        $keyColumns[] = (new Column('key', 'redis.headers.keys.key'));
        $keyColumns[] = (new Column('number_of_keys', 'redis.headers.keys.number_of_keys'))
            ->setNumeric(true);

        $hashColumns = [];
        $hashColumns[] = (new Column('key', 'redis.headers.hashes.key'))
            ->setSortable(true);
        $hashColumns[] = (new Column('number_of_fields', 'redis.headers.hashes.number_of_fields'))
            ->setSortable(true)
            ->setNumeric(true);
        $hashColumns[] = (new Column('ttl', 'redis.headers.global.ttl'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setTime(true);

        $setColumns = [];
        $setColumns[] = (new Column('key', 'redis.headers.sets.key'))
            ->setSortable(true);
        $setColumns[] = (new Column('number_of_members', 'redis.headers.sets.number_of_members'))
            ->setSortable(true)
            ->setNumeric(true);
        $setColumns[] = (new Column('ttl', 'redis.headers.global.ttl'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setTime(true);

        $listColumns = [];
        $listColumns[] = (new Column('key', 'redis.headers.lists.key'))
            ->setSortable(true);
        $listColumns[] = (new Column('number_of_elements', 'redis.headers.lists.number_of_elements'))
            ->setSortable(true)
            ->setNumeric(true);
        $listColumns[] = (new Column('ttl', 'redis.headers.global.ttl'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setTime(true);

        $sortedSetColums = [];
        $sortedSetColums[] = (new Column('key', 'redis.headers.sorted_sets.key'))
            ->setSortable(true);
        $sortedSetColums[] = (new Column('number_of_members', 'redis.headers.sorted_sets.number_of_members'))
            ->setSortable(true)
            ->setNumeric(true);
        $sortedSetColums[] = (new Column('ttl', 'redis.headers.global.ttl'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setTime(true);

        return [
            RedisDriver::TYPE_KEY => $keyColumns,
            RedisDriver::TYPE_HASH => $hashColumns,
            RedisDriver::TYPE_SET => $setColumns,
            RedisDriver::TYPE_LIST => $listColumns,
            RedisDriver::TYPE_SORTED_SET => $sortedSetColums,
        ];
    }

    public function itemsHeaders($type, $table)
    {
        $columns = [];
        if ($type == RedisDriver::TYPE_KEY || $type == RedisDriver::TYPE_HASH) {
            $columns[] = (new Column('key', 'redis.columns.' . $type . '.key'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('length', 'redis.columns.' . $type . '.length'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('value', 'redis.columns.' . $type . '.value'))
                ->setSortable(true)
                ->setFilterable(true);
        } elseif ($type == RedisDriver::TYPE_SET) {
            $columns[] = (new Column('member', 'redis.columns.' . $type . '.member'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('length', 'redis.columns.' . $type . '.length'))
                ->setSortable(true)
                ->setFilterable(true);
        } elseif ($type === RedisDriver::TYPE_LIST) {
            $columns[] = (new Column('index', 'redis.columns.' . $type . '.index'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('element', 'redis.columns.' . $type . '.element'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('length', 'redis.columns.' . $type . '.length'))
                ->setSortable(true)
                ->setFilterable(true);
        } elseif ($type === RedisDriver::TYPE_SORTED_SET) {
            $columns[] = (new Column('member', 'redis.columns.' . $type . '.member'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('score', 'redis.columns.' . $type . '.score'))
                ->setSortable(true)
                ->setNumeric(true)
                ->setDecimals(10)
                ->setFilterable(true);
            $columns[] = (new Column('length', 'redis.columns.' . $type . '.length'))
                ->setSortable(true)
                ->setNumeric(true)
                ->setFilterable(true);
        }
        return $columns;
    }
}
