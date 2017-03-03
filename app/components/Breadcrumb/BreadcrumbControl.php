<?php

namespace UniMan\Components\Breadcrumb;

use Nette\Application\UI\Control;

class BreadcrumbControl extends Control
{
    private $driver;

    private $database;

    private $databaseName;

    private $type;

    private $table;

    private $item;

    public function __construct($driver, $database, $databaseName, $type, $table, $item)
    {
        parent::__construct();
        $this->driver = $driver;
        $this->database = $database;
        $this->databaseName = $databaseName;
        $this->type = $type;
        $this->table = $table;
        $this->item = $item;
    }

    public function render()
    {
        $this->template->driver = $this->driver;
        $this->template->database = $this->database;
        $this->template->databaseName = $this->databaseName;
        $this->template->type = $this->type;
        $this->template->table = $this->table;
        $this->template->item = $this->item;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
