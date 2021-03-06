<?php

namespace UniMan\Drivers\MySql;

use UniMan\Core\DataManager\AbstractDataManager;
use UniMan\Core\Exception\OperatorNotSupportedException;
use UniMan\Core\Utils\Filter;
use UniMan\Core\Utils\Multisort;
use InvalidArgumentException;
use Nette\Utils\Strings;
use PDO;

class MySqlDataManager extends AbstractDataManager
{
    private $columns = null;

    private $connection;

    private $database;

    /**
     * cache
     * @var array|null
     */
    private $databases = null;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function databases(array $sorting = [])
    {
        if ($this->databases !== null) {
            return $this->databases;
        }

        $query = 'SELECT information_schema.SCHEMATA.*, count(information_schema.TABLES.TABLE_NAME) AS tables_count, SUM(information_schema.TABLES.DATA_LENGTH) AS size
FROM information_schema.SCHEMATA
LEFT JOIN information_schema.TABLES ON information_schema.SCHEMATA.SCHEMA_NAME = information_schema.TABLES.TABLE_SCHEMA
GROUP BY information_schema.SCHEMATA.SCHEMA_NAME
ORDER BY information_schema.SCHEMATA.SCHEMA_NAME';

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

    protected function getDatabaseNameColumn()
    {
        return 'database';
    }

    public function tables(array $sorting = [])
    {
        $tables = [
            MySqlDriver::TYPE_TABLE => [],
            MySqlDriver::TYPE_VIEW => [],
        ];
        $type = 'BASE TABLE';
        if ($this->database === 'information_schema') {
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

    public function itemsCount($type, $table, array $filter = [])
    {
        $query = sprintf('SELECT count(*) FROM `%s`', $table) . $this->createWhere($filter);
        return $this->connection->query($query)->fetch(PDO::FETCH_COLUMN);
    }

    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        $primaryColumns = $this->getPrimaryColumns($type, $table);
        $query = sprintf('SELECT * FROM `%s`', $table);
        $query .= $this->createWhere($filter);
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

    private function createWhere(array $filter)
    {
        if (empty($filter)) {
            return '';
        }

        $operatorsMap = [
            Filter::OPERATOR_EQUAL => '= "%s"',
            Filter::OPERATOR_GREATER_THAN => '> "%s"',
            Filter::OPERATOR_GREATER_THAN_OR_EQUAL => '>= "%s"',
            Filter::OPERATOR_LESS_THAN => '< "%s"',
            Filter::OPERATOR_LESS_THAN_OR_EQUAL => '<= "%s"',
            Filter::OPERATOR_NOT_EQUAL => '!= "%s"',
            Filter::OPERATOR_CONTAINS => 'LIKE "%%%s%%"',
            Filter::OPERATOR_NOT_CONTAINS => 'NOT LIKE "%%%s%%"',
            Filter::OPERATOR_STARTS_WITH => 'LIKE "%s%%"',
            Filter::OPERATOR_ENDS_WITH => 'LIKE "%%%s"',
            Filter::OPERATOR_IS_NULL => 'IS NULL',
            Filter::OPERATOR_IS_NOT_NULL => 'IS NOT NULL',
            Filter::OPERATOR_IS_IN => 'IN (%s)',
            Filter::OPERATOR_IS_NOT_IN => 'NOT IN (%s)',
        ];

        $where = ' WHERE ';
        $whereParts = [];
        foreach ($filter as $filterPart) {
            foreach ($filterPart as $key => $filterSettings) {
                foreach ($filterSettings as $operator => $value) {
                    if (!isset($operatorsMap[$operator])) {
                        throw new OperatorNotSupportedException('Operator "' . $operator . '" is not supported.');
                    }
                    if ($operator === Filter::OPERATOR_IS_IN || $operator === Filter::OPERATOR_IS_NOT_IN) {
                        $value = implode(', ', array_map(function ($item) {
                            return '"' . $item . '"';
                        }, explode(',', $value)));
                    }
                    $whereParts[] = "`$key` " . sprintf($operatorsMap[$operator], $value);
                }
            }
        }
        $where .= implode(' AND ', $whereParts);
        return $where;
    }

    private function createOrderBy(array $sorting)
    {
        if (empty($sorting)) {
            return '';
        }
        $orderBy = ' ORDER BY ';
        $order = [];
        foreach ($sorting as $sort) {
            foreach ($sort as $key => $direction) {
                $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
                $order[] = "`$key` $direction";
            }
        }
        $orderBy .= implode(', ', $order);
        return $orderBy;
    }

    public function loadItem($type, $table, $item)
    {
        $primaryColumns = $this->getPrimaryColumns($type, $table);
        $query = sprintf('SELECT * FROM `%s` WHERE md5(concat(%s)) = "%s"', $table, implode(', "|", ', $primaryColumns), $item);
        return $this->connection->query($query)->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteItem($type, $table, $item)
    {
        $primaryColumns = $this->getPrimaryColumns($type, $table);
        $query = sprintf('DELETE FROM `%s` WHERE md5(concat(%s)) = "%s"', $table, implode(', "|", ', $primaryColumns), $item);
        return $this->connection->query($query);
    }

    public function deleteTable($type, $table)
    {
        if ($type === MySqlDriver::TYPE_TABLE) {
            $query = sprintf('DROP TABLE `%s`', $table);
        } elseif ($type === MySqlDriver::TYPE_VIEW) {
            $query = sprintf('DROP VIEW `%s`', $table);
        } else {
            throw new InvalidArgumentException('Type "' . $type . '" is not supported');
        }
        return $this->connection->query($query);
    }

    public function deleteDatabase($database)
    {
        $query = sprintf('DROP DATABASE `%s`', $database);
        return $this->connection->query($query);
    }

    public function selectDatabase($database)
    {
        $this->database = $database;
        $query = sprintf('USE `%s`', $database);
        $this->connection->query($query);
    }

    public function getPrimaryColumns($type, $table)
    {
        $primaryColumns = [];
        $columns = [];
        foreach ($this->getColumns($type, $table) as $column) {
            $columns[] = $column['Field'];
            if ($column['Key'] === 'PRI') {
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
            $columns = $this->connection->query(sprintf('SHOW FULL COLUMNS FROM `%s`', $table))->fetchAll(PDO::FETCH_ASSOC);
            $keys = [];
            foreach ($this->connection->query(sprintf('SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = "%s" AND TABLE_NAME = "%s"', $this->database, $table))->fetchAll(PDO::FETCH_ASSOC) as $key) {
                $keys[$key['COLUMN_NAME']] = $key;
            }
            $this->columns = [];
            foreach ($columns as $column) {
                $column['key_info'] = isset($keys[$column['Field']]) ? $keys[$column['Field']] : [];
                $this->columns[$column['Field']] = $column;
            }
        }
        return $this->columns;
    }

    public function execute($commands)
    {
        $queries = array_filter(array_map('trim', explode(';', $commands)), function ($query) {
            return $query;
        });
        $results = [];
        foreach ($queries as $query) {
            $results[$query]['headers'] = [];
            $statement = $this->connection->query($query);
            if (Strings::startsWith(strtolower($query), 'select ') || Strings::startsWith(strtolower($query), 'show ')) {
                $res = $statement->fetchAll(PDO::FETCH_ASSOC);
                $results[$query]['headers'] = array_keys(current($res));
                $results[$query]['items'] = $res;
                continue;
            }
            $results[$query] = (bool) $statement;
        }
        return $results;
    }
}
