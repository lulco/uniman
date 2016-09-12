<?php

namespace Adminerng\Core;

interface DataManagerInterface
{
    /**
     * list of databases, vhosts, etc.
     * @return array
     */
    public function databases();

    /**
     * list of item storages: tables, views, hashes, sets, queues etc.
     * @param string $database
     * @return array
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
     */
    public function deleteItem($database, $type, $table, $item);
}
