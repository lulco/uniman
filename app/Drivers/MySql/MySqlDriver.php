<?php

namespace UniMan\Drivers\MySql;

use UniMan\Core\Driver\AbstractDriver;
use UniMan\Core\Exception\ConnectException;
use UniMan\Drivers\MySql\Forms\MySqlCredentialsForm;
use PDO;
use PDOException;

class MySqlDriver extends AbstractDriver
{
    const TYPE_TABLE = 'table';
    const TYPE_VIEW = 'view';

    private $connection;

    public function extensions()
    {
        return ['pdo_mysql'];
    }

    public function type()
    {
        return 'mysql';
    }

    public function defaultCredentials()
    {
        return [
            'server' => 'localhost:3306',
            'user' => 'root',
            'password' => '',
        ];
    }

    public function getCredentialsForm()
    {
        return new MySqlCredentialsForm();
    }

    public function connect(array $credentials)
    {
        $host = $credentials['server'];
        $port = '3306';
        if (strpos($credentials['server'], ':') !== false) {
            list($host, $port) = explode(':', $credentials['server'], 2);
        }
        $dsn = 'mysql:;host=' . $host . ';port=' . $port . ';charset=utf8';
        try {
            $this->connection = new PDO($dsn, $credentials['user'], $credentials['password']);
            $this->connection->query("SET SESSION sql_mode = ''");
        } catch (PDOException $e) {
            throw new ConnectException($e->getMessage());
        }
    }

    protected function getFormManager()
    {
        return new MySqlFormManager($this->connection, $this->dataManager());
    }

    protected function getHeaderManager()
    {
        return new MySqlHeaderManager($this->dataManager());
    }

    protected function getPermissions()
    {
        return new MySqlPermissions();
    }

    protected function getDataManager()
    {
        return new MySqlDataManager($this->connection);
    }
}
