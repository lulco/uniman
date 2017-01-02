<?php

namespace Adminerng\Tests\Drivers;

use Adminerng\Core\Helper\Formatter;
use Adminerng\Core\Translator\LanguageResolver\StaticLanguageResolver;
use Adminerng\Core\Translator\Loader\NeonFileLoader;
use Adminerng\Core\Translator\Storage\MemoryStorage;
use Adminerng\Core\Translator\Translator;
use PHPUnit_Framework_TestCase;

abstract class AbstractDriverTest extends PHPUnit_Framework_TestCase
{
    protected $translator;

    protected $formatter;

    protected function setUp()
    {
        $localizationDirectory = __DIR__ . '/../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('en');
        $this->translator = new Translator($storage, $languageResolver);
        $this->formatter = new Formatter($this->translator);
    }
}
