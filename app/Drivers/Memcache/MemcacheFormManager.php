<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\Forms\DefaultFormManager;
use Adminerng\Drivers\Memcache\Forms\MemcacheKeyForm;
use Memcache;

class MemcacheFormManager extends DefaultFormManager
{
    private $connection;

    public function __construct(Memcache $connection)
    {
        $this->connection = $connection;
    }

    public function itemForm($database, $type, $table, $item)
    {
        if ($type === MemcacheDriver::TYPE_KEY) {
            return new MemcacheKeyForm($this->connection, $item);
        }
        parent::itemForm($database, $type, $table, $item);
    }
}
