<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\DataManagerInterface;
use Memcache;

class MemcacheDataManager implements DataManagerInterface
{
    private $connection;
    
    public function __construct(Memcache $connection)
    {
        $this->connection = $connection;
    }

    public function databases()
    {
        $stats = $this->connection->getExtendedStats();
        $allSlabs = $this->connection->getExtendedStats('slabs');
        $databases = [];
        foreach ($stats as $server => $serverInfo) {
            $databases[$server] = [
                'pid' => $serverInfo['pid'],
                'uptime' => $serverInfo['uptime'],
                'curr_items' => $serverInfo['curr_items'],
                'total_items' => $serverInfo['total_items'],
                'size' => $serverInfo['bytes'],
                'active_slabs' => $allSlabs[$server]['active_slabs'],
                'total_malloced' => $allSlabs[$server]['total_malloced'],
            ];
        }
        return $databases;
    }

    public function tables($database)
    {
        $slabs = isset($this->connection->getExtendedStats('slabs')[$database])
            ? $this->connection->getExtendedStats('slabs')[$database] : [];
        $tables = [];
        foreach ($slabs as $slabId => $slabInfo) {
            if (!is_int($slabId)) {
                continue;
            }
            $tables[MemcacheDriver::TYPE_SLAB][$slabId] = [
                'size' => $slabInfo['mem_requested'],
                'used_chunks' => $slabInfo['used_chunks'],
                'total_chunks' => $slabInfo['total_chunks'],
            ];
        }
        return $tables;
    }

    public function itemsCount($database, $type, $table)
    {
        $stats = $this->connection->getExtendedStats('cachedump',(int)$table);
        $keys = isset($stats[$database]) ? $stats[$database] : [];
        return count($keys);
    }

    public function items($database, $type, $table, $page, $onPage)
    {
        $items = [];
        $keys = isset($this->connection->getExtendedStats('cachedump',(int)$table)[$database])
            ? $this->connection->getExtendedStats('cachedump',(int)$table)[$database] : [];
        if ($keys === false) {
            return $items;
        }
        $keys = array_slice($keys, ($page - 1) * $onPage, $onPage, true);
        foreach ($keys as $key => $info) {
            $flags = false;
            $items[$key] = [
                'key' => $key,
                'value' => $this->connection->get($key, $flags),
                'size' => $info[0],
                'expiration' => ($info[1] - time()) > 0 ? $info[1] - time() : null,
                'flags' => $flags == MEMCACHE_COMPRESSED ? 'compressed' : null,
            ];
        }
        ksort($items);
        return $items;
    }

    public function deleteItem($database, $type, $table, $item)
    {
        return $this->connection->delete($item);
    }

    public function selectDatabase($database)
    {
        return null;
    }
}
