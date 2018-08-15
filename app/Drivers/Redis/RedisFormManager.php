<?php

namespace UniMan\Drivers\Redis;

use RedisProxy\RedisProxy;
use UniMan\Core\Forms\DefaultFormManager;
use UniMan\Drivers\Redis\Forms\RedisCreateHashForm;
use UniMan\Drivers\Redis\Forms\RedisCreateSetForm;
use UniMan\Drivers\Redis\Forms\RedisEditDatabaseForm;
use UniMan\Drivers\Redis\Forms\RedisEditSetForm;
use UniMan\Drivers\Redis\Forms\RedisHashKeyItemForm;
use UniMan\Drivers\Redis\Forms\RedisKeyItemForm;
use UniMan\Drivers\Redis\Forms\RedisListForm;
use UniMan\Drivers\Redis\Forms\RedisListElementForm;
use UniMan\Drivers\Redis\Forms\RedisRenameHashForm;
use UniMan\Drivers\Redis\Forms\RedisSetMemberForm;
use UniMan\Drivers\Redis\RedisDatabaseAliasStorage;

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
        if ($type === RedisDriver::TYPE_HASH) {
            return new RedisHashKeyItemForm($this->connection, $table, $item);
        } elseif ($type === RedisDriver::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $item);
        } elseif ($type === RedisDriver::TYPE_SET) {
            return new RedisSetMemberForm($this->connection, $table, $item);
        } elseif ($type === RedisDriver::TYPE_LIST) {
            return new RedisListElementForm($this->connection, $table, $item);
        }
    }

    public function tableForm($database, $type, $table)
    {
        if ($type === RedisDriver::TYPE_HASH) {
            if ($table) {
                return new RedisRenameHashForm($this->connection, $table);
            }
            return new RedisCreateHashForm($this->connection);
        } elseif ($type === RedisDriver::TYPE_KEY) {
            return new RedisKeyItemForm($this->connection, $table);
        } elseif ($type === RedisDriver::TYPE_SET) {
            if (!$table) {
                return new RedisCreateSetForm($this->connection);
            }
            return new RedisEditSetForm($this->connection, $table);
        } elseif ($type === RedisDriver::TYPE_LIST) {
            return new RedisListForm($this->connection, $table);
        }
    }

    public function databaseForm($database)
    {
        return new RedisEditDatabaseForm($database, $this->databaseAliasStorage);
    }
}
