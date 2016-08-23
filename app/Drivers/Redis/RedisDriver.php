<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\AbstractDriver;
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
    
    public function name()
    {
        return 'Redis';
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
        $this->connection = new RedisProxy($credentials['host'], $credentials['port'], 0);
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

    public function selectDatabase($database)
    {
        $this->connection->select($database);
    }
    
    public function tablesHeaders()
    {
        return [
            'Hashes' => ['Hash', 'Number of fields'],
            'Keys' => ['Key', 'Value', 'Length'],
        ];
    }

    public function tables($database)
    {
        $this->selectDatabase($database);
        $tables = [];
        $commands = [
            'get' => 'Keys',
            'hLen' => 'Hashes',
        ];
        foreach ($this->connection->keys('*') as $key) {
            foreach ($commands as $command => $label) {
                $result = $this->connection->$command($key);        
                if ($this->connection->getLastError() === null) {
                    $tables[$label][$key] = [
                        $result
                    ];
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
        ];
    }
    
    public function items($database, $type, $table)
    {
        $items = [];
        $this->selectDatabase($database);
        if ($type == 'Hashes') {
            foreach ($this->connection->hGetAll($table) as $key => $value) {
                $items[$key] = [
                    'length' => strlen($value),
                    'value' => $value,
                ];
            }
        } elseif ($type == 'Keys') {
            $value = $this->connection->get($table);
            $items[$table] = [
                'length' => strlen($value),
                'value' => $value,
            ];
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
}
