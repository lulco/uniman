<?php

namespace Adminerng\Router;

use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;

class RouterFactory
{
    /**
     * @return IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList();
        $router[] = new SimpleRouter('Homepage:default');
        return $router;
    }
}
