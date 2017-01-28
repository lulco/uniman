<?php

namespace Adminerng\Tests\Drivers\MySql;

use Adminerng\Core\CredentialsFormInterface;
use Adminerng\Core\DataManager\DataManagerInterface;
use Adminerng\Core\Forms\FormManagerInterface;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;
use Adminerng\Core\Permissions\PermissionsInterface;
use Adminerng\Drivers\MySql\MySqlDriver;
use Adminerng\Tests\Drivers\AbstractDriverTest;

class MySqlDriverTest extends AbstractDriverTest
{
    public function testDriver()
    {
        $driver = new MySqlDriver($this->translator);
        $credentials = [
            'server' => getenv('ADMINERNG_MYSQL_SERVER'),
            'user' => getenv('ADMINERNG_MYSQL_USERNAME'),
            'password' => getenv('ADMINERNG_MYSQL_PASSWORD'),
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
