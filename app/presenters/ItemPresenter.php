<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Components\DatabaseSelect\TablesSideBarControl;
use Adminerng\Core\Forms\ItemForm;
use App\Component\VisualPaginator;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapInlineRenderer;

class ItemPresenter extends BasePresenter
{
    private $onPage;

    public function renderDefault($driver, $database, $type, $table, $page = 1, $onPage = 50, array $filter = [], array $sorting = [])
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
        $this->template->columns = $this->driver->headerManager()->itemsHeaders($type, $table);

        $visualPaginator = $this['paginator'];
        $paginator = $visualPaginator->getPaginator();
        $paginator->setItemCount($itemsCount);
        $paginator->setItemsPerPage($onPage);
        $paginator->page = $page;
    }

    public function actionCreate($driver, $database, $type, $table)
    {
        if (!$this->driver->permissions()->canCreateItem($database, $type, $table)) {
            throw new ForbiddenRequestException('Create item is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
        $this->template->type = $this->type = $type;
        $this->table = $table;
    }

    public function actionEdit($driver, $database, $type, $table, $item)
    {
        if (!$this->driver->permissions()->canEditItem($database, $type, $table, $item)) {
            throw new ForbiddenRequestException('Edit item is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
        $this->template->type = $this->type = $type;
        $this->table = $table;
        $this->item = $item;
    }

    public function handleDelete($driver, $database, $type, $table, $item)
    {
        if (!$this->driver->permissions()->canDeleteItem($database, $type, $table, $item)) {
            throw new ForbiddenRequestException('Delete item is not allowed');
        }
        if ($this->driver->dataManager()->deleteItem($type, $table, $item)) {
            $this->flashMessage('Item was successfully deleted', 'success');
        } else {
            $this->flashMessage('Item was not deleted', 'danger');
        }
        $this->redirect('this', $driver, $database, $type, $table);
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->translator, $this->database);
    }

    protected function createComponentForm()
    {
        return new ItemForm($this->translator, $this->driver, $this->database, $this->type, $this->table, $this->item);
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

    protected function createComponentPaginator()
    {
        return new VisualPaginator();
    }

    protected function createComponentTablesSideBar()
    {
        return new TablesSideBarControl($this->driver, $this->database, $this->table);
    }
}
