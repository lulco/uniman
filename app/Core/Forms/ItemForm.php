<?php

namespace Adminerng\Core\Forms;

use Adminerng\Core\DriverInterface;
use InvalidArgumentException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class ItemForm extends Control
{
    private $translator;

    private $driver;

    private $database;

    private $type;

    private $table;

    private $item;

    public function __construct(ITranslator $translator, DriverInterface $driver, $database, $type, $table, $item = null)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->driver = $driver;
        $this->database = $database;
        $this->type = $type;
        $this->table = $table;
        $this->item = $item;
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

        $itemForm = $this->driver->itemForm($this->database, $this->type, $this->table, $this->item);
        if (!$itemForm) {
            throw new InvalidArgumentException('Item form not set');
        }
        $itemForm->addFieldsToForm($form);
        $form->addSubmit('save', 'Save');        
        $form->onSuccess[] = [$itemForm, 'submit'];
        $form->onSuccess[] = function () {
            $this->presenter->redirect('List:items', $this->driver->type(), $this->database, $this->type, $this->table);
        };
        return $form;
    }
}
