<?php

namespace UniMan\Tests\Core\Translator\LanguageResolver;

use PHPUnit\Framework\TestCase;
use UniMan\Core\Translator\LanguageResolver\UrlLanguageResolver;

class UrlLanguageResolverTest extends TestCase
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
