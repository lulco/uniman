<?php

namespace Adminerng\Core\Forms;

use Adminerng\Core\Driver\DriverInterface;
use InvalidArgumentException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class TableForm extends Control
{
    private $translator;

    private $driver;

    private $database;

    private $type;

    private $table;

    public function __construct(ITranslator $translator, DriverInterface $driver, $database, $type, $table = null)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->driver = $driver;
        $this->database = $database;
        $this->type = $type;
        $this->table = $table;
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

        $tableForm = $this->driver->formManager()->tableForm($this->database, $this->type, $this->table);
        if (!$tableForm) {
            throw new InvalidArgumentException('Item form not set');
        }
        $tableForm->addFieldsToForm($form);
        $form->addSubmit('save', 'Save');
        $form->onSuccess[] = [$tableForm, 'submit'];
        $form->onSuccess[] = function () {
            $this->presenter->redirect('Table:default', $this->driver->type(), $this->database);
        };
        return $form;
    }
}
