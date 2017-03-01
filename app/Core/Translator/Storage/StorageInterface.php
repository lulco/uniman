<?php

namespace UniMan\Core\Translator\Storage;

interface StorageInterface
{
    /**
     * @param string $language
     * @param string $key
     * @return string|null string for translated text or null if translation cannot be found
     */
    public function load($language, $key);
}
