<?php

namespace Adminerng\Core;

interface DataManagerInterface
{
    /**
     * list of databases, vhosts, etc.
     * @return array keys are used as database names for databases dropdown, values are databases informations (size, number of tables etc)
     */
    public function databases(array $sorting = []);

    /**
     * @param string $database
     */
    public function selectDatabase($database);

    /**
     * list of item storages: tables, views, hashes, sets, queues etc.
     * @param string $database
     * @return array list of tables grouped by table type (table, view, queue)
     */
    public function tables($database, array $sorting = []);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @param array $filter
     * @return int total number of items
     */
    public function itemsCount($database, $type, $table, array $filter = []);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @param int $page
     * @param int $onPage
     * @param array $filter
     * @param array $sorting
     * @return array list of items
     */
    public function items($database, $type, $table, $page, $onPage, array $filter = [], array $sorting = []);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @param string $item
     * @return boolean|null true if delete was successfull, false if not, null if delete item is not allowed
     */
    public function deleteItem($database, $type, $table, $item);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @return boolean|null true if delete was successfull, false if not, null if delete table is not allowed
     */
    public function deleteTable($database, $type, $table);
}
