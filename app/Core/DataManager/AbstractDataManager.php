<?php

namespace Adminerng\Core\DataManager;

abstract class AbstractDataManager implements DataManagerInterface
{
    /**
     * Implement this method if permission canDeleteItem is true
     * @param string $type
     * @param string $table
     * @param string $item
     * @return boolean|null
     */
    public function deleteItem($type, $table, $item)
    {
        return null;
    }

    /**
     * Implement this method if permission canDeleteTable is true
     * @param string $type
     * @param string $table
     * @return boolean|null
     */
    public function deleteTable($type, $table)
    {
        return null;
    }
}
