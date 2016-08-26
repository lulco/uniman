<?php

namespace Adminerng\Presenters;

use Adminerng\Core\DriverInterface;
use Adminerng\Core\DriverStorage;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

abstract class BasePresenter extends Presenter
{
    /** @var DriverStorage @inject */
    public $driverStorage;

    /** @var DriverInterface */
    protected $driver;

    /** @var ITranslator @inject */
    public $translator;

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
        $this->driver->connect($credentials);
        $this->template->actualDriver = $actualDriver;
    }
}
