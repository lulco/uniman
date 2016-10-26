<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\Permissions\PermissionsInterface;

class MySqlPermissions implements PermissionsInterface
{
    public function canCreateItem($database, $type, $table)
    {
        return $database !== 'information_schema';
    }

    public function canEditItem($database, $type, $table, $item = null)
    {
        return $database !== 'information_schema';
    }

    public function canDeleteItem($database, $type, $table, $item)
    {
        return $database !== 'information_schema';
    }

    public function canCreateTable($database, $type)
    {
        return false;
    }

    public function canEditTable($database, $type, $table)
    {
        return false;
    }

    public function canDeleteTable($database, $type, $table)
    {
        return $database !== 'information_schema';
    }

    public function canCreateDatabase()
    {
        return true;
    }

    public function canEditDatabase($database)
    {
        return true;
    }

    public function canDeleteDatabase($database)
    {
        return $database !== 'information_schema';
    }
}
