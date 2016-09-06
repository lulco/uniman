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
}
