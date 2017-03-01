<?php

namespace UniMan\Drivers\Redis;

class RedisDatabaseAliasStorage
{
    private $filename;

    public function __construct()
    {
        $dirname = sys_get_temp_dir() . '/uniman';
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }
        $this->filename = $dirname . '/redis_aliases.json';
    }

    public function loadAll()
    {
        $content = file_exists($this->filename) ? file_get_contents($this->filename) : null;
        return $content ? json_decode($content, true) : [];
    }

    public function load($database)
    {
        $aliases = $this->loadAll();
        return isset($aliases[$database]) ? $aliases[$database] : null;
    }

    public function save(array $aliases)
    {
        ksort($aliases);
        return (bool) file_put_contents($this->filename, json_encode($aliases));
    }
}
