<?php

namespace Adminerng\Core\Permissions;

interface PermissionsInterface
{
    public function canCreateItem($database, $type, $table);

    public function canEditItem($database, $type, $table, $item);
}
