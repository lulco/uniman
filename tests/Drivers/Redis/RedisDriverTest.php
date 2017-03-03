<?php

namespace UniMan\Tests\Drivers\Redis;

use UniMan\Core\CredentialsFormInterface;
use UniMan\Core\DataManager\DataManagerInterface;
use UniMan\Core\Forms\FormManagerInterface;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;
use UniMan\Core\Permissions\PermissionsInterface;
use UniMan\Drivers\Redis\RedisDatabaseAliasStorage;
use UniMan\Drivers\Redis\RedisDriver;
use UniMan\Tests\Drivers\AbstractDriverTest;

class RedisDriverTest extends AbstractDriverTest
{
    public function testDriver()
    {
        $redisDatabaseAliasStorage = new RedisDatabaseAliasStorage(sys_get_temp_dir(), $this->translator);
        $driver = new RedisDriver($this->translator, $redisDatabaseAliasStorage);
        $credentials = [
            'host' => getenv('UNIMAN_REDIS_HOST'),
            'port' => getenv('UNIMAN_REDIS_PORT'),
            'database' => getenv('UNIMAN_REDIS_DATABASE'),
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
