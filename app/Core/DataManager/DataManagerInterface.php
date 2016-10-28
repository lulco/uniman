<?php

namespace Adminerng\Core\DataManager;

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
     * @param array $sorting
     * @return array list of tables grouped by table type (table, view, queue), one array for each driver supported type
     */
    public function tables(array $sorting = []);

    /**
     * @param string $type
     * @param string $table
     * @param array $filter
     * @return int total number of items
     */
    public function itemsCount($type, $table, array $filter = []);

    /**
     * @param string $type
     * @param string $table
     * @param int $page
     * @param int $onPage
     * @param array $filter
     * @param array $sorting
     * @return array list of items
     */
    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = []);

    /**
     * @param string $type
     * @param string $table
     * @param string $item
     * @return boolean|null true if delete was successfull, false if not, null if delete item is not allowed
     */
    public function deleteItem($type, $table, $item);

    /**
     * @param string $type
     * @param string $table
     * @return boolean|null true if delete was successfull, false if not, null if delete table is not allowed
     */
    public function deleteTable($type, $table);

    /**
     * @param string $database
     * @return boolean|null true if delete was successfull, false if not, null if delete table is not allowed
     */
    public function deleteDatabase($database);

    /**
     * @param string $commands
     * @return array|null array contains keys:
     * - boolean command_result
     * - array items
     * - integer items_count
     * - float execution_time
     * for each command or null if commands execution is not allowed
     */
    public function execute($commands);
}
