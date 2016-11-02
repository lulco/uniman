<?php

namespace Adminerng\Presenters;

use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class CommandPresenter extends BasePresenter
{
    private $commands;

    private $results = [];

    public function renderDefault($driver, $database = null, $commands = null)
    {
        if (!$this->driver->permissions()->canExecuteCommands()) {
            throw new ForbiddenRequestException('Executing commands is not available');
        }
        $this->database = $database;
        $this->commands = $commands;
        $this->template->results = $this->results;
    }

    protected function createComponentForm()
    {
        $form = new Form();
        $form->setMethod('get');
        $form->setRenderer(new BootstrapVerticalRenderer());
        $form->addTextArea('commands', 'Commands', null, 10)
            ->setDefaultValue($this->commands)
            ->setRequired('%label is required');
        $form->addSubmit('submit', 'Execute');
        $form->onSuccess[] = [$this, 'formSuccess'];
        return $form;
    }

    public function formSuccess(Form $form, ArrayHash $values)
    {
        $this->results = $this->driver->dataManager()->execute($values['commands']);
    }
}
