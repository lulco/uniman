<?php

namespace UniMan\Router;

use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;

class RouterFactory
{
    public static function createRouter(): IRouter
    {
        $router = new RouteList();
        $router[] = new SimpleRouter('Default:default');
        return $router;
    }
}
