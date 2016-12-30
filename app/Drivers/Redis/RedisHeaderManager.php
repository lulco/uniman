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
            ->setIsSortable(true);
        $columns[] = (new Column('keys', 'redis.headers.databases.keys'))
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column('expires', 'redis.headers.databases.expires'))
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column('avg_ttl', 'redis.headers.databases.avg_ttl'))
            ->setIsSortable(true)
            ->setIsNumeric(true)
            ->setDecimals(2);
        return $columns;
    }

    public function tablesHeaders()
    {
        $keyColumns = [];
        $keyColumns[] = (new Column('key', 'redis.headers.keys.key'));

        $hashColumns = [];
        $hashColumns[] = (new Column('key', 'redis.headers.hashes.key'))
            ->setIsSortable(true);
        $hashColumns[] = (new Column('number_of_fields', 'redis.headers.hashes.number_of_fields'))
            ->setIsSortable(true)
            ->setIsNumeric(true);

        $setColumns = [];
        $setColumns[] = (new Column('key', 'redis.headers.sets.key'))
            ->setIsSortable(true);
        $setColumns[] = (new Column('number_of_members', 'redis.headers.sets.number_of_members'))
            ->setIsSortable(true)
            ->setIsNumeric(true);

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
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('length', 'redis.columns.' . $type . '.length'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('value', 'redis.columns.' . $type . '.value'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
        } elseif ($type == RedisDriver::TYPE_SET) {
            $columns[] = (new Column('member', 'redis.columns.' . $type . '.member'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('length', 'redis.columns.' . $type . '.length'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
        }
        return $columns;
    }
}
