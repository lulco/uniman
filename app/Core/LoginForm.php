<?php

namespace Adminerng\Core;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class LoginForm extends Control
{
    private $translator;

    private $driverStorage;

    private $driver;

    public function __construct(ITranslator $translator, DriverStorage $driverStorage, $driver)
    {
        parent::__construct();
        $this->translator = $translator;
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

            $section = $this->presenter->getSession('adminerng');
            $settings = $section->{$this->driver};
            $credentials = $settings ? json_decode(base64_decode($settings), true) : [];
            $form->setDefaults($credentials);

            $form->addSubmit('connect', 'core.form.connect');
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

        if (isset($values['database']) && $values['database']) {
            $this->presenter->redirect('List:tables', $values['driver'], $values['database']);
        }
        $this->presenter->redirect('List:databases', $values['driver']);
    }
}
