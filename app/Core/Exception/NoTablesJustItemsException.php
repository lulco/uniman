<?php

namespace UniMan\Core\Exception;

class NoTablesJustItemsException extends UniManException
{
    private $type;

    private $table;

    public function __construct($type, $table)
    {
        $this->type = $type;
        $this->table = $table;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTable()
    {
        return $this->table;
    }
}
