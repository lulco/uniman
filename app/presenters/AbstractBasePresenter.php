<?php

namespace Adminerng\Presenters;

use Adminerng\Components\Breadcrumb\BreadcrumbControl;
use Adminerng\Core\Credentials\CredentialsStorageInterface;
use Adminerng\Core\DriverInterface;
use Adminerng\Core\DriverStorage;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

abstract class AbstractBasePresenter extends Presenter
{
    /** @var string @persistent */
    public $locale = 'en';

    /** @var CredentialsStorageInterface @inject */
    public $credentialsStorage;

    /** @var DriverStorage @inject */
    public $driverStorage;

    /** @var ITranslator @inject */
    public $translator;

    /** @var DriverInterface */
    protected $driver;

    protected $database;

    protected $type;

    protected $table;

    protected $item;

    protected function startup()
    {
        parent::startup();
        $this->template->locale = $this->locale;
    }

    protected function createComponentBreadcrumb()
    {
        return new BreadcrumbControl(
            $this->driver instanceof DriverInterface ? $this->driver->type() : $this->driver,
            $this->database,
            $this->type,
            $this->table,
            $this->item
        );
    }
}