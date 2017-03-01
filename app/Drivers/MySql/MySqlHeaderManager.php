<?php

namespace UniMan\Drivers\MySql;

use UniMan\Core\Column;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;

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
        $columns[] = (new Column('database', 'mysql.headers.databases.database'))
            ->setSortable(true);

        $columns[] = (new Column('charset', 'mysql.headers.databases.charset'))
            ->setSortable(true);

        $columns[] = (new Column('collation', 'mysql.headers.databases.collation'))
            ->setSortable(true);

        $columns[] = (new Column('tables_count', 'mysql.headers.databases.tables'))
            ->setSortable(true)
            ->setNumeric(true);

        $columns[] = (new Column('size', 'mysql.headers.databases.size'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setSize(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $tableFields = [
            'table' => [],
            'engine' => [],
            'collation' => [],
            'data_length' => ['is_numeric' => true, 'is_size' => true],
            'index_length' => ['is_numeric' => true, 'is_size' => true],
            'data_free' => ['is_numeric' => true, 'is_size' => true],
            'autoincrement' => ['is_numeric' => true],
            'rows' => ['is_numeric' => true],
        ];
        $tableColumns = [];
        foreach ($tableFields as $key => $settings) {
            $column = (new Column($key, 'mysql.headers.tables.' . $key))
                ->setSortable(true);
            if (isset($settings['is_numeric'])) {
                $column->setNumeric($settings['is_numeric']);
            }
            if (isset($settings['is_size'])) {
                $column->setSize($settings['is_size']);
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
            $column = (new Column($key, 'mysql.headers.views.' . $key))
                ->setSortable(true);
            if (isset($settings['is_numeric'])) {
                $column->setNumeric($settings['is_numeric']);
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
            $col = (new Column($column, $column))
                ->setSortable(true)
                ->setFilterable(true)
                ->setNumeric($this->isNumeric($definition))
                ->setDecimals($this->getDecimals($definition))
                ->setInfo($definition['Comment']);
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
