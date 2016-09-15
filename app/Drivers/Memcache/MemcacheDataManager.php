<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\DataManagerInterface;
use Adminerng\Core\Exception\NoTablesJustItemsException;
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
        throw new NoTablesJustItemsException('key', 'all');
    }

    public function itemsCount($database, $type, $table)
    {
        if ($table == 'all') {
            $slabs = $this->connection->getExtendedStats('slabs');
            $databaseSlabs = isset($slabs[$database]) ? $slabs[$database] : [];
            $count = 0;
            foreach (array_keys($databaseSlabs) as $slabId) {
                if (!is_int($slabId)) {
                    continue;
                }
                $slabKeys = $this->getSlabKeys($database, $slabId);
                $count += count($slabKeys);
            }
            return $count;
        }

        $stats = $this->connection->getExtendedStats('cachedump',(int)$table);
        $keys = isset($stats[$database]) ? $stats[$database] : [];
        return count($keys);
    }

    public function items($database, $type, $table, $page, $onPage)
    {
        if ($table == 'all') {
            $slabs = $this->connection->getExtendedStats('slabs');
            $databaseSlabs = isset($slabs[$database]) ? $slabs[$database] : [];
            $keys = [];
            foreach (array_keys($databaseSlabs) as $slabId) {
                if (!is_int($slabId)) {
                    continue;
                }
                $keys = array_merge($keys, $this->getSlabKeys($database, $slabId));
            }
        } else {
            $keys = $this->getSlabKeys($database, $table);
        }

        ksort($keys);
        $keys = array_slice($keys, ($page - 1) * $onPage, $onPage, true);

        $items = [];
        foreach ($keys as $key => $info) {
            $flags = false;
            $items[$key] = [
                'key' => $key,
                'value' => $this->connection->get($key, $flags),
                'size' => $info[0],
                'expiration' => ($info[1] - time()) > 0 ? $info[1] - time() : null,
                'flags' => $flags == MEMCACHE_COMPRESSED ? 'compressed' : null,    // TODO "compressed" should be translated
            ];
        }
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

    private function getSlabKeys($database, $slabId)
    {
        $cacheDump = $this->connection->getExtendedStats('cachedump',(int) $slabId);
        $keys = isset($cacheDump[$database]) ? $cacheDump[$database] : [];
        if ($keys === false) {
            return [];
        }
        return $keys;
    }
}
