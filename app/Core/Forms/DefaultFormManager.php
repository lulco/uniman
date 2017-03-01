<?php

namespace UniMan\Core\Forms;

use UniMan\Core\Forms\FilterForm\FilterForm;
use Nette\Localization\ITranslator;

class DefaultFormManager implements FormManagerInterface
{
    public function itemForm($database, $type, $table, $item)
    {
        return null;
    }

    public function tableForm($database, $type, $table)
    {
        return null;
    }

    public function databaseForm($database)
    {
        return null;
    }

    public function filterForm(ITranslator $translator, array $columns, array $filter, array $sorting, $onPage)
    {
        return new FilterForm($translator, $columns, $filter, $sorting, $onPage);
    }
}
