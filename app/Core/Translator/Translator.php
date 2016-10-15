<?php

namespace Adminerng\Core\Translator;

use Adminerng\Core\Translator\Storage\StorageInterface;
use Nette\Localization\ITranslator;

class Translator implements ITranslator
{
    private $backupLanguage;

    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function setBackupLanguage($language)
    {
        $this->backupLanguage = $language;
        return $this;
    }

    public function getLanguage()
    {
        return filter_input(INPUT_GET, 'locale');
    }

    public function translate($message, $count = null)
    {
        $value = $this->storage->load($this->getLanguage(), $message);
        if ($value) {
            return $value;
        }

        if (!$this->backupLanguage) {
            return '';
        }

        $value = $this->storage->load($this->backupLanguage, $message);
        return $value ?: $message;
    }
}
