<?php

namespace Adminerng\Tests\Drivers\RabbitMQ;

use Adminerng\Core\Column;
use Adminerng\Drivers\RabbitMQ\RabbitMQDriver;
use Adminerng\Drivers\RabbitMQ\RabbitMQHeaderManager;
use Adminerng\Tests\Drivers\AbstractDriverTest;

class RabbitMQHeaderManagerTest extends AbstractDriverTest
{
    public function testDatabasesHeaders()
    {
        $headerManager = new RabbitMQHeaderManager();
        $databasesHeaders = $headerManager->databasesHeaders();
        self::assertTrue(is_array($databasesHeaders));
        self::assertNotEmpty($databasesHeaders);
        foreach ($databasesHeaders as $databasesHeader) {
            self::assertInstanceOf(Column::class, $databasesHeader);
        }
    }

    public function testTablesHeaders()
    {
        $headerManager = new RabbitMQHeaderManager();
        $tablesHeaders = $headerManager->tablesHeaders();
        self::assertTrue(is_array($tablesHeaders));
        self::assertNotEmpty($tablesHeaders);
        $types = [
            RabbitMQDriver::TYPE_QUEUE,
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
        $headerManager = new RabbitMQHeaderManager();

        $types = [
            RabbitMQDriver::TYPE_QUEUE,
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
