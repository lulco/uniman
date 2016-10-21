<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Exception\ConnectException;
use Adminerng\Drivers\Memcache\Forms\MemcacheCredentialsForm;
use Memcache;

class MemcacheDriver extends AbstractDriver
{
    const TYPE_KEY = 'key';

    public function extensions()
    {
        return ['memcache'];
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

    public function getCredentialsForm()
    {
        return new MemcacheCredentialsForm();
    }

    public function connect(array $credentials)
    {
        $this->connection = new Memcache();
        $servers = array_map('trim', explode("\n", trim($credentials['servers'])));
        foreach ($servers as $server) {
            list($host, $port) = explode(':', $server, 2);
            if (!$this->connection->addserver($host, $port)) {
                throw new ConnectException("Couldn't connect to $host:$port");
            }
        }
    }

    protected function getFormManager()
    {
        return new MemcacheFormManager($this->connection);
    }

    protected function getHeaderManager()
    {
        return new MemcacheHeaderManager();
    }

    protected function getPermissions()
    {
        return new MemcachePermissions();
    }

    protected function getDataManager()
    {
        return new MemcacheDataManager($this->connection, $this->translator);
    }
}
