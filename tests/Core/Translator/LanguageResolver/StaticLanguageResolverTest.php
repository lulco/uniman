<?php

namespace Adminerng\Tests\Core\Translator\LanguageResolver;

use Adminerng\Core\Translator\LanguageResolver\StaticLanguageResolver;
use PHPUnit_Framework_TestCase;

class StaticLanguageResolverTest extends PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $languageResolver = new StaticLanguageResolver('en');
        self::assertEquals('en', $languageResolver->resolve());
    }
}
