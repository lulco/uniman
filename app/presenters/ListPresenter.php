<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Components\DatabaseSelect\TablesSideBarControl;
use App\Component\VisualPaginator;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapInlineRenderer;

class ListPresenter extends BasePresenter
{
    private $database;

    private $table;

    private $onPage;

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

    public function renderItems($driver, $database, $type, $table, $page = 1, $onPage = 50)
    {
        $this->database = $database;
        $this->table = $table;
        $this->onPage = $onPage;

        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->type = $type;
        $this->template->table = $table;
        $itemsCount = $this->driver->itemsCount($database, $type, $table);
        $this->template->itemsCount = $itemsCount;
        $this->template->items = $this->driver->items($database, $type, $table, $page, $onPage);
        $this->template->itemsTitle = $this->driver->itemsTitles($type);
        $this->template->itemsHeaders = $this->driver->itemsHeaders($type);
        
        $visualPaginator = $this['paginator'];
        $paginator = $visualPaginator->getPaginator();
        $paginator->setItemCount($itemsCount);
        $paginator->setItemsPerPage($onPage);
        $paginator->page = $page;
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->database);
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
        $form->addSubmit('filter', 'Filter');
        return $form;
    }
}
