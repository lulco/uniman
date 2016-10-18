<?php

namespace Adminerng\Components\DatabaseSelect;

use Adminerng\Core\DriverInterface;
use Adminerng\Core\Exception\NoTablesJustItemsException;
use Nette\Application\UI\Control;

class TablesSideBarControl extends Control
{
    private $driver;

    private $database;

    private $table;

    public function __construct(DriverInterface $driver, $database, $table = null)
    {
        parent::__construct();
        $this->driver = $driver;
        $this->database = $database;
        $this->table = $table;
    }

    public function render()
    {
        $this->template->driver = $this->driver;
        $this->template->actualDatabase = $this->database;
        $this->template->actualTable = $this->table;
        try {
            $tables = $this->driver->dataManager()->tables();
        } catch (NoTablesJustItemsException $e) {
            $tables = [];
        }
        $this->template->tables = $tables;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
