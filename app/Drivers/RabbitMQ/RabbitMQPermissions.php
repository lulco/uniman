<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\Permissions\DefaultPermissions;

class RabbitMQPermissions extends DefaultPermissions
{
    public function canCreateItem($database, $type, $table)
    {
        return true;
    }

    public function canCreateTable($database, $type)
    {
        return true;
    }

    public function canDeleteTable($database, $type, $table)
    {
        return true;
    }
}
