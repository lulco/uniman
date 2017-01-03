<?php

namespace Adminerng\Tests\Drivers\Memcache;

use Adminerng\Core\Column;
use Adminerng\Drivers\Memcache\MemcacheDriver;
use Adminerng\Drivers\Memcache\MemcacheHeaderManager;
use Adminerng\Tests\Drivers\AbstractDriverTest;

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
