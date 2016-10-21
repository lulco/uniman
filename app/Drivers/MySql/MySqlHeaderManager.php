<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class MySqlHeaderManager implements HeaderManagerInterface
{
    private $dataManager;

    public function __construct(MySqlDataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('database')
            ->setTitle('mysql.headers.databases.database')
            ->setIsSortable(true);

        $columns[] = (new Column())
            ->setKey('charset')
            ->setTitle('mysql.headers.databases.charset')
            ->setIsSortable(true);

        $columns[] = (new Column())
            ->setKey('collation')
            ->setTitle('mysql.headers.databases.collation')
            ->setIsSortable(true);

        $columns[] = (new Column())
            ->setKey('tables_count')
            ->setTitle('mysql.headers.databases.tables')
            ->setIsSortable(true)
            ->setIsNumeric(true);

        $columns[] = (new Column())
            ->setKey('size')
            ->setTitle('mysql.headers.databases.size')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $tableFields = [
            'table' => [],
            'engine' => [],
            'collation' => [],
            'data_length' => ['is_numeric' => true],
            'index_length' => ['is_numeric' => true],
            'data_free' => ['is_numeric' => true],
            'autoincrement' => ['is_numeric' => true],
            'rows' => ['is_numeric' => true],
        ];
        $tableColumns = [];
        foreach ($tableFields as $key => $settings) {
            $column = (new Column())
                ->setKey($key)
                ->setTitle('mysql.headers.tables.' . $key)
                ->setIsSortable(true);
            if (isset($settings['is_numeric'])) {
                $column->setIsNumeric($settings['is_numeric']);
            }
            $tableColumns[] = $column;
        }

        $viewFields = [
            'view' => [],
            'check_option' => [],
            'is_updatable' => [],
            'definer' => [],
            'security_type' => [],
            'character_set' => [],
            'collation' => [],
        ];
        $viewColumns = [];
        foreach ($viewFields as $key => $settings) {
            $column = (new Column())
                ->setKey($key)
                ->setTitle('mysql.headers.views.' . $key)
                ->setIsSortable(true);
            if (isset($settings['is_numeric'])) {
                $column->setIsNumeric($settings['is_numeric']);
            }
            $viewColumns[] = $column;
        }

        return [
            MySqlDriver::TYPE_TABLE => $tableColumns,
            MySqlDriver::TYPE_VIEW => $viewColumns,
        ];
    }

    public function itemsHeaders($type, $table)
    {
        $columns = [];
        foreach ($this->dataManager->getColumns($type, $table) as $column => $definition) {
            $col = (new Column())
                ->setKey($column)
                ->setTitle($column)
                ->setIsSortable(true)
                ->setIsNumeric($this->isNumeric($definition))
                ->setDecimals($this->getDecimals($definition));
            if ($definition['key_info'] && $definition['key_info']['REFERENCED_TABLE_NAME']) {
                $col->setExternal(
                    $definition['key_info']['REFERENCED_TABLE_SCHEMA'],
                    $definition['key_info']['REFERENCED_TABLE_NAME'],
                    function ($value) {
                        return md5($value);
                    }
                );
            }
            $columns[] = $col;
        }
        return $columns;
    }

    private function isNumeric(array $definition)
    {
        if ($definition['key_info']) {
            return false;
        }
        if (strpos($definition['Type'], 'int') !== false) {
            return true;
        }
        if (strpos($definition['Type'], 'float') !== false) {
            return true;
        }
        if (strpos($definition['Type'], 'double') !== false) {
            return true;
        }
        if (strpos($definition['Type'], 'decimal') !== false) {
            return true;
        }
        return false;
    }

    private function getDecimals(array $column)
    {
        if (!$this->isNumeric($column)) {
            return 0;
        }
        if (strpos($column['Type'], ',') === false) {
            return 0;
        }
        $pattern = '/(.*?)\((.*?),(.*?)\)/';
        preg_match($pattern, $column['Type'], $match);
        return isset($match[3]) ? $match[3] : 0;
    }
}
