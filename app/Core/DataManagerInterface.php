<?php

namespace Adminerng\Core;

interface DataManagerInterface
{
    /**
     * list of databases, vhosts, etc.
     * @return array keys are used as database names for databases dropdown, values are databases informations (size, number of tables etc)
     */
    public function databases();

    /**
     * @todo try to remove this method from data manager (or keep it as private)
     * @param string $database
     */
    public function selectDatabase($database);

    /**
     * list of item storages: tables, views, hashes, sets, queues etc.
     * @param string $database
     * @return array list of tables grouped by table type (table, view, queue)
     */
    public function tables($database);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @return int total number of items
     */
    public function itemsCount($database, $type, $table);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @param int $page
     * @param int $onPage
     * @return array list of items
     */
    public function items($database, $type, $table, $page, $onPage);

    /**
     * @param string $database
     * @param string $type
     * @param string $table
     * @param string $item
     * @return boolean|null true if delete was successfull, false if not, null if delete item is not supported
     */
    public function deleteItem($database, $type, $table, $item);
}
