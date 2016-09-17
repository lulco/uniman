<?php

namespace Adminerng\Core;

use Adminerng\Core\Credentials\CredentialsStorageInterface;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class LoginForm extends Control
{
    private $translator;

    private $driverStorage;

    private $credentialsStorage;

    private $driver;

    public function __construct(ITranslator $translator, DriverStorage $driverStorage, CredentialsStorageInterface $credentialsStorage, $driver)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->driverStorage = $driverStorage;
        $this->credentialsStorage = $credentialsStorage;
        $this->driver = $driver;
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
        $form->setMethod('get');

        $driversList = [];
        foreach ($this->driverStorage->getDrivers() as $driver) {
            if ($driver->check()) {
                $driversList[$driver->type()] = $driver->name();
            }
        }
        $form->addSelect('driver', 'core.form.driver', $driversList)
            ->setAttribute('onchange', 'window.location = "?driver=" + this.value;')
            ->setDefaultValue($this->driver);
        if ($this->driver) {
            $driver = $this->driverStorage->getDriver($this->driver);
            $driver->addFormFields($form);

            $credentials = $this->credentialsStorage->getCredentials($this->driver);
            $form->setDefaults($credentials);

            $form->addSubmit('connect', 'core.form.connect');
            $form->onSuccess[] = [$this, 'connect'];
        }

        return $form;
    }

    public function connect(Form $form, ArrayHash $values)
    {
        $values = (array)$values;
        $this->credentialsStorage->setCredentials($this->driver, $values);
        if (isset($values['database']) && $values['database']) {
            $this->presenter->redirect('List:tables', $this->driver, $values['database']);
        }
        $this->presenter->redirect('List:databases', $this->driver);
    }
}
