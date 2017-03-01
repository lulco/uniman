<?php

namespace UniMan\Tests\Drivers;

use UniMan\Core\Translator\LanguageResolver\StaticLanguageResolver;
use UniMan\Core\Translator\Loader\NeonFileLoader;
use UniMan\Core\Translator\Storage\MemoryStorage;
use UniMan\Core\Translator\Translator;
use PHPUnit_Framework_TestCase;

abstract class AbstractDriverTest extends PHPUnit_Framework_TestCase
{
    protected $translator;

    protected function setUp()
    {
        $localizationDirectory = __DIR__ . '/../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('en');
        $this->translator = new Translator($storage, $languageResolver);
    }
}
