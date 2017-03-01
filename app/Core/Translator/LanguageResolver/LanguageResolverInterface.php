<?php

namespace UniMan\Core\Translator\LanguageResolver;

interface LanguageResolverInterface
{
    /**
     * @return string|null string for language or null if language cannot be resolved
     */
    public function resolve();
}
