<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Core\Forms\DatabaseForm\DatabaseForm;
use Nette\Application\ForbiddenRequestException;

class DatabasePresenter extends BasePresenter
{
    public function renderDefault($driver, array $sorting = [])
    {
        $this->template->driver = $driver;
        $this->template->databasesHeaders = $this->driver->headerManager()->databasesHeaders();
        $this->template->databases = $this->driver->dataManager()->databases($sorting);
        $this->template->sorting = $sorting;
    }

    public function actionCreate($driver)
    {
        if (!$this->driver->permissions()->canCreateDatabase()) {
            throw new ForbiddenRequestException('Create database is not allowed');
        }
        $this->template->driver = $driver;
    }

    public function actionEdit($driver, $database)
    {
        if (!$this->driver->permissions()->canEditDatabase($database)) {
            throw new ForbiddenRequestException('Edit database is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
    }

    public function actionDelete($driver, $database)
    {
        if (!$this->driver->permissions()->canDeleteDatabase($database)) {
            throw new ForbiddenRequestException('Delete database is not allowed');
        }
        if ($this->driver->dataManager()->deleteDatabase($database)) {
            $this->flashMessage('Database was successfully deleted', 'success');
        } else {
            $this->flashMessage('Database was not deleted', 'danger');
        }
        $this->redirect('Database:default', $driver);
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->translator, $this->database);
    }

    protected function createComponentForm()
    {
        return new DatabaseForm($this->translator, $this->driver, $this->database);
    }
}
