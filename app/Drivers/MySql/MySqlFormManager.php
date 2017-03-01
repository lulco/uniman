<?php

namespace UniMan\Drivers\MySql;

use UniMan\Core\Forms\DefaultFormManager;
use UniMan\Drivers\Mysql\Forms\MySqlDatabaseForm;
use UniMan\Drivers\Mysql\Forms\MySqlItemForm;
use PDO;

class MySqlFormManager extends DefaultFormManager
{
    private $connection;

    private $dataManager;

    public function __construct(PDO $connection, MySqlDataManager $dataManager)
    {
        $this->connection = $connection;
        $this->dataManager = $dataManager;
    }

    public function itemForm($database, $type, $table, $item)
    {
        return new MySqlItemForm($this->connection, $this->dataManager, $type, $table, $item);
    }

    public function databaseForm($database)
    {
        return new MySqlDatabaseForm($this->connection, $database);
    }
}
