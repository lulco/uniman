<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\DataManagerInterface;
use Adminerng\Core\Helper\Formatter;
use Adminerng\Core\Multisort;
use InvalidArgumentException;
use PDO;

class MySqlDataManager implements DataManagerInterface
{
    private $columns = null;

    private $connection;

    private $formatter;

    private $database;

    /**
     * cache
     * @var array|null
     */
    private $databases = null;

    public function __construct(PDO $connection, Formatter $formatter)
    {
        $this->connection = $connection;
        $this->formatter = $formatter;
    }

    public function databases(array $sorting = [])
    {
        if ($this->databases !== null) {
            return $this->databases;
        }

        $query = 'SELECT information_schema.SCHEMATA.*, count(*) AS tables_count, SUM(information_schema.TABLES.DATA_LENGTH) AS size
FROM information_schema.SCHEMATA
LEFT JOIN information_schema.TABLES ON information_schema.SCHEMATA.SCHEMA_NAME = information_schema.TABLES.TABLE_SCHEMA
GROUP BY information_schema.TABLES.TABLE_SCHEMA ORDER BY information_schema.SCHEMATA.SCHEMA_NAME';

        $databases = [];
        foreach ($this->connection->query($query)->fetchAll(PDO::FETCH_ASSOC) as $database) {
            $databases[$database['SCHEMA_NAME']] = [
                'database' => $database['SCHEMA_NAME'],
                'charset' => $database['DEFAULT_CHARACTER_SET_NAME'],
                'collation' => $database['DEFAULT_COLLATION_NAME'],
                'tables_count' => $database['tables_count'],
                'size' => $database['size'] ?: 0,
            ];
        }
        $this->databases = $databases;
        return Multisort::sort($databases, $sorting);
    }

    public function tables($database, array $sorting = [])
    {
        $tables = [
            MySqlDriver::TYPE_TABLE => [],
            MySqlDriver::TYPE_VIEW => [],
        ];
        $type = 'BASE TABLE';
        if ($this->database == 'information_schema') {
            $type = 'SYSTEM VIEW';
        }

        foreach ($this->connection->query("SELECT * FROM information_schema.TABLES WHERE TABLE_TYPE = '$type' AND TABLE_SCHEMA = '{$this->database}' ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_ASSOC) as $table) {
            $tables[MySqlDriver::TYPE_TABLE][$table['TABLE_NAME']] = [
                'table' => $table['TABLE_NAME'],
                'engine' => $table['ENGINE'],
                'collation' => $table['TABLE_COLLATION'],
                'data_length' => $table['DATA_LENGTH'],
                'index_length' => $table['INDEX_LENGTH'],
                'data_free' => $table['DATA_FREE'],
                'autoincrement' => $table['AUTO_INCREMENT'],
                'rows' => $table['TABLE_ROWS'],
            ];
        }

        foreach ($this->connection->query("SELECT * FROM information_schema.VIEWS WHERE TABLE_SCHEMA = '{$this->database}' ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_ASSOC) as $view) {
            $tables[MySqlDriver::TYPE_VIEW][$view['TABLE_NAME']] = [
                'view' => $view['TABLE_NAME'],
                'check_option' => $view['CHECK_OPTION'],
                'is_updatable' => $view['IS_UPDATABLE'],
                'definer' => $view['DEFINER'],
                'security_type' => $view['SECURITY_TYPE'],
                'character_set' => $view['CHARACTER_SET_CLIENT'],
                'collation' => $view['COLLATION_CONNECTION'],
            ];
        }
        return [
            MySqlDriver::TYPE_TABLE => Multisort::sort($tables[MySqlDriver::TYPE_TABLE], $sorting),
            MySqlDriver::TYPE_VIEW => Multisort::sort($tables[MySqlDriver::TYPE_VIEW], $sorting),
        ];
    }

    public function itemsCount($database, $type, $table, array $filter = [])
    {
        $query = 'SELECT count(*) FROM `' . $table . '`';
        return $this->connection->query($query)->fetch(PDO::FETCH_COLUMN);
    }

    public function items($database, $type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $primaryColumns = $this->getPrimaryColumns($type, $table);
        $query = 'SELECT * FROM `' . $table . '`';
        $query .= $this->createOrderBy($sorting);
        $query .= ' LIMIT ' . (($page - 1) * $onPage) . ', ' . $onPage;
        $items = [];
        foreach ($this->connection->query($query)->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $pk = [];
            foreach ($primaryColumns as $primaryColumn) {
                $pk[] = $item[$primaryColumn];
            }
            $items[md5(implode('|', $pk))] = $item;
        }
        return $items;
    }

    private function createOrderBy($sorting)
    {
        if (empty($sorting)) {
            return '';
        }
        $orderBy = ' ORDER BY ';
        $order = [];
        foreach ($sorting as $sort) {
            foreach ($sort as $key => $direction) {
                $direction = strtolower($direction) == 'asc' ? 'ASC' : 'DESC';
                $order[] = "`$key` $direction";
            }
        }
        $orderBy .= implode(', ', $order);
        return $orderBy;
    }

    /**
     * TODO create abstract data manager
     * Implement this method if permission canDeleteItem is true
     * @param string $database
     * @param string $type
     * @param string $table
     * @param string $item
     * @return boolean
     */
//    public function deleteItem($database, $type, $table, $item)
//    {
//        return false;
//    }

    public function loadItem($type, $table, $item)
    {
        $primaryColumns = $this->getPrimaryColumns($type, $table);
        $query = 'SELECT * FROM `' . $table . '` WHERE md5(concat(' . implode(', "|", ', $primaryColumns) . ')) = "' . $item . '"';
        return $this->connection->query($query)->fetch(PDO::FETCH_ASSOC);
    }


    public function deleteItem($database, $type, $table, $item)
    {
        $primaryColumns = $this->getPrimaryColumns($type, $table);

        $query = 'DELETE FROM `' . $table . '` WHERE md5(concat(' . implode(', "|", ', $primaryColumns) . ')) = "' . $item . '"';
        return $this->connection->query($query);
    }

    public function deleteTable($database, $type, $table)
    {
        if ($type === MySqlDriver::TYPE_TABLE) {
            $query = 'DROP TABLE `' . $table . '`';
        } elseif ($type === MySqlDriver::TYPE_VIEW) {
            $query = 'DROP VIEW `' . $table . '`';
        } else {
            throw new InvalidArgumentException('Type "' . $type . '" is not supported');
        }
        return $this->connection->query($query);
    }

    public function selectDatabase($database)
    {
        $this->database = $database;
        $this->connection->query('USE `' . $database . '`');
    }

    public function getPrimaryColumns($type, $table)
    {
        $primaryColumns = [];
        $columns = [];
        foreach ($this->getColumns($type, $table) as $column) {
            $columns[] = $column['Field'];
            if ($column['Key'] == 'PRI') {
                $primaryColumns[] = $column['Field'];
            }
        }
        if (empty($primaryColumns)) {
            $primaryColumns = $columns;
        }
        return $primaryColumns;
    }

    public function getColumns($type, $table)
    {
        if ($this->columns === null) {
            $this->columns = $this->connection->query('SHOW FULL COLUMNS FROM `' . $table .'`')->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->columns;
    }
}
