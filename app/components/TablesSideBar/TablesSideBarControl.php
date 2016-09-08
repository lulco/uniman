<?php

namespace Adminerng\Components\DatabaseSelect;

use Adminerng\Core\DriverInterface;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

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
        $this->template->tables = $this->driver->tables($this->database);
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
