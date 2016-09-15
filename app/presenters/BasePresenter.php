<?php

namespace Adminerng\Presenters;

use Adminerng\Core\Exception\ConnectException;
use Nette\Application\BadRequestException;

abstract class BasePresenter extends AbstractBasePresenter
{
    protected function startup()
    {
        parent::startup();
        $drivers = $this->driverStorage->getDrivers();
        $actualDriver = isset($this->params['driver']) ? $this->params['driver'] : current(array_keys($drivers));

        $section = $this->getSession('adminerng');
        $settings = $section->$actualDriver;
        if (!$settings) {
            $this->redirect('Homepage:default', $actualDriver);
        }

        $credentials = json_decode(base64_decode($settings), true);
        if (!$credentials) {
            $this->redirect('Homepage:default', $actualDriver);
        }
        $this->driver = $this->driverStorage->getDriver($actualDriver);
        if (!$this->driver) {
            throw new BadRequestException('Driver "' . $actualDriver . '" not found');
        }

        // presunut mergovanie credentials a default credentials z formu az sem aby sa default cred. neukladali do session (vypisuju sa potom pri neuspesnom prihlaseni vo forme a to sa mi nepaci)
        try {
            $this->driver->connect($credentials);
        } catch (ConnectException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('Homepage:default', $actualDriver);
        }
        $this->template->actualDriver = $this->driver;
    }
}
