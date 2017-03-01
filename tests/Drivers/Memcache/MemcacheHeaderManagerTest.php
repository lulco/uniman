<?php

namespace UniMan\Tests\Drivers\Memcache;

use UniMan\Core\Column;
use UniMan\Drivers\Memcache\MemcacheDriver;
use UniMan\Drivers\Memcache\MemcacheHeaderManager;
use UniMan\Tests\Drivers\AbstractDriverTest;

class MemcacheHeaderManagerTest extends AbstractDriverTest
{
    public function testDatabasesHeaders()
    {
        $headerManager = new MemcacheHeaderManager();
        $databasesHeaders = $headerManager->databasesHeaders();
        self::assertTrue(is_array($databasesHeaders));
        self::assertNotEmpty($databasesHeaders);
        foreach ($databasesHeaders as $databasesHeader) {
            self::assertInstanceOf(Column::class, $databasesHeader);
        }
    }

    public function testTablesHeaders()
    {
        $headerManager = new MemcacheHeaderManager();
        $tablesHeaders = $headerManager->tablesHeaders();
        self::assertTrue(is_array($tablesHeaders));
        self::assertEmpty($tablesHeaders);
    }

    public function testItemsHeaders()
    {
        $headerManager = new MemcacheHeaderManager();

        $types = [
            MemcacheDriver::TYPE_KEY,
        ];
        foreach ($types as $type) {
            $itemsHeaders = $headerManager->itemsHeaders($type, 'whatever');
            self::assertTrue(is_array($itemsHeaders));
            self::assertNotEmpty($itemsHeaders);
            foreach ($itemsHeaders as $itemsHeader) {
                self::assertInstanceOf(Column::class, $itemsHeader);
            }
        }

        $unknownTypeItemsHeaders = $headerManager->itemsHeaders('unknown_type', 'whatever');
        self::assertTrue(is_array($unknownTypeItemsHeaders));
        self::assertEmpty($unknownTypeItemsHeaders);
    }
}
