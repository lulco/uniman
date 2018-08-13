<?php

namespace UniMan\Tests\Core\Translator\Loader;

use PHPUnit\Framework\TestCase;
use UniMan\Core\Translator\Loader\NeonFileLoader;

class NeonFileLoaderTest extends TestCase
{
    public function testLoad()
    {
        $localizationDirectory = __DIR__ . '/../../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);

        $en = $loader->load('en');
        self::assertTrue(is_array($en));
        self::assertTrue(!empty($en));

        $sk = $loader->load('sk');
        self::assertTrue(is_array($sk));
        self::assertTrue(!empty($sk));

        $cz = $loader->load('cz');
        self::assertTrue(is_array($cz));
        self::assertTrue(empty($cz));
    }
}
