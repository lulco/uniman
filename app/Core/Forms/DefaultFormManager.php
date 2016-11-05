<?php

namespace Adminerng\Core\Forms;

use Adminerng\Core\Forms\FilterForm\FilterForm;

class DefaultFormManager implements FormManagerInterface
{
    public function itemForm($database, $type, $table, $item)
    {
        return false;
    }

    public function tableForm($database, $type, $table)
    {
        return false;
    }

    public function databaseForm($database)
    {
        return false;
    }

    public function filterForm(array $columns, array $filter, array $sorting, $onPage)
    {
        return new FilterForm($columns, $filter, $sorting, $onPage);
    }
}
