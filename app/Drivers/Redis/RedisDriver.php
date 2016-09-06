<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\AbstractDriver;
use Adminerng\Drivers\Redis\Forms\RedisHashKeyItemForm;
use Adminerng\Drivers\Redis\Forms\RedisKeyItemForm;
use RedisProxy\RedisProxy;

class RedisDriver extends AbstractDriver
{
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

    public function databases()
    {
        $numberOfDatabases = $this->connection->config('get', 'databases')['databases'];
        $keyspace = $this->connection->info('keyspace');
        $databases = [];
        for ($i = 0; $i < $numberOfDatabases; ++$i) {
            $databases[$i] = $this->databaseInfo($keyspace, $i);
        }
        return $databases;
    }

    private function selectDatabase($database)
    {
        $this->connection->select($database);
    }
    
    public function tablesHeaders()
    {
        return [
            'Hashes' => ['Hash', 'Number of fields'],
            'Keys' => ['Key', 'Value', 'Length'],
            'Sets' => ['Set', 'Number of members']
        ];
    }

    public function tables($database)
    {
        $this->selectDatabase($database);
        $tables = [];
        $commands = [
            'get' => 'Keys',
            'hLen' => 'Hashes',
            'sMembers' => 'Sets',
        ];
        foreach ($this->connection->keys('*') as $key) {
            foreach ($commands as $command => $label) {
                $result = $this->connection->$command($key);        
                if ($this->connection->getLastError() === null) {
                    if ($label == 'Sets') {
                        $tables[$label][$key] = [count($result)];
                    } else {
                        $tables[$label][$key] = [
                            $result
                        ];
                    }
                    if ($label == 'Keys') {
                        $tables[$label][$key][] = strlen($result);
                    }
                    break;
                }
                $this->connection->clearLastError();
            }
            ksort($tables[$label]);
        }
        ksort($tables);
        return $tables;
    }

    public function itemsTitles()
    {
        return [
            'Keys' => 'Key',
            'Hashes' => 'Keys',
            'Sets' => 'Members',
        ];
    }

    public function itemsHeaders()
    {
        return [
            'Keys' => [
                'Key', 'Length', 'Value'
            ],
            'Hashes' => [
                'Key', 'Length', 'Value'
            ],
            'Sets' => [
                'Member', 'Length'
            ]
        ];
    }

    public function itemsCount($database, $type, $table)
    {
        $this->selectDatabase($database);
        if ($type == 'Hashes') {
            return $this->connection->hLen($table);
        }
        if ($type == 'Keys') {
            return 1;
        }
        if ($type == 'Sets') {
            return $this->connection->sCard($table);
        }
        return 0;
    }

    public function items($database, $type, $table, $page, $onPage)
    {
        $items = [];
        $this->selectDatabase($database);
        if ($type == 'Hashes') {
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
        } elseif ($type == 'Keys') {
            $value = $this->connection->get($table);
            $items[$table] = [
                'key' => $table,
                'length' => strlen($value),
                'value' => $value,
            ];
        } elseif ($type == 'Sets') {
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
    
    public function getCredentialsForm()
    {
        return new RedisForm();
    }
    
    private function databaseInfo($keyspace, $db)
    {
        $info = [
            'keys' => 0,
            'expires' => '-',
            'avg_ttl' => '-',
        ];
        if (isset($keyspace['db' . $db])) {
            $dbKeyspace = explode(',', $keyspace['db' . $db]);
            $info = [
                'keys' => explode('=', $dbKeyspace[0])[1],
                'expires' => explode('=', $dbKeyspace[1])[1],
                'avg_ttl' => explode('=', $dbKeyspace[2])[1],
            ];
        }
        return $info;
    }
    
    public function itemForm($database, $type, $table, $item)
    {
        $this->selectDatabase($database);
        if ($type == 'Hashes') {
            return new RedisHashKeyItemForm($this->connection, $table, $item);
        } elseif ($type == 'Keys') {
            return new RedisKeyItemForm($this->connection, $item);
        }
    }

    protected function getPermissions()
    {
        return new RedisPermissions();
    }
}
