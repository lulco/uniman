<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Core\Forms\DatabaseForm\DatabaseForm;
use Nette\Application\ForbiddenRequestException;

class DatabasePresenter extends BasePresenter
{
    public function actionCreate($driver)
    {
        if (!$this->driver->permissions()->canCreateDatabase()) {
            throw new ForbiddenRequestException('Create database is not allowed');
        }
        $this->template->driver = $driver;
    }

//    public function actionEdit($driver, $database, $type, $table, $item)
//    {
//        if (!$this->driver->permissions()->canEditItem($database, $type, $table, $item)) {
//            throw new ForbiddenRequestException('Edit item is not allowed');
//        }
//        $this->template->driver = $driver;
//        $this->database = $database;
//        $this->template->type = $this->type = $type;
//        $this->table = $table;
//        $this->item = $item;
//    }
//
//    public function actionDelete($driver, $database, $type, $table, $item)
//    {
//        if (!$this->driver->permissions()->canDeleteItem($database, $type, $table, $item)) {
//            throw new ForbiddenRequestException('Delete item is not allowed');
//        }
//        if ($this->driver->dataManager()->deleteItem($type, $table, $item)) {
//            $this->flashMessage('Item was successfully deleted', 'success');
//        } else {
//            $this->flashMessage('Item was not deleted', 'danger');
//        }
//        $this->redirect('List:items', $driver, $database, $type, $table);
//    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->translator, $this->database);
    }

    protected function createComponentForm()
    {
        return new DatabaseForm($this->translator, $this->driver, $this->database);
    }
}
