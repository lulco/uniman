<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\DataManager\AbstractDataManager;
use Adminerng\Core\Exception\NoTablesJustItemsException;
use Adminerng\Core\Utils\Multisort;
use Adminerng\Core\Utils\Filter;
use Memcache;
use Nette\Localization\ITranslator;

class MemcacheDataManager extends AbstractDataManager
{
    private $translator;

    private $connection;

    private $database;

    public function __construct(Memcache $connection, ITranslator $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    public function databases(array $sorting = [])
    {
        $stats = $this->connection->getExtendedStats();
        $allSlabs = $this->connection->getExtendedStats('slabs');
        $databases = [];
        foreach ($stats as $server => $serverInfo) {
            $databases[$server] = [
                'server' => $server,
                'process_id' => $serverInfo['pid'],
                'uptime' => $serverInfo['uptime'],
                'current_items' => $serverInfo['curr_items'],
                'total_items' => $serverInfo['total_items'],
                'size' => $serverInfo['bytes'],
                'active_slabs' => $allSlabs[$server]['active_slabs'],
                'total_malloced' => $allSlabs[$server]['total_malloced'],
            ];
        }
        return Multisort::sort($databases, $sorting);
    }

    public function tables(array $sorting = [])
    {
        throw new NoTablesJustItemsException('key', 'all');
    }

    public function itemsCount($type, $table, array $filter = [])
    {
        if ($table == 'all') {
            $slabs = $this->connection->getExtendedStats('slabs');
            $databaseSlabs = isset($slabs[$this->database]) ? $slabs[$this->database] : [];
            $count = 0;
            foreach (array_keys($databaseSlabs) as $slabId) {
                if (!is_int($slabId)) {
                    continue;
                }
                $slabKeys = $this->getSlabKeys($this->database, $slabId);
                $count += count($slabKeys);
            }
            return $count;
        }

        $stats = $this->connection->getExtendedStats('cachedump', (int)$table);
        $keys = isset($stats[$this->database]) ? $stats[$this->database] : [];
        return count($keys);
    }

    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        if ($table == 'all') {
            $slabs = $this->connection->getExtendedStats('slabs');
            $databaseSlabs = isset($slabs[$this->database]) ? $slabs[$this->database] : [];
            $keys = [];
            foreach (array_keys($databaseSlabs) as $slabId) {
                if (!is_int($slabId)) {
                    continue;
                }
                $keys = array_merge($keys, $this->getSlabKeys($this->database, $slabId));
            }
        } else {
            $keys = $this->getSlabKeys($this->database, $table);
        }

        $items = [];
        foreach ($keys as $key => $info) {
            $flags = false;
            $item = [
                'key' => $key,
                'value' => $this->connection->get($key, $flags),
                'length' => $info[0],
                'expiration' => ($info[1] - time()) > 0 ? $info[1] - time() : null,
                'compressed' => $flags == MEMCACHE_COMPRESSED ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
            ];
            if (Filter::apply($item, $filter)) {
                $items[$key] = $item;
            }
        }

        $items = Multisort::sort($items, $sorting);
        return array_slice($items, ($page - 1) * $onPage, $onPage, true);
    }

    public function deleteItem($type, $table, $item)
    {
        return $this->connection->delete($item);
    }

    public function selectDatabase($database)
    {
        return $this->database = $database;
    }

    private function getSlabKeys($database, $slabId)
    {
        $cacheDump = $this->connection->getExtendedStats('cachedump', (int) $slabId);
        $keys = isset($cacheDump[$database]) ? $cacheDump[$database] : [];
        if ($keys === false) {
            return [];
        }
        return $keys;
    }
}
