<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class RedisHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('database')
            ->setTitle('redis.headers.databases.database')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('keys')
            ->setTitle('redis.headers.databases.keys')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('expires')
            ->setTitle('redis.headers.databases.expires')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('avg_ttl')
            ->setTitle('redis.headers.databases.avg_ttl')
            ->setIsSortable(true)
            ->setIsNumeric(true)
            ->setDecimals(2);
        return $columns;
    }

    public function tablesHeaders()
    {
        $keyColumns = [];
        $keyColumns[] = (new Column())
            ->setKey('key')
            ->setTitle('redis.headers.keys.key')
            ->setIsSortable(true);
        $keyColumns[] = (new Column())
            ->setKey('value')
            ->setTitle('redis.headers.keys.value')
            ->setIsSortable(true);
        $keyColumns[] = (new Column())
            ->setKey('length')
            ->setTitle('redis.headers.keys.length')
            ->setIsSortable(true)
            ->setIsNumeric(true);

        $hashColumns = [];
        $hashColumns[] = (new Column())
            ->setKey('key')
            ->setTitle('redis.headers.hashes.key')
            ->setIsSortable(true);
        $hashColumns[] = (new Column())
            ->setKey('number_of_fields')
            ->setTitle('redis.headers.hashes.number_of_fields')
            ->setIsSortable(true)
            ->setIsNumeric(true);

        $setColumns = [];
        $setColumns[] = (new Column())
            ->setKey('key')
            ->setTitle('redis.headers.sets.key')
            ->setIsSortable(true);
        $setColumns[] = (new Column())
            ->setKey('number_of_members')
            ->setTitle('redis.headers.sets.number_of_members')
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
            $columns[] = (new Column())
                ->setKey('key')
                ->setTitle('redis.columns.' . $type . '.key')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('length')
                ->setTitle('redis.columns.' . $type . '.length')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('value')
                ->setTitle('redis.columns.' . $type . '.value')
                ->setIsSortable(true)
                ->setIsFilterable(true);
        } elseif ($type == RedisDriver::TYPE_SET) {
            $columns[] = (new Column())
                ->setKey('member')
                ->setTitle('redis.columns.' . $type . '.member')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('length')
                ->setTitle('redis.columns.' . $type . '.length')
                ->setIsSortable(true)
                ->setIsFilterable(true);
        }
        return $columns;
    }
}
