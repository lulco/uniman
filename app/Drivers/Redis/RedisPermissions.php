<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Permissions\DefaultPermissions;

class RedisPermissions extends DefaultPermissions
{
    public function canCreateItem($database, $type, $table)
    {
        return $type !== RedisDriver::TYPE_KEY;
    }

    public function canEditItem($database, $type, $table, $item = null)
    {
        return $type !== RedisDriver::TYPE_KEY;
    }

    public function canDeleteItem($database, $type, $table, $item)
    {
        return $type !== RedisDriver::TYPE_KEY;
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
