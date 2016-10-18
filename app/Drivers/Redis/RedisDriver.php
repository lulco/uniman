<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Column;
use Adminerng\Core\Exception\ConnectException;
use Adminerng\Drivers\Redis\Forms\RedisCreateHashForm;
use Adminerng\Drivers\Redis\Forms\RedisCreateSetForm;
use Adminerng\Drivers\Redis\Forms\RedisEditSetForm;
use Adminerng\Drivers\Redis\Forms\RedisHashKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisRenameHashForm;
use Adminerng\Drivers\Redis\Forms\RedisSetMemberForm;
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
        $columns = [];
        $columns[] = (new Column())
            ->setKey('database')
            ->setTitle('redis.headers.databases.database')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('keys')
            ->setTitle('redis.headers.databases.keys')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('expires')
            ->setTitle('redis.headers.databases.expires')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('avg_ttl')
            ->setTitle('redis.headers.databases.avg_ttl')
            ->setIsSortable(true)
            ->setIsNumeric(true)
            ->setDecimals(2);
        return $columns;
    }

    public function tablesHeaders()
    {
        $keyColumns = [];
        $keyColumns[] = (new Column())
            ->setKey('key')
            ->setTitle('redis.headers.keys.key')
            ->setIsSortable(true);
        $keyColumns[] = (new Column())
            ->setKey('value')
            ->setTitle('redis.headers.keys.value')
            ->setIsSortable(true);
        $keyColumns[] = (new Column())
            ->setKey('length')
            ->setTitle('redis.headers.keys.length')
            ->setIsSortable(true);

        $hashColumns = [];
        $hashColumns[] = (new Column())
            ->setKey('key')
            ->setTitle('redis.headers.hashes.key')
            ->setIsSortable(true);
        $hashColumns[] = (new Column())
            ->setKey('number_of_fields')
            ->setTitle('redis.headers.hashes.number_of_fields')
            ->setIsSortable(true)
            ->setIsNumeric(true);

        $setColumns = [];
        $setColumns[] = (new Column())
            ->setKey('key')
            ->setTitle('redis.headers.sets.key')
            ->setIsSortable(true);
        $setColumns[] = (new Column())
            ->setKey('number_of_members')
            ->setTitle('redis.headers.sets.number_of_members')
            ->setIsSortable(true)
            ->setIsNumeric(true);

        return [
            self::TYPE_KEY => $keyColumns,
            self::TYPE_HASH => $hashColumns,
            self::TYPE_SET => $setColumns,
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
        if ($type == self::TYPE_HASH) {
            return new RedisHashKeyItemForm($this->connection, $table, $item);
        } elseif ($type == self::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $item);
        } elseif ($type == self::TYPE_SET) {
            return new RedisSetMemberForm($this->connection, $table, $item);
        }
    }

    public function tableForm($database, $type, $table)
    {
        if ($type == self::TYPE_HASH) {
            if ($table) {
                return new RedisRenameHashForm($this->connection, $table);
            }
            return new RedisCreateHashForm($this->connection);
        } elseif ($type == self::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $table);
        } elseif ($type == self::TYPE_SET) {
            if (!$table) {
                return new RedisCreateSetForm($this->connection, $table);
            }
            return new RedisEditSetForm($this->connection, $table);
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
