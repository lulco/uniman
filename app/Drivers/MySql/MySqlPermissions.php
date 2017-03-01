<?php

namespace UniMan\Drivers\MySql;

use UniMan\Core\Permissions\DefaultPermissions;

class MySqlPermissions extends DefaultPermissions
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

    public function canExecuteCommands()
    {
        return true;
    }
}
