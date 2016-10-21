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
            'uptime' => [],
            'current_items' => [],
            'total_items' => [],
            'size' => [],
            'active_slabs' => [],
            'total_malloced' => [],
        ];

        $columns = [];
        foreach ($fields as $key => $settings) {
            $column = (new Column())
                ->setKey($key)
                ->setTitle('memcache.headers.servers.' . $key)
                ->setIsSortable(true);
            if (!isset($settings['is_numeric'])) {
                $column->setIsNumeric(true);
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
                ->setTitle('memcache.columns.' . $type . '.key');
            $columns[] = (new Column())
                ->setKey('value')
                ->setTitle('memcache.columns.' . $type . '.value');
            $columns[] = (new Column())
                ->setKey('size')
                ->setTitle('memcache.columns.' . $type . '.size');
            $columns[] = (new Column())
                ->setKey('expiration')
                ->setTitle('memcache.columns.' . $type . '.expiration');
            $columns[] = (new Column())
                ->setKey('compressed')
                ->setTitle('memcache.columns.' . $type . '.compressed');
        }
        return $columns;
    }
}
