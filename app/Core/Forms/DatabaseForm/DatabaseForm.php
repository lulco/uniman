<?php

namespace UniMan\Core\Forms\DatabaseForm;

use UniMan\Core\Driver\DriverInterface;
use InvalidArgumentException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class DatabaseForm extends Control
{
    private $translator;

    private $driver;

    private $database;

    public function __construct(ITranslator $translator, DriverInterface $driver, $database = null)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->driver = $driver;
        $this->database = $database;
    }

    public function render()
    {
        echo $this['form'];
    }

    protected function createComponentForm()
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->setRenderer(new BootstrapVerticalRenderer());

        $databaseForm = $this->driver->formManager()->databaseForm($this->database);
        if (!$databaseForm) {
            throw new InvalidArgumentException('Item form not set');
        }
        $databaseForm->addFieldsToForm($form);
        $form->addSubmit('save', 'Save');
        $form->onSuccess[] = [$databaseForm, 'submit'];
        $form->onSuccess[] = function () {
            $this->presenter->redirect('Database:default', $this->driver->type());
        };
        return $form;
    }
}
