<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Components\DatabaseSelect\TablesSideBarControl;
use Adminerng\Core\Exception\NoTablesJustItemsException;
use Adminerng\Core\Forms\TableForm;
use App\Component\VisualPaginator;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapInlineRenderer;

class ListPresenter extends BasePresenter
{
    private $onPage;

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

    public function actionDelete($driver, $database, $type, $table)
    {
        if (!$this->driver->permissions()->canDeleteTable($database, $type, $table)) {
            throw new ForbiddenRequestException('Delete table is not allowed');
        }
        if ($this->driver->dataManager()->deleteTable($database, $type, $table)) {
            $this->flashMessage('Table was successfully deleted', 'success');
        } else {
            $this->flashMessage('Table was not deleted', 'danger');
        }
        $this->redirect('List:tables', $driver, $database);
    }

    public function renderDatabases($driver, array $sorting = [])
    {
        $this->template->driver = $driver;
        $this->template->databasesHeaders = $this->driver->databasesHeaders();
        $this->template->databases = $this->driver->dataManager()->databases($sorting);
        $this->template->sorting = $sorting;
    }

    public function renderTables($driver, $database = null, array $sorting = [])
    {
        if ($database === null) {
            $this->redirect('List:databases', $driver);
        }
        $this->database = $database;

        try {
            $tables = $this->driver->dataManager()->tables($sorting);
        } catch (NoTablesJustItemsException $e) {
            $this->redirect('List:items', $driver, $database, $e->getType(), $e->getTable());
        }

        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->tables = $tables;
        $this->template->tablesHeaders = $this->driver->tablesHeaders();
        $this->template->sorting = $sorting;
    }

    public function renderItems($driver, $database, $type, $table, $page = 1, $onPage = 50, array $filter = [], array $sorting = [])
    {
        ksort($sorting);
        $this->database = $database;
        $this->type = $type;
        $this->table = $table;
        $this->onPage = $onPage;

        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->type = $type;
        $this->template->table = $table;
        $this->template->sorting = $sorting;

        $itemsCount = $this->driver->dataManager()->itemsCount($type, $table, $filter);
        $this->template->itemsCount = $itemsCount;
        $this->template->items = $this->driver->dataManager()->items($type, $table, $page, $onPage, $filter, $sorting);
        $this->template->columns = $this->driver->columns($type, $table);

        $visualPaginator = $this['paginator'];
        $paginator = $visualPaginator->getPaginator();
        $paginator->setItemCount($itemsCount);
        $paginator->setItemsPerPage($onPage);
        $paginator->page = $page;
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->translator, $this->database);
    }

    protected function createComponentTablesSideBar()
    {
        return new TablesSideBarControl($this->driver, $this->database, $this->table);
    }

    protected function createComponentPaginator()
    {
        return new VisualPaginator();
    }

    protected function createComponentFilterForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapInlineRenderer());
        $form->setMethod('get');
        $form->addText('onPage', 'On page')
            ->setDefaultValue($this->onPage);
        $form->addSubmit('submit', 'Filter');
        return $form;
    }

    protected function createComponentForm()
    {
        return new TableForm($this->translator, $this->driver, $this->database, $this->type, $this->table);
    }
}
