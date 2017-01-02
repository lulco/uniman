<?php

namespace Adminerng\Tests\Core\Translator\LanguageResolver;

use Adminerng\Core\Translator\LanguageResolver\UrlLanguageResolver;
use PHPUnit_Framework_TestCase;

class UrlLanguageResolverTest extends PHPUnit_Framework_TestCase
{
    public function testNoQueryParam()
    {
        $languageResolver = new UrlLanguageResolver('http://www.example.com/test');
        self::assertNull($languageResolver->resolve());
    }

    public function testDefaultQueryParam()
    {
        $languageResolver = new UrlLanguageResolver('http://www.example.com/test?locale=en');
        self::assertEquals('en', $languageResolver->resolve());
    }

    public function testCustomQueryParam()
    {
        $languageResolver = new UrlLanguageResolver('http://www.example.com/test?language=sk', 'language');
        self::assertEquals('sk', $languageResolver->resolve());
    }
}
