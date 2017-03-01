<?php

namespace UniMan\Core\Translator\Loader;

interface LoaderInterface
{
    /**
     * @param string $lang
     * @return array
     */
    public function load($lang);
}
