<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Permissions\PermissionsInterface;

class RedisPermissions implements PermissionsInterface
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
        return true;
    }

    public function canDeleteTable($database, $type, $table)
    {
        return true;
    }
}
