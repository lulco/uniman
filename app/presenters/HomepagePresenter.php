<?php

namespace Adminerng\Presenters;

use Adminerng\Core\DriverStorage;
use Adminerng\Core\LoginForm;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{
    /** @var DriverStorage @inject */
    public $driverStorage;

    private $driver;

    public function actionDefault($driver = null)
    {
        $this->driver = $driver;// ?: current(array_keys($this->driverStorage->getDrivers()));
        $section = $this->getSession('adminerng');
        $settings = $section->{$this->driver};
        $credentials = json_decode(base64_decode($settings), true);
        if ($credentials) {
            $this->redirect('List:databases', $this->driver);
        }
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
        return new LoginForm($this->driverStorage, $this->driver);
    }
}
