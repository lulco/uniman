<?php

namespace Adminerng\Core\Translator;

use Adminerng\Core\Translator\LanguageResolver\LanguageResolverInterface;
use Adminerng\Core\Translator\Storage\StorageInterface;
use Nette\Localization\ITranslator;

class Translator implements ITranslator
{
    private $storage;

    private $languageResolver;

    private $defaultLanguage;

    public function __construct(StorageInterface $storage, LanguageResolverInterface $languageResolver)
    {
        $this->storage = $storage;
        $this->languageResolver = $languageResolver;
    }

    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;
        return $this;
    }

    public function translate($message, $params = null)
    {
        $language = $this->languageResolver->resolve();
        if ($language !== null) {
            $value = $this->storage->load($language, $message);
            if ($value !== null) {
                return $this->replaceTokens($value, $params);
            }
        }

        if (!$this->defaultLanguage) {
            return '';
        }

        $value = $this->storage->load($this->defaultLanguage, $message);
        return $value ? $this->replaceTokens($value, $params) : $message;
    }

    private function replaceTokens($message, $params = null)
    {
        if (!$params) {
            return $message;
        }
        foreach ($params as $key => $value) {
            $message = str_replace('%' . $key . '%', $value, $message);
        }
        return $message;
    }
}
