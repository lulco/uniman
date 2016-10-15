<?php

namespace Adminerng\Core\Translator\Storage;

class MemoryStorage implements StorageInterface
{
    private $translations = [];

    private $actualized = null;

    public function add($language, $key, $value)
    {
        $this->translations[$language][$key] = $value;
        return $this;
    }

    public function store($data)
    {
        $this->actualized = date('c');
        foreach ($data as $language => $translations) {
            foreach ($translations as $translation) {
                $this->add($language, $translation['key'], $translation['value']);
            }
        }
    }

    public function remove($language, $key)
    {
        if (isset($this->translations[$language][$key])) {
            unset($this->translations[$language][$key]);
        }
        return $this;
    }

    public function load($language, $key)
    {
        return isset($this->translations[$language][$key]) ? $this->translations[$language][$key] : false;
    }

    public function loadAll($language)
    {
        return isset($this->translations[$language]) ? $this->translations[$language] : [];
    }

    public function clean()
    {
        $this->translations = [];
        $this->actualized = null;
        return $this;
    }

    public function isActual($expiration)
    {
        return $this->actualized && $this->actualized > $expiration;
    }
}
