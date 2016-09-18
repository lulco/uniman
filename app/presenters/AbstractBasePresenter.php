<?php

namespace Adminerng\Presenters;

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

    protected function startup()
    {
        parent::startup();
        $this->template->locale = $this->locale;
    }
}
