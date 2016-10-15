<?php

namespace Adminerng\Core\Translator\Loader;

use Nette\Neon\Neon;
use Symfony\Component\Finder\Finder;

class NeonFileLoader implements LoaderInterface
{
    private $localizationDirectory;

    public function __construct($localizationDirectory)
    {
        $this->localizationDirectory = $localizationDirectory;
    }

    public function load($lang)
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.neon')
            ->in($this->localizationDirectory . '/' . $lang);

        $translations = [];
        foreach ($finder as $file) {
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
                $nodePath = $path ? $path.'.'.$key : $key;
                $this->flatten($messages, $value, $nodePath);
                if ($path === null) {
                    unset($messages[$key]);
                }
            } elseif ($path !== null) {
                $messages[$path.'.'.$key] = $value;
            }
        }
    }
}
