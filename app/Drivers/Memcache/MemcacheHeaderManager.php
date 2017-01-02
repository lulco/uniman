<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class MemcacheHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $fields = [
            'server' => ['is_numeric' => false],
            'process_id' => ['is_numeric' => false],
            'uptime' => ['is_numeric' => true, 'is_time' => true],
            'current_items' => ['is_numeric' => true],
            'total_items' => ['is_numeric' => true],
            'size' => ['is_numeric' => true, 'is_size' => true],
            'active_slabs' => ['is_numeric' => true],
            'total_malloced' => ['is_numeric' => true, 'is_size' => true],
        ];

        $columns = [];
        foreach ($fields as $key => $settings) {
            $column = (new Column($key, 'memcache.headers.servers.' . $key))
                ->setSortable(true);
            if (isset($settings['is_numeric'])) {
                $column->setNumeric($settings['is_numeric']);
            }
            if (isset($settings['is_size'])) {
                $column->setSize($settings['is_size']);
            }
            if (isset($settings['is_time'])) {
                $column->setTime($settings['is_time']);
            }
            $columns[] = $column;
        }
        return $columns;
    }

    public function tablesHeaders()
    {
        return [];
    }

    public function itemsHeaders($type, $table)
    {
        $columns = [];
        if ($type == MemcacheDriver::TYPE_KEY) {
            $columns[] = (new Column('key', 'memcache.columns.' . $type . '.key'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('value', 'memcache.columns.' . $type . '.value'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('length', 'memcache.columns.' . $type . '.length'))
                ->setSortable(true)
                ->setFilterable(true)
                ->setNumeric(true);
            $columns[] = (new Column('expiration', 'memcache.columns.' . $type . '.expiration'))
                ->setSortable(true)
                ->setFilterable(true)
                ->setNumeric(true)
                ->setTime(true);
            $columns[] = (new Column('compressed', 'memcache.columns.' . $type . '.compressed'))
                ->setSortable(true)
                ->setFilterable(true);
        }
        return $columns;
    }
}
