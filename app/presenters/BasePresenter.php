<?php

namespace Adminerng\Presenters;

use Adminerng\Components\DatabaseSelect\DatabaseSelectControl;
use Adminerng\Core\Exception\ConnectException;
use Nette\Application\BadRequestException;

abstract class BasePresenter extends AbstractBasePresenter
{
    protected function startup()
    {
        parent::startup();
        $drivers = $this->driverStorage->getDrivers();
        $actualDriver = isset($this->params['driver']) ? $this->params['driver'] : current(array_keys($drivers));

        $this->template->driver = $actualDriver;
        $this->driver = $this->driverStorage->getDriver($actualDriver);
        if (!$this->driver) {
            throw new BadRequestException('Driver "' . $actualDriver . '" not found');
        }

        $credentials = $this->credentialsStorage->getCredentials($actualDriver);
        if (!$credentials) {
            $this->redirect('Default:default', $actualDriver);
        }

        foreach ($this->driver->defaultCredentials() as $key => $defaultCredential) {
            $credentials[$key] = $credentials[$key] ?: $defaultCredential;
        }
        try {
            $this->driver->connect($credentials);
            if (isset($this->params['database'])) {
                $this->driver->dataManager()->selectDatabase($this->params['database']);
            }
        } catch (ConnectException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('Default:default', $actualDriver);
        }
        $this->template->actualDriver = $this->driver;
    }

    protected function createComponentDatabaseSelect()
    {
        return new DatabaseSelectControl($this->driver, $this->translator, $this->database);
    }
}
