<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Forms\DefaultFormManager;
use Adminerng\Drivers\Redis\Forms\RedisCreateHashForm;
use Adminerng\Drivers\Redis\Forms\RedisCreateSetForm;
use Adminerng\Drivers\Redis\Forms\RedisEditDatabaseForm;
use Adminerng\Drivers\Redis\Forms\RedisEditSetForm;
use Adminerng\Drivers\Redis\Forms\RedisHashKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisRenameHashForm;
use Adminerng\Drivers\Redis\Forms\RedisSetMemberForm;
use RedisProxy\RedisProxy;

class RedisFormManager extends DefaultFormManager
{
    private $connection;

    private $databaseAliasStorage;

    public function __construct(RedisProxy $connection, RedisDatabaseAliasStorage $databaseAliasStorage)
    {
        $this->connection = $connection;
        $this->databaseAliasStorage = $databaseAliasStorage;
    }

    public function itemForm($database, $type, $table, $item)
    {
        if ($type == RedisDriver::TYPE_HASH) {
            return new RedisHashKeyItemForm($this->connection, $table, $item);
        } elseif ($type == RedisDriver::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $item);
        } elseif ($type == RedisDriver::TYPE_SET) {
            return new RedisSetMemberForm($this->connection, $table, $item);
        }
    }

    public function tableForm($database, $type, $table)
    {
        if ($type == RedisDriver::TYPE_HASH) {
            if ($table) {
                return new RedisRenameHashForm($this->connection, $table);
            }
            return new RedisCreateHashForm($this->connection);
        } elseif ($type == RedisDriver::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $table);
        } elseif ($type == RedisDriver::TYPE_SET) {
            if (!$table) {
                return new RedisCreateSetForm($this->connection);
            }
            return new RedisEditSetForm($this->connection, $table);
        }
    }

    public function databaseForm($database)
    {
        return new RedisEditDatabaseForm($database, $this->databaseAliasStorage);
    }
}
