<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Column;
use Adminerng\Core\Exception\ConnectException;
use Adminerng\Drivers\Redis\Forms\RedisCreateHashForm;
use Adminerng\Drivers\Redis\Forms\RedisHashKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisRenameHashForm;
use RedisException;
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
        try {
            $this->connection = new RedisProxy($credentials['host'], $credentials['port'], $credentials['database']);
            $this->connection->select($credentials['database']);
        } catch (RedisException $e) {
            throw new ConnectException($e->getMessage());
        }
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

    public function columns($type, $table)
    {
        $columns = [];
        if ($type == self::TYPE_KEY || $type == self::TYPE_HASH) {
            $columns[] = (new Column())
                ->setKey('key')
                ->setTitle('redis.columns.' . $type . '.key');
            $columns[] = (new Column())
                ->setKey('length')
                ->setTitle('redis.columns.' . $type . '.length');
            $columns[] = (new Column())
                ->setKey('value')
                ->setTitle('redis.columns.' . $type . '.value');
        } elseif ($type == self::TYPE_SET) {
            $columns[] = (new Column())
                ->setKey('member')
                ->setTitle('redis.columns.' . $type . '.member');
            $columns[] = (new Column())
                ->setKey('length')
                ->setTitle('redis.columns.' . $type . '.length');
        }
        return $columns;
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

    public function tableForm($database, $type, $table)
    {
        $this->dataManager()->selectDatabase($database);
        if ($type == self::TYPE_HASH) {
            if ($table) {
                return new RedisRenameHashForm($this->connection, $table);
            }
            return new RedisCreateHashForm($this->connection);
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
