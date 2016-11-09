<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Column;
use Adminerng\Core\DataManager\AbstractDataManager;
use Adminerng\Core\Multisort;
use RedisProxy\RedisProxy;

class RedisDataManager extends AbstractDataManager
{
    private $connection;

    private $itemsCountCache = false;

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
        return Multisort::sort($databases, $sorting);
    }

    public function tables(array $sorting = [])
    {
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
        }
        return [
            RedisDriver::TYPE_KEY => Multisort::sort($tables[RedisDriver::TYPE_KEY], $sorting),
            RedisDriver::TYPE_HASH => Multisort::sort($tables[RedisDriver::TYPE_HASH], $sorting),
            RedisDriver::TYPE_SET => Multisort::sort($tables[RedisDriver::TYPE_SET], $sorting),
        ];
    }

    public function itemsCount($type, $table, array $filter = [])
    {
        if ($this->itemsCountCache !== false) {
            return $this->itemsCountCache;
        }
        if ($type == RedisDriver::TYPE_HASH) {
            if (!$filter) {
                $this->itemsCountCache = $this->connection->hLen($table);
                return $this->itemsCountCache;
            } else {
                $totalItems = 0;
                foreach ($filter as $filterParts) {
                    if (isset($filterParts['key'][Column::OPERATOR_EQUAL])) {
                        $res = $this->connection->hget($table, $filterParts['key'][Column::OPERATOR_EQUAL]);
                        if ($res) {
                            $item = [
                                'key' => $filterParts['key'][Column::OPERATOR_EQUAL],
                                'length' => strlen($res),
                                'value' => $res,
                            ];
                            if ($this->applyFilter($item, $filter)) {
                                $totalItems++;
                            }
                        }
                        $this->itemsCountCache = $totalItems;
                        return $this->itemsCountCache;
                    }
                }
                $iterator = '';
                do {
                    $pattern = null;    // TODO create pattern based on filter contains / starts / ends
                    $res = $this->connection->hscan($table, $iterator, $pattern, 1000);
                    $res = $res ?: [];
                    foreach ($res as $key => $value) {
                        $item = [
                            'key' => $key,
                            'length' => strlen($value),
                            'value' => $value,
                        ];
                        if ($this->applyFilter($item, $filter)) {
                            $totalItems++;
                        }
                    }
                } while ($iterator !== 0);
                $this->itemsCountCache = $totalItems;
                return $this->itemsCountCache;
            }
        }
        if ($type == RedisDriver::TYPE_KEY) {
            return 1;
        }
        if ($type == RedisDriver::TYPE_SET) {
            if (!$filter) {
                return $this->connection->sCard($table);
            }
            $iterator = '';
            $totalItems = 0;
            do {
                $res = $this->connection->sscan($table, $iterator, null, 1000);
                $res = $res ?: [];
                foreach ($res as $member) {
                    $item = [
                        'member' => $member,
                        'length' => strlen($member),
                    ];
                    if ($this->applyFilter($item, $filter)) {
                        $totalItems++;
                    }
                }
            } while ($iterator !== 0);
            return $totalItems;
        }
        return 0;
    }

    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $items = [];
        if ($type == RedisDriver::TYPE_HASH) {
            foreach ($filter as $filterParts) {
                if (isset($filterParts['key'][Column::OPERATOR_EQUAL])) {
                    $items = [];
                    $res = $this->connection->hget($table, $filterParts['key'][Column::OPERATOR_EQUAL]);
                    if ($res) {
                        $item = [
                            'key' => $filterParts['key'][Column::OPERATOR_EQUAL],
                            'length' => strlen($res),
                            'value' => $res,
                        ];
                        if ($this->applyFilter($item, $filter)) {
                            $items[$item['key']] = $item;
                        }
                    }
                    return $items;
                }
            }

            $iterator = '';
            do {
                $pattern = null; // TODO create pattern based on filter contains / starts / ends
                $res = $this->connection->hscan($table, $iterator, $pattern, $onPage * 10);
                $res = $res ?: [];
                foreach ($res as $key => $value) {
                    $item = [
                        'key' => $key,
                        'length' => strlen($value),
                        'value' => $value,
                    ];
                    if ($this->applyFilter($item, $filter)) {
                        $items[$key] = $item;
                        if (count($items) === $onPage) {
                            break;
                        }
                    }
                }
            } while ($iterator !== 0 && count($items) < $onPage);
        } elseif ($type == RedisDriver::TYPE_KEY) {
            $value = $this->connection->get($table);
            $items[$table] = [
                'key' => $table,
                'length' => strlen($value),
                'value' => $value,
            ];
        } elseif ($type == RedisDriver::TYPE_SET) {
            $iterator = '';
            do {
                $pattern = null; // TODO create pattern based on filter contains / starts / ends
                $res = $this->connection->sscan($table, $iterator, $pattern, $onPage * 10);
                $res = $res ?: [];
                foreach ($res as $member) {
                    $item = [
                        'member' => $member,
                        'length' => strlen($member),
                    ];
                    if ($this->applyFilter($item, $filter)) {
                        $items[$member] = $item;
                        if (count($items) === $onPage) {
                            break;
                        }
                    }
                }
            } while ($iterator !== 0 && count($items) < $onPage);
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
        if ($type == RedisDriver::TYPE_HASH) {
            return $this->connection->hdel($table, $item);
        }
        if ($type == RedisDriver::TYPE_KEY) {
            return $this->connection->del($table);
        }
        if ($type == RedisDriver::TYPE_SET) {
            return $this->connection->srem($table, $item);
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
//            print_R($rows);
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

    private function applyFilter($item, array $filter)
    {
        foreach ($filter as $filterPart) {
            foreach ($filterPart as $key => $filterSettings) {
                foreach ($filterSettings as $operator => $value) {
                    if (!$this->checkFilter($operator, $item[$key], $value)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function checkFilter($operator, $actualValue, $expectedValue)
    {
        if ($operator === Column::OPERATOR_EQUAL) {
            return $actualValue == $expectedValue;
        } elseif ($operator === Column::OPERATOR_GREATER_THAN) {
            return $actualValue > $expectedValue;
        } elseif ($operator === Column::OPERATOR_GREATER_THAN_OR_EQUAL) {
            return $actualValue >= $expectedValue;
        } elseif ($operator === Column::OPERATOR_LESS_THAN) {
            return $actualValue < $expectedValue;
        } elseif ($operator === Column::OPERATOR_LESS_THAN_OR_EQUAL) {
            return $actualValue <= $expectedValue;
        } elseif ($operator === Column::OPERATOR_NOT_EQUAL) {
            return $actualValue !== $expectedValue;
        } elseif ($operator === Column::OPERATOR_CONTAINS) {
            return strpos($actualValue, $expectedValue) !== false;
        } elseif ($operator === Column::OPERATOR_NOT_CONTAINS) {
            return strpos($actualValue, $expectedValue) === false;
        } elseif ($operator === Column::OPERATOR_STARTS_WITH) {
            return strpos($actualValue, $expectedValue) === 0;
        } elseif ($operator === Column::OPERATOR_ENDS_WITH) {
            return substr($actualValue, -strlen($expectedValue)) === $expectedValue;
        } elseif ($operator === Column::OPERATOR_IS_NULL) {
            return $actualValue === null;
        } elseif ($operator === Column::OPERATOR_IS_NOT_NULL) {
            return $actualValue !== null;
        } elseif ($operator === Column::OPERATOR_IS_IN) {
            $expectedValues = explode(',', $expectedValue);
            return in_array($actualValue, $expectedValues);
        } elseif ($operator === Column::OPERATOR_IS_NOT_IN) {
            $expectedValues = explode(',', $expectedValue);
            return !in_array($actualValue, $expectedValues);
        }
        return false;
    }
}
