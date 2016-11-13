<?php

namespace Adminerng\Core\ListingHeaders;

use Adminerng\Core\Column;

interface HeaderManagerInterface
{
    /**
     * @return Column[]
     */
    public function databasesHeaders();

    /**
     * @return Column[]
     */
    public function tablesHeaders();

    /**
     * @return Column[]
     */
    public function itemsHeaders($type, $table);
}
