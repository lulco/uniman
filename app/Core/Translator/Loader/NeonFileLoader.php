<?php

namespace Adminerng\Core\Translator\Loader;

use Nette\Neon\Neon;
use Nette\Utils\Finder;

class NeonFileLoader implements LoaderInterface
{
    private $localizationDirectory;

    public function __construct($localizationDirectory)
    {
        $this->localizationDirectory = rtrim($localizationDirectory, '/');
    }

    public function load($lang)
    {
        $translations = [];
        $dir = $this->localizationDirectory . '/' . $lang;
        if (!file_exists($dir)) {
            return $translations;
        }
        $files = Finder::findFiles('*.neon')->in($dir);
        foreach ($files as $file) {
            $translations[pathinfo($file, PATHINFO_FILENAME)] = Neon::decode(file_get_contents($file));
        }
        $this->flatten($translations);
        return $translations;
    }

    private function flatten(array &$messages, array $subnode = null, $path = null)
    {
        if ($subnode === null) {
            $subnode = &$messages;
        }
        foreach ($subnode as $key => $value) {
            if (is_array($value)) {
                $nodePath = $path ? $path . '.' . $key : $key;
                $this->flatten($messages, $value, $nodePath);
                if ($path === null) {
                    unset($messages[$key]);
                }
            } elseif ($path !== null) {
                $messages[$path . '.' . $key] = $value;
            }
        }
    }
}
