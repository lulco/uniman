<?php

namespace Adminerng\Core;

use Adminerng\Core\Credentials\CredentialsStorageInterface;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

class LoginForm extends Control
{
    private $translator;

    private $driverStorage;

    private $credentialsStorage;

    private $driver;

    private $locale;

    public function __construct(ITranslator $translator, DriverStorage $driverStorage, CredentialsStorageInterface $credentialsStorage, $driver, $locale)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->driverStorage = $driverStorage;
        $this->credentialsStorage = $credentialsStorage;
        $this->driver = $driver;
        $this->locale = $locale;
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
            ->setAttribute('onchange', 'window.location = "' . $this->presenter->link('this', ['driver' => null, 'locale' => $this->locale]) . ($this->locale == 'en' ? '?' : '&') . 'driver=" + this.value;')
            ->setDefaultValue($this->driver)
            ->setOption('description', Html::el('span')->addText($this->translator->translate('core.missing_driver') . ' ')->addHtml(Html::el('a')->setAttribute('href', $this->presenter->link('Default:check'))->setAttribute('target', '_blank')->setText($this->translator->translate('core.check_why'))));
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
