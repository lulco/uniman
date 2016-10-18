<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\Permissions\PermissionsInterface;

class RabbitMQPermissions implements PermissionsInterface
{
    public function canCreateItem($database, $type, $table)
    {
        return true;
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
        return true;
    }

    public function canEditTable($database, $type, $table)
    {
        return false;
    }

    public function canDeleteTable($database, $type, $table)
    {
        return true;
    }
}
