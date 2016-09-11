<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\AbstractDriver;
use Memcache;

class MemcacheDriver extends AbstractDriver
{
    public function check()
    {
        return extension_loaded('memcache');
    }

    public function type()
    {
        return 'memcache';
    }

    public function defaultCredentials()
    {
        return [
            'servers' => 'localhost:11211',
        ];
    }

    public function connect(array $credentials)
    {
        $this->connection = new Memcache();
        $servers = array_map('trim', explode("\n", trim($credentials['servers'])));
        foreach ($servers as $server) {
            list($host, $port) = explode(':', $server, 2);
            $this->connection->addserver($host, $port);
        }
    }

    public function databaseTitle()
    {
        return 'server';
    }

    public function databasesHeaders()
    {
        return [
            'Server',
            'Process id',
            'Uptime',
            'Current items',
            'Total items',
            'Size',
            'Active slabs',
            'Total malloced',
        ];
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

    public function tablesHeaders()
    {
        return [
            'Slabs' => ['Slab ID', 'Size', 'Used chunks', 'Total chunks']
        ];
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
            $tables['Slabs'][$slabId] = [
                'size' => $slabInfo['mem_requested'],
                'used_chunks' => $slabInfo['used_chunks'],
                'total_chunks' => $slabInfo['total_chunks'],
            ];
        }
        return $tables;
    }

    public function itemsTitles($type = null)
    {
        $titles = [
            'Slabs' => 'Keys',
        ];
        return $type === null ? $titles : $titles[$type];
    }

    public function itemsHeaders($type)
    {
        $headers = [
            'Slabs' => ['Key', 'Value', 'Size', 'Expiration', 'Flags']
        ];
        return isset($headers[$type]) ? $headers[$type] : [];
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

    protected function getPermissions()
    {
        return new MemcachePermissions();
    }

    public function deleteItem($database, $type, $table, $item)
    {
        return $this->connection->delete($item);
    }

    protected function getCredentialsForm()
    {
        return new MemcacheForm();
    }
}
