<?php

namespace UniMan\Drivers\Redis;

use Exception;
use Nette\Localization\ITranslator;
use Phar;

class RedisDatabaseAliasStorage
{
    private $translator;

    private $baseDirname;

    private $savesDirname = 'uniman_saves';

    private $filename = 'redis_aliases.json';

    public function __construct($dir, ITranslator $translator)
    {
        $this->translator = $translator;
        $pharFile = Phar::running(false);
        $dirname = $pharFile ? dirname($pharFile) : $dir;
        $this->baseDirname = realpath($dirname);
    }

    public function loadAll()
    {
        $filename = $this->createFilename();
        $content = file_exists($filename) ? file_get_contents($filename) : null;
        return $content ? json_decode($content, true) : [];
    }

    public function load($database)
    {
        $aliases = $this->loadAll();
        return isset($aliases[$database]) ? $aliases[$database] : null;
    }

    public function check()
    {
        if (!is_writeable($this->baseDirname)) {
            throw new Exception($this->translator->translate('redis.alias_storage_not_writable', ['directory' => $this->baseDirname]));
        }
        $dirname = $this->baseDirname . '/' . $this->savesDirname;
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }
        if (!file_exists($dirname)) {
            throw new Exception($this->translator->translate('redis.alias_storage_not_writable', ['directory' => $this->baseDirname]));
        }
        return true;
    }

    public function save(array $aliases)
    {
        ksort($aliases);
        $filename = $this->createFilename();
        return (bool) file_put_contents($filename, json_encode($aliases));
    }

    private function createFilename()
    {
        return $this->baseDirname . '/' . $this->savesDirname . '/' . $this->filename;
    }
}
