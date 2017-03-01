<?php

namespace UniMan\Tests\Drivers\RabbitMQ;

use UniMan\Core\CredentialsFormInterface;
use UniMan\Core\DataManager\DataManagerInterface;
use UniMan\Core\Forms\FormManagerInterface;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;
use UniMan\Core\Permissions\PermissionsInterface;
use UniMan\Drivers\RabbitMQ\RabbitMQDriver;
use UniMan\Tests\Drivers\AbstractDriverTest;

class RabbitMQDriverTest extends AbstractDriverTest
{
    public function testDriver()
    {
        $driver = new RabbitMQDriver($this->translator);
        $driver->connect($driver->defaultCredentials());

        self::assertTrue(is_string($driver->type()));
        self::assertTrue(is_string($driver->name()));
        self::assertTrue(is_array($driver->classes()));
        self::assertTrue(is_array($driver->extensions()));
        self::assertTrue(is_bool($driver->check()));
        self::assertTrue(is_array($driver->defaultCredentials()));
        self::assertInstanceOf(CredentialsFormInterface::class, $driver->getCredentialsForm());
        self::assertInstanceOf(DataManagerInterface::class, $driver->dataManager());
        self::assertInstanceOf(FormManagerInterface::class, $driver->formManager());
        self::assertInstanceOf(HeaderManagerInterface::class, $driver->headerManager());
        self::assertInstanceOf(PermissionsInterface::class, $driver->permissions());
    }
}
