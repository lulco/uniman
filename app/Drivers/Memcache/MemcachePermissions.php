<?php

namespace UniMan\Drivers\Memcache;

use UniMan\Core\Permissions\DefaultPermissions;

class MemcachePermissions extends DefaultPermissions
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
