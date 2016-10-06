<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\DataManagerInterface;
use InvalidArgumentException;
use PDO;

class MySqlDataManager implements DataManagerInterface
{
    private $columns = null;

    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function databases()
    {
        $tableSchemas = [];
        foreach ($this->connection->query('SELECT TABLE_SCHEMA, count(*) AS tables_count, SUM(DATA_LENGTH) AS size FROM information_schema.TABLES GROUP BY TABLE_SCHEMA')->fetchAll(PDO::FETCH_ASSOC) as $tableSchema) {
            $tableSchemas[$tableSchema['TABLE_SCHEMA']] = [
                'tables_count' => $tableSchema['tables_count'],
                'size' => $tableSchema['size'],
            ];
        }
        $databases = [];
        foreach ($this->connection->query('SELECT * FROM information_schema.SCHEMATA')->fetchAll(PDO::FETCH_ASSOC) as $database) {
            $databases[$database['SCHEMA_NAME']] = [
                'charset' => $database['DEFAULT_CHARACTER_SET_NAME'],
                'collation' => $database['DEFAULT_COLLATION_NAME'],
                'tables_count' => isset($tableSchemas[$database['SCHEMA_NAME']]['tables_count']) ? $tableSchemas[$database['SCHEMA_NAME']]['tables_count'] : '0',
                'size' => isset($tableSchemas[$database['SCHEMA_NAME']]['size']) ? $tableSchemas[$database['SCHEMA_NAME']]['size'] : '0',
            ];
        }
        return $databases;
    }

    public function tables($database)
    {
        $tables = [
            MySqlDriver::TYPE_TABLE => [],
            MySqlDriver::TYPE_VIEW => [],
        ];
        $type = 'BASE TABLE';
        if ($database == 'information_schema') {
            $type = 'SYSTEM VIEW';
        }
        foreach ($this->connection->query("SELECT * FROM information_schema.TABLES WHERE TABLE_TYPE = '$type' AND TABLE_SCHEMA = '$database' ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_ASSOC) as $table) {
            $tables[MySqlDriver::TYPE_TABLE][$table['TABLE_NAME']] = [
                $table['ENGINE'],
                $table['TABLE_COLLATION'],
                number_format($table['DATA_LENGTH'], 0),
                $table['INDEX_LENGTH'],
                number_format($table['DATA_FREE'], 0, ',', ' '),
                $table['AUTO_INCREMENT'],
                $table['TABLE_ROWS'],
            ];
        }
        foreach ($this->connection->query("SELECT * FROM information_schema.VIEWS WHERE TABLE_SCHEMA = '$database' ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_ASSOC) as $view) {
            $tables[MySqlDriver::TYPE_VIEW][$view['TABLE_NAME']] = [
                $view['CHECK_OPTION'],
                $view['IS_UPDATABLE'],
                $view['DEFINER'],
                $view['SECURITY_TYPE'],
                $view['CHARACTER_SET_CLIENT'],
                $view['COLLATION_CONNECTION'],
            ];
        }
        return $tables;
    }

    public function itemsCount($database, $type, $table, array $filter = [])
    {
        $this->selectDatabase($database);
        $query = 'SELECT count(*) FROM `' . $table . '`';
        return $this->connection->query($query)->fetch(PDO::FETCH_COLUMN);
    }

    public function items($database, $type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $this->selectDatabase($database);

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

    public function deleteItem($database, $type, $table, $item)
    {
        $this->selectDatabase($database);
        $primaryColumns = $this->getPrimaryColumns($type, $table);

        $query = 'DELETE FROM `' . $table . '` WHERE md5(concat(' . implode(', "|", ', $primaryColumns) . ')) = "' . $item . '"';
        return $this->connection->query($query);
    }

    public function deleteTable($database, $type, $table)
    {
        $this->selectDatabase($database);
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
        $this->connection->query('USE `' . $database . '`');
    }

    private function getPrimaryColumns($type, $table)
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
