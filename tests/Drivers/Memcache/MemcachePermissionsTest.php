<?php

namespace Adminerng\Tests\Drivers\Memcache;

use Adminerng\Drivers\Memcache\MemcachePermissions;
use PHPUnit_Framework_TestCase;

class MemcachePermissionsTest extends PHPUnit_Framework_TestCase
{
    public function testPermissions()
    {
        $permissions = new MemcachePermissions();

        self::assertFalse($permissions->canCreateDatabase());
        self::assertFalse($permissions->canEditDatabase('database_name'));
        self::assertFalse($permissions->canDeleteDatabase('database_name'));

        self::assertFalse($permissions->canCreateTable('database_name', 'type_name'));
        self::assertFalse($permissions->canEditTable('database_name', 'type_name', 'table_name'));
        self::assertFalse($permissions->canDeleteTable('database_name', 'type_name', 'table_name'));

        self::assertTrue($permissions->canCreateItem('database_name', 'type_name', 'table_name'));
        self::assertTrue($permissions->canEditItem('database_name', 'type_name', 'table_name', 'item_id'));
        self::assertTrue($permissions->canDeleteItem('database_name', 'type_name', 'table_name', 'item_id'));

        self::assertFalse($permissions->canExecuteCommands());
    }
}
