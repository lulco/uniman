<?php

namespace UniMan\Tests\Drivers\Memcache;

use UniMan\Core\CredentialsFormInterface;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;
use UniMan\Core\Permissions\PermissionsInterface;
use UniMan\Drivers\Memcache\MemcacheDriver;
use UniMan\Tests\Drivers\AbstractDriverTest;

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
