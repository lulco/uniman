<?php

namespace Adminerng\Tests\Drivers\Memcache;

use Adminerng\Core\CredentialsFormInterface;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;
use Adminerng\Core\Permissions\PermissionsInterface;
use Adminerng\Drivers\Memcache\MemcacheDriver;
use Adminerng\Tests\Drivers\AbstractDriverTest;

class MemcacheDriverTest extends AbstractDriverTest
{
    public function testDriver()
    {
        $driver = new MemcacheDriver($this->translator);
        self::assertTrue(is_string($driver->type()));
        self::assertTrue(is_string($driver->name()));
        self::assertTrue(is_array($driver->classes()));
        self::assertTrue(is_array($driver->extensions()));
        self::assertTrue(is_bool($driver->check()));
        self::assertTrue(is_array($driver->defaultCredentials()));
        self::assertInstanceOf(CredentialsFormInterface::class, $driver->getCredentialsForm());
        self::assertInstanceOf(HeaderManagerInterface::class, $driver->headerManager());
        self::assertInstanceOf(PermissionsInterface::class, $driver->permissions());
    }
}
