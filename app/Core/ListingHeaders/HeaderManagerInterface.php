<?php

namespace Adminerng\Core\ListingHeaders;

interface HeaderManagerInterface
{
    /**
     * @return Column[]
     */
    public function databasesHeaders();

    /**
     * @return Columns
     */
    public function tablesHeaders();

    /**
     * @return Column[]
     */
    public function itemsHeaders($type, $table);
}
