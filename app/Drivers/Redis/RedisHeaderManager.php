<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class RedisHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column('database', 'redis.headers.databases.database'))
            ->setSortable(true);
        $columns[] = (new Column('alias', 'redis.headers.databases.alias'))
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
            ->setDecimals(2);
        return $columns;
    }

    public function tablesHeaders()
    {
        $keyColumns = [];
        $keyColumns[] = (new Column('key', 'redis.headers.keys.key'));

        $hashColumns = [];
        $hashColumns[] = (new Column('key', 'redis.headers.hashes.key'))
            ->setSortable(true);
        $hashColumns[] = (new Column('number_of_fields', 'redis.headers.hashes.number_of_fields'))
            ->setSortable(true)
            ->setNumeric(true);

        $setColumns = [];
        $setColumns[] = (new Column('key', 'redis.headers.sets.key'))
            ->setSortable(true);
        $setColumns[] = (new Column('number_of_members', 'redis.headers.sets.number_of_members'))
            ->setSortable(true)
            ->setNumeric(true);

        return [
            RedisDriver::TYPE_KEY => $keyColumns,
            RedisDriver::TYPE_HASH => $hashColumns,
            RedisDriver::TYPE_SET => $setColumns,
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
        }
        return $columns;
    }
}
