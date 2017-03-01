<?php

namespace UniMan\Core\ListingHeaders;

use UniMan\Core\Column;

interface HeaderManagerInterface
{
    /**
     * @return Column[]
     */
    public function databasesHeaders();

    /**
     * @return array list of Column[] for each type supported by driver
     */
    public function tablesHeaders();

    /**
     * @return Column[]
     */
    public function itemsHeaders($type, $table);
}
