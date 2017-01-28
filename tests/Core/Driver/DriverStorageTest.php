<?php

namespace Adminerng\Tests\Core\Driver;

use Adminerng\Core\Driver\DriverInterface;
use Adminerng\Core\Driver\DriverStorage;
use Adminerng\Tests\Mock\Drivers\FakeDriver;
use PHPUnit_Framework_TestCase;

class DriverStorageTest extends PHPUnit_Framework_TestCase
{
    public function testDriverStorage()
    {
        $driverStorage = new DriverStorage();
        self::assertTrue(is_array($driverStorage->getDrivers()));
        self::assertEmpty($driverStorage->getDrivers());
        self::assertNull($driverStorage->getDriver('someDriver'));

        $fakeDriver = new FakeDriver();
        self::assertInstanceOf(DriverStorage::class, $driverStorage->add($fakeDriver));
        self::assertTrue(is_array($driverStorage->getDrivers()));
        self::assertCount(1, $driverStorage->getDrivers());
        self::assertInstanceOf(DriverInterface::class, $driverStorage->getDriver($fakeDriver->type()));
    }
}
