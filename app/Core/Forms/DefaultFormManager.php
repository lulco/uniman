<?php

namespace Adminerng\Core\Forms;

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
}
