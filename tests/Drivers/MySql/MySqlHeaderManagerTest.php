<?php

namespace Adminerng\Tests\Drivers\MySql;

use Adminerng\Core\Column;
use Adminerng\Drivers\MySql\MySqlDataManager;
use Adminerng\Drivers\MySql\MySqlDriver;
use Adminerng\Drivers\MySql\MySqlHeaderManager;
use Adminerng\Tests\Drivers\AbstractDriverTest;
use PDO;

class MySqlHeaderManagerTest extends AbstractDriverTest
{
    private $headerManager;

    protected function setUp()
    {
        parent::setUp();

        $host = getenv('ADMINERNG_MYSQL_SERVER') ?: 'localhost';
        $port = 3306;
        if (getenv('ADMINERNG_MYSQL_SERVER')) {
            list($host, $port) = explode(':', getenv('ADMINERNG_MYSQL_SERVER'));
        }
        $user = getenv('ADMINERNG_MYSQL_USERNAME') ?: 'root';
        $password = getenv('ADMINERNG_MYSQL_PASSWORD') ?: '';
        $dsn = 'mysql:;host=' . $host . ';port=' . $port . ';charset=utf8';
        $connection = new PDO($dsn, $user, $password);
        $dataManager = new MySqlDataManager($connection);
        $this->headerManager = new MySqlHeaderManager($dataManager);
    }

    public function testDatabasesHeaders()
    {
        $databasesHeaders = $this->headerManager->databasesHeaders();
        self::assertTrue(is_array($databasesHeaders));
        self::assertNotEmpty($databasesHeaders);
        foreach ($databasesHeaders as $databasesHeader) {
            self::assertInstanceOf(Column::class, $databasesHeader);
        }
    }

    public function testTablesHeaders()
    {
        $tablesHeaders = $this->headerManager->tablesHeaders();
        self::assertTrue(is_array($tablesHeaders));
        self::assertNotEmpty($tablesHeaders);
        $types = [
            MySqlDriver::TYPE_TABLE,
            MySqlDriver::TYPE_VIEW,
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

//    public function testItemsHeaders()
//    {
//        $types = [
//            MySqlDriver::TYPE_TABLE,
//            MySqlDriver::TYPE_VIEW,
//        ];
//        foreach ($types as $type) {
//            $itemsHeaders = $this->headerManager->itemsHeaders($type, 'whatever');
//            self::assertTrue(is_array($itemsHeaders));
//            self::assertNotEmpty($itemsHeaders);
//            foreach ($itemsHeaders as $itemsHeader) {
//                self::assertInstanceOf(Column::class, $itemsHeader);
//            }
//        }
//
//        $unknownTypeItemsHeaders = $this->headerManager->itemsHeaders('unknown_type', 'whatever');
//        self::assertTrue(is_array($unknownTypeItemsHeaders));
//        self::assertEmpty($unknownTypeItemsHeaders);
//    }
}
