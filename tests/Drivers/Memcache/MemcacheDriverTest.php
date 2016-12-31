<?php

namespace Adminerng\Tests\Drivers\Memcache;

use Adminerng\Core\CredentialsFormInterface;
use Adminerng\Core\DataManager\DataManagerInterface;
use Adminerng\Core\Forms\FormManagerInterface;
use Adminerng\Core\Helper\Formatter;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;
use Adminerng\Core\Permissions\PermissionsInterface;
use Adminerng\Core\Translator\LanguageResolver\StaticLanguageResolver;
use Adminerng\Core\Translator\Loader\NeonFileLoader;
use Adminerng\Core\Translator\Storage\MemoryStorage;
use Adminerng\Core\Translator\Translator;
use Adminerng\Drivers\Memcache\MemcacheDriver;
use PHPUnit_Framework_TestCase;

class MemcacheDrverTest extends PHPUnit_Framework_TestCase
{
    private $translator;

    private $formatter;

    protected function setUp()
    {
        $localizationDirectory = __DIR__ . '/../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('en');
        $this->translator = new Translator($storage, $languageResolver);
        $this->formatter = new Formatter($this->translator);
    }

    public function testDriverWithoutConnection()
    {
        $driver = new MemcacheDriver($this->translator, $this->formatter);
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

//    public function testDriverWithConnection()
//    {
//        $driver = new MemcacheDriver($this->translator, $this->formatter);
//        $driver->connect($driver->defaultCredentials());
//
//        self::assertTrue(is_string($driver->type()));
//        self::assertTrue(is_string($driver->name()));
//        self::assertTrue(is_array($driver->classes()));
//        self::assertTrue(is_array($driver->extensions()));
//        self::assertTrue(is_bool($driver->check()));
//        self::assertTrue(is_array($driver->defaultCredentials()));
//        self::assertInstanceOf(CredentialsFormInterface::class, $driver->getCredentialsForm());
//        self::assertInstanceOf(HeaderManagerInterface::class, $driver->headerManager());
//        self::assertInstanceOf(PermissionsInterface::class, $driver->permissions());
//        self::assertInstanceOf(DataManagerInterface::class, $driver->dataManager());
//        self::assertInstanceOf(FormManagerInterface::class, $driver->formManager());
//    }
}
