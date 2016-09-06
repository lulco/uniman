<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class ItemPresenter extends BasePresenter
{
    private $database;

    public function actionCreate($driver, $database, $type, $table)
    {
        if (!$this->driver->permissions()->canCreateItem($database, $type, $table)) {
            throw new ForbiddenRequestException('Create item is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
    }

    public function actionEdit($driver, $database, $type, $table, $item)
    {
        if (!$this->driver->permissions()->canEditItem($database, $type, $table, $item)) {
            throw new ForbiddenRequestException('Edit item is not allowed');
        }
        $this->template->driver = $driver;
        $this->database = $database;
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->database);
    }

    protected function createComponentForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapVerticalRenderer());
        $form->addText('x', 'y');
        $form->addSubmit('save', 'Save');
        return $form;
    }
}
