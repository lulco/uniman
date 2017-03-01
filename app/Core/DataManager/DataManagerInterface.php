<?php

namespace UniMan\Core\DataManager;

interface DataManagerInterface
{
    /**
     * list of databases, vhosts, etc.
     * @param array $sorting
     * @return array keys are used as database identifiers, values are databases informations (size, number of tables etc)
     */
    public function databases(array $sorting = []);

    /**
     * list of databases, vhost, etc. for DatabaseSelect dropdown and all places where we need just identifier and name
     * @param array $sorting
     * @return array keys are used as option values for dropdown, values are used as text to show
     */
    public function databasesKeyValue(array $sorting = []);

    /**
     * database name
     * @param string $identifier
     * @return string
     */
    public function databaseName($identifier);

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
     * - array headers
     * - array items
     * - integer items_count
     * - float execution_time
     * for each command or null if commands execution is not allowed
     */
    public function execute($commands);

    /**
     * @return array list of messages to show as flash messages
     */
    public function getMessages();
}
