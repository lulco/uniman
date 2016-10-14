<?php

namespace Adminerng\Presenters;

use Adminerng\Core\LoginForm;

class HomepagePresenter extends AbstractBasePresenter
{
    public function actionDefault($driver = null)
    {
        $actualDriver = $driver ?: current(array_keys($this->driverStorage->getDrivers()));
        $this->driver = $this->driverStorage->getDriver($actualDriver);
        $this->template->driver = $actualDriver;
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
        return new LoginForm($this->translator, $this->driverStorage, $this->credentialsStorage, $this->driver->type(), $this->locale);
    }
}
