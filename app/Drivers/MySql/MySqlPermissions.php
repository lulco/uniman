<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\Permissions\PermissionsInterface;

class MySqlPermissions implements PermissionsInterface
{
    public function canCreateItem($database, $type, $table)
    {
        return true;
    }

    public function canEditItem($database, $type, $table, $item = null)
    {
        return true;
    }

    public function canDeleteItem($database, $type, $table, $item)
    {
        return true;
    }
}
