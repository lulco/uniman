<?php

namespace UniMan\Core\Translator\Storage;

use UniMan\Core\Translator\Loader\LoaderInterface;

class MemoryStorage implements StorageInterface
{
    private $loader;

    private $translations = [];

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function load($language, $key)
    {
        if (!isset($this->translations[$language])) {
            $this->translations[$language] = $this->loader->load($language);
        }
        return isset($this->translations[$language][$key]) ? $this->translations[$language][$key] : null;
    }
}
