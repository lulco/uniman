<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\Forms\DefaultFormManager;
use Adminerng\Drivers\Mysql\Forms\MySqlDatabaseForm;
use Adminerng\Drivers\Mysql\Forms\MySqlItemForm;
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
