<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;

class ListPresenter extends BasePresenter
{
    private $database;

    public function renderDatabases($driver)
    {
        $this->template->driver = $driver;
        $this->template->databasesHeaders = $this->driver->databasesHeaders();
        $this->template->databases = $this->driver->databases();
    }
    
    public function renderTables($driver, $database = null)
    {
        if ($database === null) {
            $this->redirect('List:databases', $driver);
        }
        $this->database = $database;
        
        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->tables = $this->driver->tables($database);
        $this->template->itemsTitles = $this->driver->itemsTitles();
        $this->template->databaseTitle = $this->driver->databaseTitle();
        $this->template->tablesHeaders = $this->driver->tablesHeaders();
    }

    public function renderItems($driver, $database, $type, $table)
    {
        $this->database = $database;

        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->type = $type;
        $this->template->table = $table;
        $this->template->items = $this->driver->items($database, $type, $table);
        $this->template->itemsTitles = $this->driver->itemsTitles();
        $this->template->itemsHeaders = $this->driver->itemsHeaders();
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->database);
    }
}
