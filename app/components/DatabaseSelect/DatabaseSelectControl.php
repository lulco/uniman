<?php

namespace Adminerng\Components\DatabaseSelect;

use Adminerng\Core\DriverInterface;
use Nette\Application\UI\Control;

class DatabaseSelectControl extends Control
{
    private $driver;

    private $database;
    
    public function __construct(DriverInterface $driver, $database = null)
    {
        parent::__construct();
        $this->driver = $driver;
        $this->database = $database;
    }

    public function render()
    {
        $this->template->driver = $this->driver;
        $this->template->actualDatabase = $this->database;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
