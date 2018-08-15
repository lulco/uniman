<?php

namespace UniMan\Drivers\Redis;

use RedisProxy\RedisProxy;
use UniMan\Core\DataManager\AbstractDataManager;
use UniMan\Core\Utils\Multisort;
use UniMan\Drivers\Redis\DataManager\RedisHashDataManager;
use UniMan\Drivers\Redis\DataManager\RedisKeyDataManager;
use UniMan\Drivers\Redis\DataManager\RedisListDataManager;
use UniMan\Drivers\Redis\DataManager\RedisSetDataManager;
use UniMan\Drivers\Redis\RedisDatabaseAliasStorage;

class RedisDataManager extends AbstractDataManager
{
    private $connection;

    private $databaseAliasStorage;

    private $itemsCountCache = false;

    public function __construct(RedisProxy $connection, RedisDatabaseAliasStorage $databaseAliasStorage)
    {
        $this->connection = $connection;
        $this->databaseAliasStorage = $databaseAliasStorage;
    }

    public function databases(array $sorting = [])
    {
        $keyspace = $this->connection->info('keyspace');
        $aliases = $this->databaseAliasStorage->loadAll();
        $databases = [];
        foreach ($keyspace as $db => $info) {
            $db = str_replace('db', '', $db);
            $alias = isset($aliases[$db]) ? ' (' . $aliases[$db] . ')' : '';
            $info['database'] = $db . $alias;
            $databases[$db] = $info;
        }
        return Multisort::sort($databases, $sorting);
    }

    protected function getDatabaseNameColumn()
    {
        return 'database';
    }

    public function tablesCount()
    {
        $tables = [
            RedisDriver::TYPE_KEY => 0,
            RedisDriver::TYPE_HASH => 0,
            RedisDriver::TYPE_SET => 0,
            RedisDriver::TYPE_LIST => 0,
        ];
        foreach ($this->connection->keys('*') as $key) {
            $type = $this->connection->type($key);
            switch ($type) {
                case RedisProxy::TYPE_STRING:
                    $tables[RedisDriver::TYPE_KEY]++;
                    break;
                case RedisProxy::TYPE_HASH:
                    $tables[RedisDriver::TYPE_HASH]++;
                    break;
                case RedisProxy::TYPE_SET:
                    $tables[RedisDriver::TYPE_SET]++;
                    break;
                case RedisProxy::TYPE_LIST:
                    $tables[RedisDriver::TYPE_LIST]++;
                    break;
                default:
                    break;
            }
        }
        return $tables;
    }

    public function tables(array $sorting = [])
    {
        $tables = [
            RedisDriver::TYPE_KEY => [
                'list_of_all_keys' => [
                    'key' => 'Show all keys',
                    'number_of_keys' => 0,
                ]
            ],
            RedisDriver::TYPE_HASH => [],
            RedisDriver::TYPE_SET => [],
            RedisDriver::TYPE_LIST => [],
        ];
        foreach ($this->connection->keys('*') as $key) {
            $type = $this->connection->type($key);
            if ($type === RedisProxy::TYPE_STRING) {
                $tables[RedisDriver::TYPE_KEY]['list_of_all_keys']['number_of_keys']++;
            } elseif ($type === RedisProxy::TYPE_HASH) {
                $result = $this->connection->hlen($key);
                $tables[RedisDriver::TYPE_HASH][$key] = [
                    'key' => $key,
                    'number_of_fields' => $result,
                ];
            } elseif ($type === RedisProxy::TYPE_SET) {
                $result = $this->connection->scard($key);
                $tables[RedisDriver::TYPE_SET][$key] = [
                    'key' => $key,
                    'number_of_members' => $result,
                ];
            } elseif ($type === RedisProxy::TYPE_LIST) {
                $result = $this->connection->llen($key);
                $tables[RedisDriver::TYPE_LIST][$key] = [
                    'key' => $key,
                    'number_of_elements' => $result,
                ];
            }
            // TODO sorted set
        }
        return [
            RedisDriver::TYPE_KEY => Multisort::sort($tables[RedisDriver::TYPE_KEY], $sorting),
            RedisDriver::TYPE_HASH => Multisort::sort($tables[RedisDriver::TYPE_HASH], $sorting),
            RedisDriver::TYPE_SET => Multisort::sort($tables[RedisDriver::TYPE_SET], $sorting),
            RedisDriver::TYPE_LIST => Multisort::sort($tables[RedisDriver::TYPE_LIST], $sorting),
        ];
    }

