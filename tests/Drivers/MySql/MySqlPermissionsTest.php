<?php

namespace UniMan\Tests\Drivers\MySql;

use UniMan\Drivers\MySql\MySqlPermissions;
use PHPUnit_Framework_TestCase;

class MySqlPermissionsTest extends PHPUnit_Framework_TestCase
{
    public function testPermissions()
    {
        $permissions = new MySqlPermissions();

        self::assertTrue($permissions->canCreateDatabase());
        self::assertTrue($permissions->canEditDatabase('database_name'));
        self::assertTrue($permissions->canDeleteDatabase('database_name'));

        self::assertFalse($permissions->canCreateTable('database_name', 'type_name'));
        self::assertFalse($permissions->canEditTable('database_name', 'type_name', 'table_name'));
        self::assertTrue($permissions->canDeleteTable('database_name', 'type_name', 'table_name'));

        self::assertFalse($permissions->canDeleteTable('information_schema', 'type_name', 'table_name'));

        self::assertTrue($permissions->canCreateItem('database_name', 'type_name', 'table_name'));
        self::assertTrue($permissions->canEditItem('database_name', 'type_name', 'table_name', 'item_id'));
        self::assertTrue($permissions->canDeleteItem('database_name', 'type_name', 'table_name', 'item_id'));

        self::assertFalse($permissions->canCreateItem('information_schema', 'type_name', 'table_name'));
        self::assertFalse($permissions->canEditItem('information_schema', 'type_name', 'table_name', 'item_id'));
        self::assertFalse($permissions->canDeleteItem('information_schema', 'type_name', 'table_name', 'item_id'));

        self::assertTrue($permissions->canExecuteCommands());
    }
}
