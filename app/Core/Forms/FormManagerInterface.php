<?php

namespace Adminerng\Core\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Adminerng\Core\Forms\TableForm\TableFormInterface;

interface FormManagerInterface
{
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
}
