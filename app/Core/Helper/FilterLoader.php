<?php

namespace UniMan\Core\Helper;

use Nette\Utils\Strings;

class FilterLoader
{
    private $filters = [];

    public function load(string $helper)
    {
        if (isset($this->filters[$helper])) {
            return call_user_func_array($this->filters[$helper], array_slice(func_get_args(), 1));
        }
    }

    public function register(string $name, callable $callable): void
    {
        $this->filters[Strings::lower($name)] = $callable;
    }
}
