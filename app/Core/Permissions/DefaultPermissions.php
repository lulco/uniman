<?php

namespace Adminerng\Core\Permissions;

class DefaultPermissions implements PermissionsInterface
{
    public function canCreateItem($database, $type, $table)
    {
        return false;
    }

    public function canEditItem($database, $type, $table, $item = null)
    {
        return false;
    }

    public function canDeleteItem($database, $type, $table, $item)
    {
        return false;
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
        return false;
    }

    public function canCreateDatabase()
    {
        return false;
    }

    public function canEditDatabase($database)
    {
        return false;
    }

    public function canDeleteDatabase($database)
    {
        return false;
    }

    public function canExecuteCommands()
    {
        return false;
    }
}
