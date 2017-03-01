<?php

namespace UniMan\Core\Forms;

use UniMan\Core\Forms\DatabaseForm\DatabaseFormInterface;
use UniMan\Core\Forms\FilterForm\FilterFormInterface;
use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use UniMan\Core\Forms\TableForm\TableFormInterface;
use Nette\Localization\ITranslator;

interface FormManagerInterface
{
    /**
     * create / edit database form
     * @param string|null $database
     * @return DatabaseFormInterface
     */
    public function databaseForm($database);

    /**
     * create / edit table form
     * @param string $database
     * @param string $type
     * @param string|null $table
     * @return TableFormInterface
     */
    public function tableForm($database, $type, $table);

    /**
     * create / edit item form
     * @param string $database
     * @param string $type
     * @param string $table
     * @param mixed|null $item is null if create item form is rendered
     * @return ItemFormInterface
     */
    public function itemForm($database, $type, $table, $item);

    /**
     * filter form
     * @param ITranslator $translator
     * @param array $columns
     * @param array $filter
     * @param array $sorting
     * @param integer $onPage
     * @return FilterFormInterface
     */
    public function filterForm(ITranslator $translator, array $columns, array $filter, array $sorting, $onPage);
}
