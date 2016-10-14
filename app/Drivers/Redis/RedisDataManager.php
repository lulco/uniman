<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\DataManagerInterface;
use RedisProxy\RedisProxy;

class RedisDataManager implements DataManagerInterface
{
    private $connection;

    public function __construct(RedisProxy $connection)
    {
        $this->connection = $connection;
    }

    public function databases(array $sorting = [])
    {
        $numberOfDatabases = $this->connection->config('get', 'databases')['databases'];
        $keyspace = $this->connection->info('keyspace');
        $databases = [];
        for ($i = 0; $i < $numberOfDatabases; ++$i) {
            $databases[$i] = $this->databaseInfo($keyspace, $i);
        }
        return $databases;
    }

    public function tables($database, array $sorting = [])
    {
        $this->selectDatabase($database);
        $tables = [
            RedisDriver::TYPE_KEY => [],
            RedisDriver::TYPE_HASH => [],
            RedisDriver::TYPE_SET => [],
        ];
        $commands = [
            'get' => RedisDriver::TYPE_KEY,
            'hLen' => RedisDriver::TYPE_HASH,
            'sCard' => RedisDriver::TYPE_SET,
        ];
        foreach ($this->connection->keys('*') as $key) {
            foreach ($commands as $command => $label) {
                $result = $this->connection->$command($key);
                if ($this->connection->getLastError() !== null) {
                    $this->connection->clearLastError();
                    continue;
                }
                $tables[$label][$key] = [
                    'key' => $key
                ];
                if ($label == RedisDriver::TYPE_KEY) {
                    $tables[$label][$key]['value'] = $result;
                    $tables[$label][$key]['length'] = strlen($result);
                } elseif ($label == RedisDriver::TYPE_HASH) {
                    $tables[$label][$key]['number_of_fields'] = $result;
                } elseif ($label == RedisDriver::TYPE_SET) {
                    $tables[$label][$key]['number_of_members'] = $result;
                }
                break;
            }
            ksort($tables[$label]);
        }
        ksort($tables);
        return $tables;
    }

    public function itemsCount($database, $type, $table, array $filter = [])
    {
        $this->selectDatabase($database);
        if ($type == RedisDriver::TYPE_HASH) {
            return $this->connection->hLen($table);
        }
        if ($type == RedisDriver::TYPE_KEY) {
            return 1;
        }
        if ($type == RedisDriver::TYPE_SET) {
            return $this->connection->sCard($table);
        }
        return 0;
    }

    public function items($database, $type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $items = [];
        $this->selectDatabase($database);
        if ($type == RedisDriver::TYPE_HASH) {
            $counter = 0;
            $iterator = '';
            do {
                $res = $this->connection->hscan($table, $iterator, null, $onPage);
                $counter++;
            } while ($counter != $page);
            $res = $res ?: [];
            foreach ($res as $key => $value) {
                $items[$key] = [
                    'key' => $key,
                    'length' => strlen($value),
                    'value' => $value,
                ];
            }
        } elseif ($type == RedisDriver::TYPE_KEY) {
            $value = $this->connection->get($table);
            $items[$table] = [
                'key' => $table,
                'length' => strlen($value),
                'value' => $value,
            ];
        } elseif ($type == RedisDriver::TYPE_SET) {
            $counter = 0;
            $iterator = '';
            do {
                $res = $this->connection->sscan($table, $iterator, null, $onPage);
                $counter++;
            } while ($counter != $page);
            $res = $res ?: [];
            foreach ($res as $item) {
                $items[$item] = [
                    'member' => $item,
                    'length' => strlen($item),
                ];
            }
        }
        return $items;
    }

    public function deleteItem($database, $type, $table, $item)
    {
        $this->selectDatabase($database);
        if ($type == RedisDriver::TYPE_HASH) {
            return $this->connection->hdel($table, $item);
        }
        if ($type == RedisDriver::TYPE_KEY) {
            return $this->connection->del($table);
        }
        if ($type == RedisDriver::TYPE_SET) {
            return $this->connection->srem($table, $item);
        }
        // TODO throw exception if type is not found?
        return false;
    }

    public function deleteTable($database, $type, $table)
    {
        $this->selectDatabase($database);
        return $this->connection->del($table);
    }

    private function databaseInfo($keyspace, $db)
    {
        $info = [
            'database' => $db,
            'keys' => 0,
            'expires' => null,
            'avg_ttl' => null,
        ];
        if (isset($keyspace['db' . $db])) {
            $dbKeyspace = explode(',', $keyspace['db' . $db]);
            $info['keys'] = explode('=', $dbKeyspace[0])[1];
            $info['expires'] = explode('=', $dbKeyspace[1])[1];
            $info['avg_ttl'] = explode('=', $dbKeyspace[2])[1];
        }
        return $info;
    }

    public function selectDatabase($database)
    {
        $this->connection->select($database);
    }
}