    public function itemsCount($type, $table, array $filter = [])
    {
        if ($this->itemsCountCache !== false) {
            return $this->itemsCountCache;
        }
        $itemsCount = 0;
        if ($type === RedisDriver::TYPE_KEY) {
            $manager = new RedisKeyDataManager($this->connection);
            $itemsCount = $manager->itemsCount($filter);
        } elseif ($type === RedisDriver::TYPE_HASH) {
            $manager = new RedisHashDataManager($this->connection);
            $itemsCount = $manager->itemsCount($table, $filter);
        } elseif ($type === RedisDriver::TYPE_SET) {
            $manager = new RedisSetDataManager($this->connection);
            $itemsCount = $manager->itemsCount($table, $filter);
        } elseif ($type === RedisDriver::TYPE_LIST) {
            $manager = new RedisListDataManager($this->connection);
            $itemsCount = $manager->itemsCount($table, $filter);
        }
        $this->itemsCountCache = $itemsCount;
        return $itemsCount;
    }

    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $items = [];
        if ($type === RedisDriver::TYPE_KEY) {
            $manager = new RedisKeyDataManager($this->connection);
            $items = $manager->items($page, $onPage, $filter);
        } elseif ($type === RedisDriver::TYPE_HASH) {
            $manager = new RedisHashDataManager($this->connection);
            $items = $manager->items($table, $page, $onPage, $filter);
        } elseif ($type === RedisDriver::TYPE_SET) {
            $manager = new RedisSetDataManager($this->connection);
            $items = $manager->items($table, $page, $onPage, $filter);
        } elseif ($type === RedisDriver::TYPE_LIST) {
            $manager = new RedisListDataManager($this->connection);
            $items = $manager->items($table, $page, $onPage, $filter);
        }

        if ($this->itemsCount($type, $table, $filter) <= $onPage) {
            $items = Multisort::sort($items, $sorting);
        } elseif ($sorting) {
            $this->addMessage('Sorting has not been applied because the number of items is greater then the limit. Increase the limit or modify the filter.');
        }

        return $items;
    }

    public function deleteItem($type, $table, $item)
    {
        if ($type === RedisDriver::TYPE_HASH) {
            return $this->connection->hdel($table, $item);
        }
        if ($type === RedisDriver::TYPE_KEY) {
            return $this->connection->del($item);
        }
        if ($type === RedisDriver::TYPE_SET) {
            return $this->connection->srem($table, $item);
        }
        if ($type === RedisDriver::TYPE_LIST) {
            $value = md5(uniqid());
            $this->connection->lset($table, $item, $value);
            return $this->connection->lrem($table, $value);
        }
        return parent::deleteItem($type, $table, $item);
    }

    public function deleteTable($type, $table)
    {
        return $this->connection->del($table);
    }

    public function selectDatabase($database)
    {
        $this->connection->select($database);
    }

    public function execute($commands)
    {
        $listOfCommands = array_filter(array_map('trim', explode("\n", $commands)), function ($command) {
            return $command;
        });

        $results = [];
        foreach ($listOfCommands as $command) {
            $commandParts = explode(' ', $command);
            $function = array_shift($commandParts);
            $function = strtolower($function);
            $results[$command]['headers'] = $this->headers($function);
            $rows = call_user_func_array([$this->connection, $function], $commandParts);
            $items = $this->getItems($function, $rows);
            $results[$command]['items'] = $items;
            $results[$command]['count'] = count($items);
        }
        return $results;
    }

    private function headers($function)
    {
        if ($function === 'get' || $function === 'hget') {
            return ['value'];
        }
        if ($function === 'keys') {
            return ['key'];
        }
        if ($function === 'hgetall') {
            return ['key', 'value'];
        }
        if ($function === 'hlen') {
            return ['items_count'];
        }
        return [];
    }

    private function getItems($function, $rows)
    {
        $items = [];
        if ($function === 'keys') {
            foreach ($rows as $key) {
                $items[] = [$key];
            }
        } elseif ($function === 'hgetall') {
            foreach ($rows as $key => $value) {
                $items[] = [$key, $value];
            }
        } else {
            return [[$rows]];
        }
        return $items;
    }
}
