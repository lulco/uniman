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
            $column = (new Column())
                ->setKey($key)
                ->setTitle('memcache.headers.servers.' . $key)
                ->setIsSortable(true);
            if (isset($settings['is_numeric'])) {
                $column->setIsNumeric($settings['is_numeric']);
            }
            if (isset($settings['is_size'])) {
                $column->setIsSize($settings['is_size']);
            }
            if (isset($settings['is_time'])) {
                $column->setIsTime($settings['is_time']);
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
            $columns[] = (new Column())
                ->setKey('key')
                ->setTitle('memcache.columns.' . $type . '.key')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('value')
                ->setTitle('memcache.columns.' . $type . '.value')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('length')
                ->setTitle('memcache.columns.' . $type . '.length')
                ->setIsSortable(true)
                ->setIsFilterable(true)
                ->setIsNumeric(true);
            $columns[] = (new Column())
                ->setKey('expiration')
                ->setTitle('memcache.columns.' . $type . '.expiration')
                ->setIsSortable(true)
                ->setIsFilterable(true)
                ->setIsNumeric(true)
                ->setIsTime(true);
            $columns[] = (new Column())
                ->setKey('compressed')
                ->setTitle('memcache.columns.' . $type . '.compressed')
                ->setIsSortable(true)
                ->setIsFilterable(true);
        }
        return $columns;
    }
}
