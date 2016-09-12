<?php

namespace Adminerng\Components\DatabaseSelect;

use Adminerng\Core\DriverInterface;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class DatabaseSelectControl extends Control
{
    private $driver;

    private $database;
    
    public function __construct(DriverInterface $driver, $database = null)
    {
        parent::__construct();
        $this->driver = $driver;
        $this->database = $database;
    }

    public function render()
    {
        $this->template->driver = $this->driver;
        $this->template->actualDatabase = $this->database;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
    
    protected function createComponentSelect()
    {
        $databases = array_keys($this->driver->dataManager()->databases());
        $form = new Form();
        $form->setRenderer(new BootstrapVerticalRenderer());
        $form->addSelect('database', ucfirst($this->driver->databaseTitle()), array_combine($databases, $databases))
            ->setPrompt('Select ' . $this->driver->databaseTitle())
            ->setAttribute('onchange', 'window.location = \'' . $this->presenter->link('List:tables', $this->driver->type()) . '&database=\' + this.value')
            ->setDefaultValue($this->database);
        return $form;
    }
}
