<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\Permissions\PermissionsInterface;

class MemcachePermissions implements PermissionsInterface
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
        return true;
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
}
