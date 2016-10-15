<?php

namespace Adminerng\Core\Translator;

use Adminerng\Core\Translator\Storage\StorageInterface;
use Nette\Localization\ITranslator;

class Translator implements ITranslator
{
    private $defaultLanguage;

    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;
        return $this;
    }

    public function getLanguage()
    {
        return filter_input(INPUT_GET, 'locale');
    }

    public function translate($message, $params = null)
    {
        if ($this->getLanguage()) {
            $value = $this->storage->load($this->getLanguage(), $message);
            if ($value) {
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
