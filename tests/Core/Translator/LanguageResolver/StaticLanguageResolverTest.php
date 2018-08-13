<?php

namespace UniMan\Tests\Core\Translator\LanguageResolver;

use PHPUnit\Framework\TestCase;
use UniMan\Core\Translator\LanguageResolver\StaticLanguageResolver;

class StaticLanguageResolverTest extends TestCase
{
    public function testResolve()
    {
        $languageResolver = new StaticLanguageResolver('en');
        self::assertEquals('en', $languageResolver->resolve());
    }
}
