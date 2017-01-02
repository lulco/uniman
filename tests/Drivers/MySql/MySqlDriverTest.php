<?php

namespace Adminerng\Tests\Drivers\MySql;

use Adminerng\Core\CredentialsFormInterface;
use Adminerng\Core\Permissions\PermissionsInterface;
use Adminerng\Drivers\MySql\MySqlDriver;
use Adminerng\Tests\Drivers\AbstractDriverTest;

class MySqlDriverTest extends AbstractDriverTest
{
    public function testDriver()
    {
        $driver = new MySqlDriver($this->translator);
        self::assertTrue(is_string($driver->type()));
        self::assertTrue(is_string($driver->name()));
        self::assertTrue(is_array($driver->classes()));
        self::assertTrue(is_array($driver->extensions()));
        self::assertTrue(is_bool($driver->check()));
        self::assertTrue(is_array($driver->defaultCredentials()));
        self::assertInstanceOf(CredentialsFormInterface::class, $driver->getCredentialsForm());
        self::assertInstanceOf(PermissionsInterface::class, $driver->permissions());
    }
}
