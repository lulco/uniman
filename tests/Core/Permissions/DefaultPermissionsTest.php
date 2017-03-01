<?php

namespace UniMan\Tests\Core\Permissions;

use UniMan\Core\Permissions\DefaultPermissions;
use PHPUnit_Framework_TestCase;

class DefaultPermissionsTest extends PHPUnit_Framework_TestCase
{
    public function testPermissions()
    {
        $permissions = new DefaultPermissions();

        self::assertFalse($permissions->canCreateDatabase());
        self::assertFalse($permissions->canEditDatabase('database_name'));
        self::assertFalse($permissions->canDeleteDatabase('database_name'));

        self::assertFalse($permissions->canCreateTable('database_name', 'type_name'));
        self::assertFalse($permissions->canEditTable('database_name', 'type_name', 'table_name'));
        self::assertFalse($permissions->canDeleteTable('database_name', 'type_name', 'table_name'));

        self::assertFalse($permissions->canCreateItem('database_name', 'type_name', 'table_name'));
        self::assertFalse($permissions->canEditItem('database_name', 'type_name', 'table_name', 'item_id'));
        self::assertFalse($permissions->canDeleteItem('database_name', 'type_name', 'table_name', 'item_id'));

        self::assertFalse($permissions->canExecuteCommands());
    }
}
