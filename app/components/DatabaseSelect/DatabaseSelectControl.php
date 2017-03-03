<?php

namespace UniMan\Components\DatabaseSelect;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;
use UniMan\Core\Driver\DriverInterface;

class DatabaseSelectControl extends Control
{
    private $driver;

    private $translator;

    private $database;

    public function __construct(DriverInterface $driver, ITranslator $translator, $database = null)
    {
        parent::__construct();
        $this->driver = $driver;
        $this->translator = $translator;
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
        $databases = $this->driver->dataManager()->databaseNames();
        ksort($databases);
        $form = new Form();
        $form->setRenderer(new BootstrapVerticalRenderer());
        $form->addSelect('database', $this->translator->translate($this->driver->type() . '.database_select_control.database.label'), $databases)
            ->setPrompt($this->translator->translate($this->driver->type() . '.database_select_control.database.prompt'))
            ->setAttribute('onchange', 'window.location = \'' . $this->presenter->link('Table:default', $this->driver->type()) . '&database=\' + this.value')
            ->setDefaultValue($this->database);
        return $form;
    }
}
