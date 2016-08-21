<?php

use Adminerng\Drivers\Memcache\MemcacheDriver;
use Adminerng\Drivers\Rabbit\RabbitDriver;
use Adminerng\Drivers\Redis\RedisDriver;

return [
    'redis' => new RedisDriver(),
    'memcache' => new MemcacheDriver(),
    'rabbit' => new RabbitDriver(),
];
