<?php

namespace Adminerng\Core\Translator\Storage;

interface StorageInterface
{
    /**
     * @param string $language
     * @param string $key
     * @return string
     */
    public function load($language, $key);
}
