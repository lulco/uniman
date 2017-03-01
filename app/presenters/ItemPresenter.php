<?php

namespace UniMan\Presenters;

use UniMan\Components\DatabaseSelect\TablesSideBarControl;
use UniMan\Core\Forms\FilterForm\FilterForm;
use UniMan\Core\Forms\ItemForm;
use App\Component\VisualPaginator;
use Nette\Application\ForbiddenRequestException;

class ItemPresenter extends BasePresenter
{
    private $onPage;

    private $sorting = [];

    private $filter = [];

    private $columns = [];

    public function actionDefault($driver, $database, $type, $table, $page = 1, $onPage = FilterForm::DEFAULT_ON_PAGE, array $filter = [], array $sorting = [])
    {
        ksort($sorting);
        $this->database = $database;
        $this->type = $type;
        $this->table = $table;
        $this->onPage = $onPage;
        $this->sorting = $sorting;
        $this->filter = $filter;
        $this->columns = $this->driver->headerManager()->itemsHeaders($type, $table);
    }

    public function renderDefault($driver, $database, $type, $table, $page = 1, $onPage = FilterForm::DEFAULT_ON_PAGE, array $filter = [], array $sorting = [])
    {
        $this->template->driver = $driver;
        $this->template->database = $database;
        $this->template->type = $type;
        $this->template->table = $table;
        $this->template->sorting = $sorting;

        $itemsCount = $this->driver->dataManager()->itemsCount($type, $table, $filter);
        $this->template->itemsCount = $itemsCount;
        $this->template->items = $this->driver->dataManager()->items($type, $table, $page, $onPage, $filter, $sorting);

        $this->template->columns = $this->columns;

        $visualPaginator = $this['paginator'];
        $paginator = $visualPaginator->getPaginator();
        $paginator->setItemCount($itemsCount);
        $paginator->setItemsPerPage($onPage);
        $paginator->page = $page;

        foreach ($this->driver->dataManager()->getMessages() as $message => $type) {
            $type = $type ?: 'info';
            $this->flashMessage($message, $type);
        }
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

    protected function createComponentForm()
    {
        return new ItemForm($this->translator, $this->driver, $this->database, $this->type, $this->table, $this->item);
    }

    protected function createComponentFilterForm()
    {
        $form = $this->driver->formManager()->filterForm($this->translator, $this->columns, $this->filter, $this->sorting, $this->onPage);
        $form->doRedirect[] = function ($onPage, $filter, $sorting) {
            $this->redirect('Item:default', $this->driver->type(), $this->database, $this->type, $this->table, 1, $onPage, $filter, $sorting);
        };
        $form->doReset[] = function () {
            $this->redirect('Item:default', $this->driver->type(), $this->database, $this->type, $this->table);
        };
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
