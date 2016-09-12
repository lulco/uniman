<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\AbstractDriver;
use Adminerng\Drivers\Redis\Forms\RedisHashKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisKeyItemForm;
use RedisProxy\RedisProxy;

class RedisDriver extends AbstractDriver
{
    const TYPE_KEY = 'key';
    const TYPE_HASH = 'hash';
    const TYPE_SET = 'set';

    public function check()
    {
        return extension_loaded('redis') || class_exists('Predis\Client');
    }

    public function type()
    {
        return 'redis';
    }

    public function defaultCredentials()
    {
        return [
            'host' => 'localhost',
            'port' => '6379',
            'database' => '0',
        ];
    }

    public function connect(array $credentials)
    {
        $this->connection = new RedisProxy($credentials['host'], $credentials['port'], $credentials['database']);
    }

    public function databaseTitle()
    {
        return 'database';
    }

    public function databasesHeaders()
    {
        return [
            'Database',
            'Keys',
            'Expires',
            'Avg ttl'
        ];
    }

    public function tablesHeaders()
    {
        return [
            self::TYPE_KEY => ['Key', 'Value', 'Length'],
            self::TYPE_HASH => ['Hash', 'Number of fields'],
            self::TYPE_SET => ['Set', 'Number of members'],
        ];
    }

    public function itemsTitles($type = null)
    {
        $titles = [
            self::TYPE_KEY => 'Key',
            self::TYPE_HASH => 'Keys',
            self::TYPE_SET => 'Members',
        ];
        return $type === null ? $titles : $titles[$type];
    }

    public function itemsHeaders($type, $title)
    {
        $headers = [
            self::TYPE_KEY => [
                'Key', 'Length', 'Value'
            ],
            self::TYPE_HASH => [
                'Key', 'Length', 'Value'
            ],
            self::TYPE_SET => [
                'Member', 'Length'
            ]
        ];
        return isset($headers[$type]) ? $headers[$type] : [];
    }

    public function getCredentialsForm()
    {
        return new RedisForm();
    }

    public function itemForm($database, $type, $table, $item)
    {
        $this->dataManager()->selectDatabase($database);
        if ($type == self::TYPE_HASH) {
            return new RedisHashKeyItemForm($this->connection, $table, $item);
        } elseif ($type == self::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $item);
        }
    }

    protected function getPermissions()
    {
        return new RedisPermissions();
    }

    protected function getDataManager()
    {
        return new RedisDataManager($this->connection);
    }
}
