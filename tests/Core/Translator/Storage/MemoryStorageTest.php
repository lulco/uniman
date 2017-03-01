<?php

namespace UniMan\Tests\Core\Translator\Storage;

use UniMan\Core\Translator\Loader\NeonFileLoader;
use UniMan\Core\Translator\Storage\MemoryStorage;
use PHPUnit_Framework_TestCase;

class MemoryStorageTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $localizationDirectory = __DIR__ . '/../../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);

        $storage = new MemoryStorage($loader);
        self::assertEquals('Yes', $storage->load('en', 'core.yes'));
        self::assertEquals('Áno', $storage->load('sk', 'core.yes'));
        self::assertNull($storage->load('cz', 'core.yes'));
    }
}
