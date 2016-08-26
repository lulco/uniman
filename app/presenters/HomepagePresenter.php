<?php

namespace Adminerng\Presenters;

use Adminerng\Core\DriverStorage;
use Adminerng\Core\LoginForm;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

class HomepagePresenter extends Presenter
{
    /** @var DriverStorage @inject */
    public $driverStorage;
    
    /** @var ITranslator @inject */
    public $translator;

    private $driver;

    public function actionDefault($driver = null)
    {
        $this->driver = $driver ?: current(array_keys($this->driverStorage->getDrivers()));
    }

    public function actionLogout($driver = null)
    {
        $section = $this->getSession('adminerng');
        if ($driver) {
            unset($section->{$driver});
        } else {
            $section->remove();
        }
        $this->redirect('Homepage:default', $driver);
    }

    protected function createComponentLoginForm()
    {
        return new LoginForm($this->translator, $this->driverStorage, $this->driver);
    }
}
