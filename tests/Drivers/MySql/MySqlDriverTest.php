<?php

namespace UniMan\Tests\Drivers\MySql;

use UniMan\Core\CredentialsFormInterface;
use UniMan\Core\DataManager\DataManagerInterface;
use UniMan\Core\Forms\FormManagerInterface;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;
use UniMan\Core\Permissions\PermissionsInterface;
use UniMan\Drivers\MySql\MySqlDriver;
use UniMan\Tests\Drivers\AbstractDriverTest;

class MySqlDriverTest extends AbstractDriverTest
{
    public function testDriver()
    {
        $driver = new MySqlDriver($this->translator);
        $credentials = [
            'server' => getenv('UNIMAN_MYSQL_SERVER'),
            'user' => getenv('UNIMAN_MYSQL_USERNAME'),
            'password' => getenv('UNIMAN_MYSQL_PASSWORD'),
        ];
        $driver->connect($credentials);

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
