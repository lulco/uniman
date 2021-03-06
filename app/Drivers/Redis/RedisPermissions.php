<?php

namespace UniMan\Drivers\Redis;

use UniMan\Core\Permissions\DefaultPermissions;

class RedisPermissions extends DefaultPermissions
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

    public function canCreateTable($database, $type)
    {
        return true;
    }

    public function canEditTable($database, $type, $table)
    {
        return $type !== RedisDriver::TYPE_KEY;
    }

    public function canDeleteTable($database, $type, $table)
    {
        return $type !== RedisDriver::TYPE_KEY;
    }

    public function canEditDatabase($database)
    {
        return true;
    }

    public function canExecuteCommands()
    {
        return true;
    }
}
