<?php

namespace Adminerng\Tests\Core\Helper;

use Adminerng\Core\Helper\Formatter;
use Adminerng\Core\Translator\LanguageResolver\StaticLanguageResolver;
use Adminerng\Core\Translator\Loader\NeonFileLoader;
use Adminerng\Core\Translator\Storage\MemoryStorage;
use Adminerng\Core\Translator\Translator;
use PHPUnit_Framework_TestCase;

class FormatterTest extends PHPUnit_Framework_TestCase
{
    private $formatter;

    protected function setUp()
    {
        $localizationDirectory = __DIR__ . '/../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('en');
        $translator = new Translator($storage, $languageResolver);
        $this->formatter = new Formatter($translator);
    }

    public function testFormatNumber()
    {
        self::assertEquals('123,456,789', $this->formatter->formatNumber(123456789.256));
        self::assertEquals('123,456,789.26', $this->formatter->formatNumber(123456789.256, 2));
        self::assertEquals('123,456,789.256', $this->formatter->formatNumber(123456789.256, 3));
    }

    public function testFormatSize()
    {
        self::assertEquals('123 B', $this->formatter->formatSize(123));
        self::assertEquals('1.2 kB', $this->formatter->formatSize(1234));
        self::assertEquals('12.1 kB', $this->formatter->formatSize(12345));
        self::assertEquals('120.6 kB', $this->formatter->formatSize(123456));
        self::assertEquals('1.2 MB', $this->formatter->formatSize(1234567));
        self::assertEquals('11.8 MB', $this->formatter->formatSize(12345678));
        self::assertEquals('117.7 MB', $this->formatter->formatSize(123456789));
        self::assertEquals('1.1 GB', $this->formatter->formatSize(1234567890));
        self::assertEquals('11.5 GB', $this->formatter->formatSize(12345678901));
        self::assertEquals('115.0 GB', $this->formatter->formatSize(123456789012));
        self::assertEquals('1.1 TB', $this->formatter->formatSize(1234567890123));
        self::assertEquals('11,228.3 TB', $this->formatter->formatSize(12345678901234567));
    }

    public function testFromatTime()
    {
        self::assertEquals('15 s', $this->formatter->formatTime(15));
        self::assertEquals('01:00', $this->formatter->formatTime(60));
        self::assertEquals('02:30', $this->formatter->formatTime(150));
        self::assertEquals('08:20', $this->formatter->formatTime(500));
        self::assertEquals('41:40', $this->formatter->formatTime(2500));
        self::assertEquals('58:20', $this->formatter->formatTime(3500));
        self::assertEquals('1h 00m 00s', $this->formatter->formatTime(3600));
        self::assertEquals('1h 23m 20s', $this->formatter->formatTime(5000));
        self::assertEquals('1h 32m 35s', $this->formatter->formatTime(5555));
    }
}
