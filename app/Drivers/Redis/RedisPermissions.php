<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\Permissions\PermissionsInterface;

class RedisPermissions implements PermissionsInterface
{
    public function canCreateItem($database, $type, $table)
    {
        return true;
    }

    public function canEditItem($database, $type, $table, $item)
    {
        return true;
    }
}
