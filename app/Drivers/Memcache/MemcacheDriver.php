<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\AbstractDriver;
use Memcache;

class MemcacheDriver extends AbstractDriver
{
    const TYPE_KEY = 'key';
    
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

    public function tablesHeaders()
    {
        return [];
    }

    public function columns($type, $table)
    {
        $headers = [
            self::TYPE_KEY => ['Key', 'Value', 'Size', 'Expiration', 'Flags']
        ];
        return isset($headers[$type]) ? $headers[$type] : [];
    }

    protected function getPermissions()
    {
        return new MemcachePermissions();
    }

    protected function getCredentialsForm()
    {
        return new MemcacheForm();
    }

    protected function getDataManager()
    {
        return new MemcacheDataManager($this->connection);
    }
}
