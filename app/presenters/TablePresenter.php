<?php

namespace Adminerng\Presenters;

use Adminerng\Core\Exception\NoTablesJustItemsException;
use Adminerng\Core\Forms\TableForm;
use Nette\Application\ForbiddenRequestException;

class TablePresenter extends BasePresenter
{
    public function renderDefault($driver, $database = null, array $sorting = [])
    {
        if ($database === null) {
            $this->redirect('Database:default', $driver);
        }
        $this->database = $database;

        try {
            $tables = $this->driver->dataManager()->tables($sorting);
        } catch (NoTablesJustItemsException $e) {
            $this->redirect('Item:default', $driver, $database, $e->getType(), $e->getTable());
        }

        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->tables = $tables;
        $this->template->tablesHeaders = $this->driver->headerManager()->tablesHeaders();
        $this->template->sorting = $sorting;
    }

    public function actionCreate($driver, $database, $type)
    {
        if (!$this->driver->permissions()->canCreateTable($database, $type)) {
            throw new ForbiddenRequestException('Create table is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
        $this->template->type = $this->type = $type;
    }

    public function actionEdit($driver, $database, $type, $table)
    {
        if (!$this->driver->permissions()->canEditTable($database, $type, $table)) {
            throw new ForbiddenRequestException('Edit table is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
        $this->template->type = $this->type = $type;
        $this->table = $table;
    }

    public function handleDelete($driver, $database, $type, $table)
    {
        if (!$this->driver->permissions()->canDeleteTable($database, $type, $table)) {
            throw new ForbiddenRequestException('Delete table is not allowed');
        }
        if ($this->driver->dataManager()->deleteTable($type, $table)) {
            $this->flashMessage('Table was successfully deleted', 'success');
        } else {
            $this->flashMessage('Table was not deleted', 'danger');
        }
        $this->redirect('this', $driver, $database);
    }

    protected function createComponentForm()
    {
        return new TableForm($this->translator, $this->driver, $this->database, $this->type, $this->table);
    }
}
