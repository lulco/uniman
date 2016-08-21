<?php

namespace Adminerng\Core;

class DriverStorage
{
    private $drivers = [];

    public function add(DriverInterface $driver)
    {
        $this->drivers[$driver->type()] = $driver;
        return $this;
    }
    
    public function getDrivers()
    {
        return $this->drivers;
    }
    
    public function getDriver($driver)
    {
        return isset($this->drivers[$driver]) ? $this->drivers[$driver] : null;
    }
}
