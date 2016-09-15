<?php

namespace Adminerng\Presenters;

use Adminerng\Core\DriverInterface;
use Adminerng\Core\DriverStorage;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

abstract class AbstractBasePresenter extends Presenter
{
    /** @var string @persistent */
    public $locale;

    /** @var DriverStorage @inject */
    public $driverStorage;

    /** @var ITranslator @inject */
    public $translator;

    /** @var DriverInterface */
    protected $driver;
}
