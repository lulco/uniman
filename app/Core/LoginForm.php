<?php

namespace Adminerng\Core;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class LoginForm extends Control
{
    private $driverStorage;
    
    private $driver;
    
    public function __construct(DriverStorage $driverStorage, $driver)
    {
        parent::__construct();
        $this->driverStorage = $driverStorage;
        $this->driver = $driver;
    }

    public function render()
    {
        echo $this['form'];
    }
    
    protected function createComponentForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapVerticalRenderer);
        $form->setMethod('get');

        $driversList = [];
        foreach ($this->driverStorage->getDrivers() as $key => $driver) {
            if ($driver->check()) {
                $driversList[$key] = $driver->name();
            }
        }
        $form->addSelect('driver', 'Driver', $driversList)
            ->setAttribute('onchange', 'window.location = "?driver=" + this.value;')
            ->setDefaultValue($this->driver);
        if ($this->driver) {
            $driver = $this->driverStorage->getDriver($this->driver);
            $driver->addFormFields($form);
            $form->addSubmit('connect', 'Connect');
            $form->onSuccess[] = [$this, 'connect'];
        }
        return $form;
    }

    public function connect(Form $form, ArrayHash $values)
    {
        $driver = $this->driverStorage->getDriver($this->driver);
        $defaultCredentials = $driver->defaultCredentials();
        $values = (array)$values;
        foreach ($defaultCredentials as $key => $defaultCredential) {
            $values[$key] = $values[$key] ?: $defaultCredential;
        }
        $section = $this->presenter->getSession('adminerng');
        $section->{$values['driver']} = base64_encode(json_encode($values));
        $this->presenter->redirect('List:databases', $values['driver']);
    }
}
