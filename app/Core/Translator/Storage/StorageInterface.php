<?php

namespace Adminerng\Core\Translator\Storage;

interface StorageInterface
{
    /**
     * @param string $language
     * @param string $key
     * @param string $value
     * @return StorageInterface
     */
    public function add($language, $key, $value);

    /**
     * @param array $data data from driver in format [language1 => [['key' => 'key1', 'value' => 'value1], ['key' => 'key2', 'value' => 'value2]]]
     */
    public function store($data);

    /**
     * @param string $language
     * @param string $key
     * @return StorageInterface
     */
    public function remove($language, $key);

    /**
     * @param string $language
     * @param string $key
     * @return string
     */
    public function load($language, $key);

    /**
     * @return array array of all translations key => value
     */
    public function loadAll($language);

    /**
     * @return StorageInterface
     */
    public function clean();

    /**
     * @param mixed $expiration time when translations in storage expired, e.g. 19-10-2015 7:29
     */
    public function isActual($expiration);
}
