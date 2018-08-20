<?php

namespace UniMan\Tests\Drivers\Redis;

use UniMan\Core\Column;
use UniMan\Drivers\Redis\RedisDriver;
use UniMan\Drivers\Redis\RedisHeaderManager;
use UniMan\Tests\Drivers\AbstractDriverTest;

class RedisHeaderManagerTest extends AbstractDriverTest
{
    public function testDatabasesHeaders()
    {
        $headerManager = new RedisHeaderManager();
        $databasesHeaders = $headerManager->databasesHeaders();
        self::assertTrue(is_array($databasesHeaders));
        self::assertNotEmpty($databasesHeaders);
        foreach ($databasesHeaders as $databasesHeader) {
            self::assertInstanceOf(Column::class, $databasesHeader);
        }
    }

    public function testTablesHeaders()
    {
        $headerManager = new RedisHeaderManager();
        $tablesHeaders = $headerManager->tablesHeaders();
        self::assertTrue(is_array($tablesHeaders));
        self::assertNotEmpty($tablesHeaders);
        $types = [
            RedisDriver::TYPE_KEY,
            RedisDriver::TYPE_HASH,
            RedisDriver::TYPE_SET,
            RedisDriver::TYPE_LIST,
            RedisDriver::TYPE_SORTED_SET,
        ];
        foreach ($types as $type) {
            self::assertArrayHasKey($type, $tablesHeaders);
        }
        foreach ($tablesHeaders as $type => $typeTableHeaders) {
            self::assertTrue(in_array($type, $types));
            foreach ($typeTableHeaders as $tablesHeader) {
                self::assertInstanceOf(Column::class, $tablesHeader);
            }
        }
    }

    public function testItemsHeaders()
    {
        $headerManager = new RedisHeaderManager();

        $types = [
            RedisDriver::TYPE_KEY,
            RedisDriver::TYPE_HASH,
            RedisDriver::TYPE_SET,
            RedisDriver::TYPE_LIST,
            RedisDriver::TYPE_SORTED_SET,
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
