<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Core\Forms\ItemForm;
use Nette\Application\ForbiddenRequestException;

class ItemPresenter extends BasePresenter
{
    private $database;

    private $type;
    
    private $table;

    private $item;

    public function actionCreate($driver, $database, $type, $table)
    {
        if (!$this->driver->permissions()->canCreateItem($database, $type, $table)) {
            throw new ForbiddenRequestException('Create item is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
        $this->type = $type;
        $this->table = $table;
    }

    public function actionEdit($driver, $database, $type, $table, $item)
    {
        if (!$this->driver->permissions()->canEditItem($database, $type, $table, $item)) {
            throw new ForbiddenRequestException('Edit item is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
        $this->type = $type;
        $this->table = $table;
        $this->item = $item;
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->database);
    }

    protected function createComponentForm()
    {
        return new ItemForm($this->translator, $this->driver, $this->database, $this->type, $this->table, $this->item);
    }
}
