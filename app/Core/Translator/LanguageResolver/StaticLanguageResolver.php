<?php

namespace Adminerng\Core\Translator\LanguageResolver;

class StaticLanguageResolver implements LanguageResolverInterface
{
    private $language;

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function resolve()
    {
        return $this->language;
    }
}
